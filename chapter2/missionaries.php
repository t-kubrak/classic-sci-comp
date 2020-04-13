<?php

const MAX_NUM = 3;

class MCState
{
    /**
     * @var int
     */
    private int $wm;
    /**
     * @var int
     */
    private int $wc;
    /**
     * @var bool
     */
    private bool $isWestBoat;
    /**
     * @var int
     */
    private int $em;
    /**
     * @var int
     */
    private int $ec;

    public function __construct(int $missionaries, int $cannibals, bool $boat)
    {
        $this->wm = $missionaries; // west bank missionaries
        $this->wc = $cannibals; // west bank cannibals
        $this->em = MAX_NUM - $this->wm; // east bank missionaries
        $this->ec = MAX_NUM - $this->wc; // east bank cannibals
        $this->isWestBoat = $boat;
    }

    public function __toString(): string
    {
        $boat = $this->isWestBoat ? 'west' : 'east';

        return "On the west bank there are {$this->wm} missionaries and 
            {$this->wc} cannibals.\n
            On the east bank there are {$this->em} missionaries and
             {$this->ec} cannibals.\n
             The boat is on the {$boat} bank.";
    }

    public function goalTest(): bool
    {
        return $this->isLegal()
            && $this->em == MAX_NUM
            && $this->ec == MAX_NUM;
    }

    private function isLegal()
    {
        if ($this->wm < $this->wc && $this->wm > 0) {
            return false;
        }

        if ($this->em < $this->ec && $this->em > 0) {
            return false;
        }

        return true;
    }

    public function successors(): array
    {
        $successors = new ArrayObject();

        if ($this->isWestBoat) {
            if ($this->wm > 1) {
                $successors->append(new MCState($this->wm - 2, $this->wc, !$this->isWestBoat));
            }
            if ($this->wm > 0) {
                $successors->append(new MCState($this->wm - 1, $this->wc, !$this->isWestBoat));
            }
            if ($this->wc > 1) {
                $successors->append(new MCState($this->wm, $this->wc - 2, !$this->isWestBoat));
            }
            if ($this->wc > 0) {
                $successors->append(new MCState($this->wm, $this->wc - 1, !$this->isWestBoat));
            }
            if ($this->wc > 0 && $this->wm > 0) {
                $successors->append(new MCState($this->wm - 1, $this->wc - 1, !$this->isWestBoat));
            }
        } else {
            if ($this->em > 1) {
                $successors->append(new MCState($this->wm + 2, $this->wc, !$this->isWestBoat));
            }
            if ($this->em > 0) {
                $successors->append(new MCState($this->wm + 1, $this->wc, !$this->isWestBoat));
            }
            if ($this->ec > 1) {
                $successors->append(new MCState($this->wm, $this->wc + 2, !$this->isWestBoat));
            }
            if ($this->ec > 0) {
                $successors->append(new MCState($this->wm, $this->wc + 1, !$this->isWestBoat));
            }
            if ($this->ec > 0 && $this->em > 0) {
                $successors->append(new MCState($this->wm + 1, $this->wc + 1, !$this->isWestBoat));
            }
        }

        return array_filter($successors, function (MCState $state) {
            return $state->isLegal();
        });
    }
}