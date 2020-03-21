<?php


namespace UntangledConditionals\Dependencies;


class ProjectBuilder
{
    /**
     * @var TestStatus
     */
    private $testStatus;

    /**
     * @var bool
     */
    private $deploysSuccessfully = false;

    public function setTestStatus(TestStatus $testStatus): self
    {
        $this->testStatus = $testStatus;
        return $this;
    }

    public function setDeploysSuccessfully(bool $value): self
    {
        $this->deploysSuccessfully = $value;
        return $this;
    }

    public function build(): Project
    {
        return new Project($this->deploysSuccessfully, $this->testStatus);
    }
}