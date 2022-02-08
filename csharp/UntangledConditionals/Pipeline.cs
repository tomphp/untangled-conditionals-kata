using System;

namespace UntangledConditionals
{
    public class Pipeline
    {
        private readonly Config _config;
        private readonly Emailer _emailer;
        private readonly Logger _log;


        public Pipeline(Config config, Emailer emailer, Logger log)
        {
            _config = config;
            _emailer = emailer;
            _log = log;
        }
        
        public void run(Project project) {
            bool testsPassed;
            bool deploySuccessful;

            if (project.hasTests()) {
                if ("success".Equals(project.runTests())) {
                    _log.info("Tests passed");
                    testsPassed = true;
                } else {
                    _log.error("Tests failed");
                    testsPassed = false;
                }
            } else {
                _log.info("No tests");
                testsPassed = true;
            }

            if (testsPassed) {
                if ("success".Equals(project.deploy())) {
                    _log.info("Deployment successful");
                    deploySuccessful = true;
                } else {
                    _log.error("Deployment failed");
                    deploySuccessful = false;
                }
            } else {
                deploySuccessful = false;
            }

            if (_config.sendEmailSummary()) {
                _log.info("Sending email");
                if (testsPassed) {
                    if (deploySuccessful) {
                        _emailer.send("Deployment completed successfully");
                    } else {
                        _emailer.send("Deployment failed");
                    }
                } else {
                    _emailer.send("Tests failed");
                }
            } else {
                _log.info("Email disabled");
            }
        }
    }

    public interface Logger
    {
        void info(string message);
        void error(string message);
    }

    public interface Emailer
    {
        void send(string message);
    }

    public interface Config
    {
        bool sendEmailSummary();
    }
}