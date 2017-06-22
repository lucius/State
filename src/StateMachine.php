<?php
namespace Particle\State;

use Particle\State\Exception\NoSuchTransition;
use Particle\State\Exception\TransitionNotAllowed;

class StateMachine
{
    /** @var StateCollection */
    protected $states;

    /** @var TransitionCollection */
    protected $transitions;

    /** @var State */
    protected $currentState;

    /** @var Emitter */
    protected $emitter;

    /**
     * @param State $initialState
     * @param StateCollection $states
     */
    private function __construct(State $initialState, StateCollection $states)
    {
        $this->states = $states;
        $this->currentState = $initialState;
        $this->transitions = new TransitionCollection();
    }

    /**
     * @return State
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * @param State $compare
     * @return bool
     */
    public function isInState(State $compare)
    {
        return $this->currentState->equals($compare);
    }

    /**
     * @param string $transitionName
     * @return bool
     * @throws NoSuchTransition
     */
    public function canApplyTransition($transitionName)
    {
        return $this->transitions->get($transitionName)->hasStartState($this->currentState);
    }

    /**
     * @param State $initialState
     * @param StateCollection $states
     * @return StateMachine
     */
    public static function withStates(State $initialState, StateCollection $states)
    {
        return new self($initialState, $states);
    }

    /**
     * @param Transition $transition
     */
    public function addTransition(Transition $transition)
    {
        $transition->addToCollection($this->transitions);
    }

    /**
     * @param string $transitionName
     * @return bool
     * @throws NoSuchTransition
     * @throws TransitionNotAllowed
     */
    public function transition($transitionName, $data = [])
    {
        if (!$this->canApplyTransition($transitionName)) {
            throw TransitionNotAllowed::forTransitionName($transitionName);
        }

        $transition = $this->transitions->get($transitionName);

        $setState = function (State $state) {
            $this->currentState = $state;
        };

        return $transition->apply($setState, $data);
    }

    /**
     * @param string $event
     * @param callable $listener
     */
    public function addListener($event, callable $listener)
    {
        $this->getEmitter()->addListener($event, $listener);
    }

}
