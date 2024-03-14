import {Config} from "./dependencies/Config";
import {Mailer} from "./dependencies/Mailer";
import {Logger} from "./dependencies/Logger";
import {Project} from "./dependencies/Project";

export class Pipeline {
    private readonly config: Config;
    private readonly mailer: Mailer;
    private readonly log: Logger;

    constructor(config: Config, mailer: Mailer, log: Logger) {
        this.config = config;
        this.mailer = mailer;
        this.log = log;
    }

    run(project: Project): void {
        let testsPassed: boolean;
        let deploySuccessful: boolean;

        if (project.hasTests()) {
            if ("success" === project.runTests()) {
                this.log.info("Tests passed");
                testsPassed = true;
            } else {
                this.log.error("Tests failed");
                testsPassed = false;
            }
        } else {
            this.log.info("No tests");
            testsPassed = true;
        }

        if (testsPassed) {
            if ("success" === project.deploy()) {
                this.log.info("Deployment successful");
                deploySuccessful = true;
            } else {
                this.log.error("Deployment failed");
                deploySuccessful = false;
            }
        } else {
            deploySuccessful = false;
        }

        if (this.config.sendEmailSummary()) {
            this.log.info("Sending email");
            if (testsPassed) {
                if (deploySuccessful) {
                    this.mailer.send("Deployment completed successfully");
                } else {
                    this.mailer.send("Deployment failed");
                }
            } else {
                this.mailer.send("Tests failed");
            }
        } else {
            this.log.info("Email disabled");
        }
    }
}
