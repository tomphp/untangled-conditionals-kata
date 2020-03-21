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

    public function run(Project $project): void
    {
        if ($project->hasTests()) {
            if ($project->runTests() === 'success') {
                $this->log->info('Tests passed');
                $testsPassed = true;
            } else {
                $this->log->error('Tests failed');
                $testsPassed = false;
            }
        } else {
            $this->log->info('No tests');
            $testsPassed = true;
        }

        if ($testsPassed) {
            if ($project->deploy() === 'success') {
                $this->log->info('Deployment successful');
                $deploySuccessful = true;
            } else {
                $this->log->error('Deployment failed');
                $deploySuccessful = false;
            }
        } else {
            $deploySuccessful = false;
        }

        if ($this->config->sendEmailSummary()) {
            $this->log->info('Sending email');
            if ($testsPassed) {
                if ($deploySuccessful) {
                    $this->emailer->send('Deployment completed successfully');
                } else {
                    $this->emailer->send('Deployment failed');
                }
            } else {
                $this->emailer->send('Tests failed');
            }
        } else {
            $this->log->info('Email disabled');
        }
    }
}