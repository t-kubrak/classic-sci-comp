<?php
include_once "generic_search.php";

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

        return "On the west bank there are {$this->wm} missionaries and {$this->wc} cannibals.\n"
                ."On the east bank there are {$this->em} missionaries and {$this->ec} cannibals.\n"
                ."The boat is on the {$boat} bank.\n";
    }

    public function isLegal()
    {
        if ($this->wm < $this->wc && $this->wm > 0) {
            return false;
        }

        if ($this->em < $this->ec && $this->em > 0) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function wm(): int
    {
        return $this->wm;
    }

    /**
     * @return int
     */
    public function wc(): int
    {
        return $this->wc;
    }

    /**
     * @return int
     */
    public function em(): int
    {
        return $this->em;
    }

    /**
     * @return int
     */
    public function ec(): int
    {
        return $this->ec;
    }

    /**
     * @return bool
     */
    public function isWestBoat(): bool
    {
        return $this->isWestBoat;
    }
}

function goalTest(MCState $state): bool
{
    return $state->isLegal()
        && $state->em() == MAX_NUM
        && $state->ec() == MAX_NUM;
}

function successors(MCState $state): array
{
    $successors = [];

    if ($state->isWestBoat()) {
        if ($state->wm() > 1) {
            $successors[] = new MCState($state->wm() - 2, $state->wc(), !$state->isWestBoat());
        }
        if ($state->wm() > 0) {
            $successors[] = new MCState($state->wm() - 1, $state->wc(), !$state->isWestBoat());
        }
        if ($state->wc() > 1) {
            $successors[] = new MCState($state->wm(), $state->wc() - 2, !$state->isWestBoat());
        }
        if ($state->wc() > 0) {
            $successors[] = new MCState($state->wm(), $state->wc() - 1, !$state->isWestBoat());
        }
        if ($state->wc() > 0 && $state->wm() > 0) {
            $successors[] = new MCState($state->wm() - 1, $state->wc() - 1, !$state->isWestBoat());
        }
    } else {
        if ($state->em() > 1) {
            $successors[] = new MCState($state->wm() + 2, $state->wc(), !$state->isWestBoat());
        }
        if ($state->em() > 0) {
            $successors[] = new MCState($state->wm() + 1, $state->wc(), !$state->isWestBoat());
        }
        if ($state->ec() > 1) {
            $successors[] = new MCState($state->wm(), $state->wc() + 2, !$state->isWestBoat());
        }
        if ($state->ec() > 0) {
            $successors[] = new MCState($state->wm(), $state->wc() + 1, !$state->isWestBoat());
        }
        if ($state->ec() > 0 && $state->em() > 0) {
            $successors[] = new MCState($state->wm() + 1, $state->wc() + 1, !$state->isWestBoat());
        }
    }

    return array_filter($successors, function (MCState $state) {
        return $state->isLegal();
    });
}

/**
 * @param MCState[]|array $path
 */
function displaySolution(array $path): void
{
    if (count($path) == 0) {
        return;
    }

    $oldState = $path[0];
    echo $oldState;
    $path = array_slice($path, 1);

    foreach ($path as $currentState) {
        if ($currentState->isWestBoat()) {
            printf("%d missionaries and %d cannibals moved from the east bank to the west bank.\n\n",
                    $oldState->em() - $currentState->em(),
                    $oldState->ec() - $currentState->ec()
            );
        } else {
            printf("%d missionaries and %d cannibals moved from the west bank to the east bank.\n\n",
                    $oldState->wm() - $currentState->wm(),
                    $oldState->wc() - $currentState->wc()
            );
        }

        echo $currentState;
        $oldState = $currentState;
    }
}

$initialState = new MCState(MAX_NUM, MAX_NUM, true);
$solution = bfs($initialState, 'goalTest', 'successors');

if (!$solution) {
    echo "No solution was found!";
} else {
    $path = nodeToPath($solution);
    displaySolution($path);
}