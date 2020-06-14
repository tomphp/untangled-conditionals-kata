#include "Pipeline.h"

void Pipeline::run(Project project)
{
    bool testsPassed;
    bool deploySuccessful;

    if(project.hasTests()) {
        if("success" == project.runTests()) {
            log.info("Tests passed");
            testsPassed = true;
        }
        else {
            log.error("Tests failed");
            testsPassed = false;
        }
    }
    else {
        log.info("No tests");
        testsPassed = true;
    }

    if(testsPassed) {
        if("success" == project.deploy()) {
            log.info("Deployment successful");
            deploySuccessful = true;
        }
        else {
            log.error("Deployment failed");
            deploySuccessful = false;
        }
    }
    else {
        deploySuccessful = false;
    }

    if(config.sendEmailSummary()) {
        log.info("Sending email");
        if(testsPassed) {
            if(deploySuccessful) {
                emailer.send("Deployment completed successfully");
            }
            else {
                emailer.send("Deployment failed");
            }
        }
        else {
            emailer.send("Tests failed");
        }
    }
    else {
        log.info("Email disabled");
    }
}
