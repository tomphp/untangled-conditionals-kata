package src

type Pipeline struct {
	config  Config
	emailer Emailer
	log     Logger
}

func (p *Pipeline) run(project Project) {
	var testsPassed = false
	var deploySuccessful = false

	if project.hasTests() {
		if "success" == project.runTests() {
			p.log.info("Tests passed")
			testsPassed = true
		} else {
			p.log.error("Tests failed")
			testsPassed = false
		}
	} else {
		p.log.info("No tests")
		testsPassed = true
	}

	if testsPassed {
		if "success" == project.deploy() {
			p.log.info("Deployment successful")
			deploySuccessful = true
		} else {
			p.log.error("Deployment failed")
			deploySuccessful = false
		}
	} else {
		deploySuccessful = false
	}

	if p.config.sendEmailSummary() {
		p.log.info("Sending email")
		if testsPassed {
			if deploySuccessful {
				p.emailer.send("Deployment completed successfully")
			} else {
				p.emailer.send("Deployment failed")
			}
		} else {
			p.emailer.send("Tests failed")
		}
	} else {
		p.log.info("Email disabled")
	}
}
