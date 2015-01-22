<?php
namespace Vda\Util;

class FiniteStateResolver
{
    private $transistionTable;
    private $reverseTransitionTable;

    public function __construct(array $transitionTable)
    {
        $this->transistionTable = $transitionTable;
    }

    public function catSwitch($from, $to)
    {
        return in_array($to, $this->availableTo($from));
    }

    public function availableTo($state)
    {
        if (!array_key_exists($state, $this->transistionTable)) {
            throw new \UnexpectedValueException(
                "Requested state ({$state}) is not present in the transition table"
            );
        }

        return (array) $this->transistionTable[$state];
    }

    public function availableFrom($state)
    {
        if (!array_key_exists($state, $this->transistionTable)) {
            throw new \UnexpectedValueException(
                "Requested state ({$state}) is not present in the transition table"
            );
        }

        if (is_null($this->reverseTransitionTable)) {
            $this->buildReverseTransitionTable();
        }

        return isset($this->reverseTransitionTable[$state])
            ? $this->reverseTransitionTable[$state]
            : array();
    }

    private function buildReverseTransitionTable()
    {
        $this->reverseTransitionTable = array();

        foreach ($this->transistionTable as $from => $toList) {
            foreach ((array) $toList as $to) {
                $this->reverseTransitionTable[$to][] = $from;
            }
        }
    }
}
