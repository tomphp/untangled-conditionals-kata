<?php declare(strict_types=1);

namespace UntangledConditionals\Dependencies;

final class Project
{
    /**
     * @var TestStatus
     */
    private $testStatus;

    /**
     * @var bool
     */
    private $deploysSuccessfully;

    public static function builder(): ProjectBuilder
    {
        return new ProjectBuilder();
    }

    public function __construct(bool $deploysSuccessfully, TestStatus $testStatus)
    {

        $this->deploysSuccessfully = $deploysSuccessfully;
        $this->testStatus = $testStatus;
    }

    public function hasTests(): bool
    {
        return $this->testStatus != TestStatus::noTests();
    }

    public function runTestsResult() :bool {
        return $this->testStatus == TestStatus::passingTests();
    }

    public function deploysSuccessfully() :bool {
        return $this->deploysSuccessfully;
    }
}