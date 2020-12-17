<?php declare(strict_types=1);

namespace UntangledConditionals;

use UntangledConditionals\Dependencies\Config;
use UntangledConditionals\Dependencies\Emailer;
use UntangledConditionals\Dependencies\Logger;
use UntangledConditionals\Dependencies\Project;

final class Pipeline
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
     * @var Logger
     */
    private $log;

    public function __construct(Config $config, Emailer $emailer, Logger $log)
    {
        $this->config = $config;
        $this->emailer = $emailer;
        $this->log = $log;
    }

    private function projectTestPass(Project $project) :bool {
        if (!$project->hasTests()) {
            $this->log->info('No tests');
            return true;
        }
        if (!$project->runTestsResult()) {
            $this->log->error('Tests failed');
            return false;
        }
        $this->log->info('Tests passed');
        return true;
    }

    private function projectDeployedSuccessfully(Project $project) :bool { 
        if(!$project->deploysSuccessfully()) {
            $this->log->error('Deployment failed');
            return false;
        }
        $this->log->info('Deployment successful');
        return true;
    }

    public function run(Project $project): void
    {
        $testsPassed = $this->projectTestPass($project);
        $deploySuccessful = $testsPassed && $this->projectDeployedSuccessfully($project);

        if (!$this->config->sendEmailSummary()) {
            $this->log->info('Email disabled');
            return;
        }
        $this->log->info('Sending email');
        if (!$testsPassed) {
            $this->emailer->send('Tests failed');
            return;
        }        
        $this->emailer->send($deploySuccessful? 'Deployment completed successfully': 'Deployment failed');
    }
}