<?php

require_once 'aoc5-common.php';

class Part2 extends PageSorter
{
    /**
     * @return int
     */
    public function answer(): int
    {
        $sum = 0;

        foreach ($this->getInvalidUpdateBreakdown()['invalid'] as $update) {
            // fix the update
            $fixed = $this->repairUpdate($update);
            $sum += $fixed[floor(count($fixed) / 2)];
        }

        return $sum;
    }

    /**
     * @param array $update
     *
     * @return array
     */
    private function repairUpdate(array $update): array
    {
        $isValid = false;

        // brute force - keep re-validating the update and performing "fixes" until no further violations occur
        while (!$isValid) {
            foreach ($this->rules as $rulePageNumber => $rulePrequisitePages) {
                try {
                    $this->validateRule($update, $rulePageNumber, $rulePrequisitePages);
                } catch (RuleViolationException $ex) {
                    /**
                     * Update: [97,13,75,29,47]
                     *
                     * Update indexes:
                     *  0   1   2   3   4
                     * ---------------------
                     *  97  13  75  29  47
                     *
                     * Rule: 75|13 - Page 75 must come BEFORE page 13
                     *
                     * Exception:
                     *      pageIndex: 2    (index of 75)
                     *      prequisiteIndex: 1  (index of 13)
                     *
                     * Fixed: [97, 75, 13, 29, 47]
                     *              <->
                     *
                     * When this rule violation occurs - it states that "75 is not before 13", and provides the indexes
                     * of both the page which has been violated and the pre-requisite position it failed on
                     *
                     * We can now use this information to "fix" the violation by swapping the pages around
                     */
                    $tmp = $update[$ex->getPageIndex()];
                    $update[$ex->getPageIndex()] = $update[$ex->getPrerequisiteIndex()];
                    $update[$ex->getPrerequisiteIndex()] = $tmp;
                    continue 2;
                }
            }

            $isValid = true;
        }

        return $update;
    }
}

echo (new Part2)->answer() . "\n";
