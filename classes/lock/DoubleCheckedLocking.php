<?php

namespace bandwidthThrottle\tokenBucket\lock;

/**
 * The double-checked locking pattern.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @link bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK Donations
 * @license WTFPL
 */
class DoubleCheckedLocking
{
    
    /**
     * @var Mutex The mutex.
     */
    private $mutex;
    
    /**
     * @var callable The check.
     */
    private $check;

    /**
     * Sets the mutex.
     *
     * @param Mutex $mutex The mutex.
     * @internal
     */
    public function __construct(Mutex $mutex)
    {
        $this->mutex = $mutex;
    }
    
    /**
     * Sets the check.
     *
     * @param callable $check The check.
     * @internal
     */
    public function setCheck(callable $check)
    {
        $this->check = $check;
    }
    
    /**
     * Executes a code only if a check is true.
     *
     * Both the check and the code execution are locked by a mutex.
     *
     * @param callable $code The locked code.
     *
     * @throws \Exception The execution block or the check threw an exception.
     */
    public function then(callable $code)
    {
        if (!call_user_func($this->check)) {
            return;
        }
        $this->mutex->synchronized(function () use ($code) {
            if (call_user_func($this->check)) {
                call_user_func($code);
            }
        });
    }
}
