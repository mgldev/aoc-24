<?php

class RuleViolationException extends Exception
{
    public function __construct(
        private readonly int $pageIndex,
        private readonly int $prerequisiteIndex
    )
    {
        parent::__construct('Rule violated');
    }

    public function getPageIndex(): int
    {
        return $this->pageIndex;
    }

    public function getPrerequisiteIndex(): int
    {
        return $this->prerequisiteIndex;
    }
}

class PageSorter
{
    protected array $rules = [];
    protected array $updates = [];

    public function __construct()
    {
        $this->initialiseRulesAndUpdates();
    }

    private function initialiseRulesAndUpdates(): void
    {
        $processingUpdates = false;
        $rules = [];
        $updates = [];

        foreach (file(__DIR__ . '/day5.txt', FILE_IGNORE_NEW_LINES & FILE_SKIP_EMPTY_LINES) as $line) {
            if (!strpos($line, '|')) $processingUpdates = true;

            if (!$processingUpdates) {
                [$page, $dependency] = array_map('intval', explode('|', $line));
                if (!isset($rules[$page])) {
                    $rules[$page] = [];
                }
                $rules[$page][] = $dependency;
            } else {
                $updates[] = array_map('intval', explode(',', $line));
            }
        }

        $this->rules = $rules;
        $this->updates = $updates;
    }

    /**
     * @return array[]
     */
    protected function getInvalidUpdateBreakdown(): array
    {
        $breakdown = [
            'valid' => [],
            'invalid' => []
        ];

        foreach ($this->updates as $update) {
            foreach ($this->rules as $rulePageNumber => $rulePrequisitePages) {
                try {
                    $this->validateRule($update, $rulePageNumber, $rulePrequisitePages);
                } catch (RuleViolationException) {
                    $breakdown['invalid'][] = $update;
                    continue 2;
                }
            }

            $breakdown['valid'][] = $update;
        }

        return $breakdown;
    }

    /**
     * @param array $pages
     * @param int $pageNumber
     * @param array $pagePrerequisites
     *
     * @return void
     *
     * @throws RuleViolationException
     */
    protected function validateRule(
        array $pages,
        int   $pageNumber,
        array $pagePrerequisites
    ): void
    {
        if (!in_array($pageNumber, $pages)) {
            return;
        }

        $pageIndex = array_search($pageNumber, $pages);

        foreach ($pagePrerequisites as $prerequisitePage) {
            $prerequisiteIndex = array_search($prerequisitePage, $pages);

            if ($prerequisiteIndex === false) continue;

            if ($pageIndex > $prerequisiteIndex) {
                throw new RuleViolationException($pageIndex, $prerequisiteIndex);
            }
        }
    }
}
