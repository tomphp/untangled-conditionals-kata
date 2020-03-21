<?php


namespace UntangledConditionals;

use PHPUnit\Framework\TestCase;
use UntangledConditionals\Dependencies\CapturingLogger;
use UntangledConditionals\Dependencies\Config;
use UntangledConditionals\Dependencies\Emailer;
use UntangledConditionals\Dependencies\Logger;
use UntangledConditionals\Dependencies\Project;
use UntangledConditionals\Dependencies\ProjectBuilder;
use UntangledConditionals\Dependencies\TestStatus;

final class PipelineTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Emailer
     */
    private $emailer;

    /**
     * @var CapturingLogger
     */
    private $log;

    /**
     * @var Pipeline
     */
    private $pipeline;

    public function setUp(): void
    {
        $this->config = $this->createStub(Config::class);
        $this->emailer = $this->createMock(Emailer::class);
        $this->log = new CapturingLogger();
        $this->pipeline = new Pipeline($this->config, $this->emailer, $this->log);
    }

    public function test_project_with_tests_that_deploys_successfully_with_email_notification(): void
    {
        $this->config->method('sendEmailSummary')->willReturn(true);

        $project = Project::builder()
            ->setTestStatus(TestStatus::passingTests())
            ->setDeploysSuccessfully(true)
            ->build();

        $this->emailer
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo("Deployment completed successfully"));

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: Tests passed",
            "INFO: Deployment successful",
            "INFO: Sending email"
        ], $this->log->getLoggedLines());
    }


    public function test_project_with_tests_that_deploys_successfully_without_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(false);

        $project = Project::builder()
            ->setTestStatus(TestStatus::passingTests())
            ->setDeploysSuccessfully(true)
            ->build();

        $this->emailer
            ->expects($this->never())
            ->method('send');

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: Tests passed",
            "INFO: Deployment successful",
            "INFO: Email disabled"
        ], $this->log->getLoggedLines());
    }


    public function test_project_without_tests_that_deploys_successfully_with_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(true);

        $project = Project::builder()
            ->setTestStatus(TestStatus::noTests())
            ->setDeploysSuccessfully(true)
            ->build();

        $this->emailer
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo("Deployment completed successfully"));

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: No tests",
            "INFO: Deployment successful",
            "INFO: Sending email"
        ], $this->log->getLoggedLines());
    }


    public function test_project_without_tests_that_deploys_successfully_without_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(false);

        $project = Project::builder()
            ->setTestStatus(TestStatus::noTests())
            ->setDeploysSuccessfully(true)
            ->build();

        $this->emailer
            ->expects($this->never())
            ->method('send');

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: No tests",
            "INFO: Deployment successful",
            "INFO: Email disabled"
        ], $this->log->getLoggedLines());
    }


    public function test_project_with_tests_that_fail_with_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(true);

        $project = Project::builder()
            ->setTestStatus(TestStatus::failingTests())
            ->build();

        $this->emailer
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo("Tests failed"));

        $this->pipeline->run($project);

        $this->assertEquals([
            "ERROR: Tests failed",
            "INFO: Sending email"
        ], $this->log->getLoggedLines());
    }


    public function test_project_with_tests_that_fail_without_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(false);

        $project = Project::builder()
            ->setTestStatus(TestStatus::failingTests())
            ->build();

        $this->emailer
            ->expects($this->never())
            ->method('send');

        $this->pipeline->run($project);

        $this->assertEquals([
            "ERROR: Tests failed",
            "INFO: Email disabled"
        ], $this->log->getLoggedLines());
    }


    public function test_project_with_tests_and_failing_build_with_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(true);

        $project = Project::builder()
            ->setTestStatus(TestStatus::passingTests())
            ->setDeploysSuccessfully(false)
            ->build();

        $this->emailer
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo("Deployment failed"));

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: Tests passed",
            "ERROR: Deployment failed",
            "INFO: Sending email"
        ], $this->log->getLoggedLines());
    }


    public function test_project_with_tests_and_failing_build_without_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(false);

        $project = Project::builder()
            ->setTestStatus(TestStatus::passingTests())
            ->setDeploysSuccessfully(false)
            ->build();

        $this->emailer
            ->expects($this->never())
            ->method('send');

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: Tests passed",
            "ERROR: Deployment failed",
            "INFO: Email disabled"
        ], $this->log->getLoggedLines());
    }


    public function test_project_without_tests_and_failing_build_with_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(true);

        $project = Project::builder()
            ->setTestStatus(TestStatus::noTests())
            ->setDeploysSuccessfully(false)
            ->build();

        $this->emailer
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo("Deployment failed"));

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: No tests",
            "ERROR: Deployment failed",
            "INFO: Sending email"
        ], $this->log->getLoggedLines());
    }


    public function test_project_without_tests_and_failing_build_without_email_notification()
    {
        $this->config->method('sendEmailSummary')->willReturn(false);

        $project = Project::builder()
            ->setTestStatus(TestStatus::noTests())
            ->setDeploysSuccessfully(false)
            ->build();

        $this->emailer
            ->expects($this->never())
            ->method('send');

        $this->pipeline->run($project);

        $this->assertEquals([
            "INFO: No tests",
            "ERROR: Deployment failed",
            "INFO: Email disabled"
        ], $this->log->getLoggedLines());
    }
}