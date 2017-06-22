<?php
namespace Particle\State;

class State
{
    /** @var string */
    private $state;

    /**
     * @var callback function
     */
    private $onEnterCallback;
    /**
     * @var callback function
     */
    private $onLeaveCallback;

    /**
     * @param string $state
     */
    private function __construct($state, $onEnterCallback = null, $onLeaveCallback = null)
    {
        $this->state = $state;

        $this->onEnterCallback = $onEnterCallback;
        $this->onLeaveCallback = $onLeaveCallback;
    }

    /**
     * @param string $state
     * @return State
     */
    public static function withName($state)
    {
        return new self($state);
    }

    /**
     * @param State $state
     * @return bool
     */
    public function equals(State $state)
    {
        return $state->state === $this->state;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->state;
    }

    public function enterState()
    {
        $this->callCallback($this->onEnterCallback);
    }

    public function leaveState()
    {
        $this->callCallback($this->onLeaveCallback);
    }

    private function callCallback($cb) {
        if(!is_null($cb)) {
            return $cb();
        }

        return true;
    }
}
