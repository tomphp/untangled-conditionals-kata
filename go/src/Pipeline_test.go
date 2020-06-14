package src

import (
	"github.com/stretchr/testify/assert"
	"testing"
)

type SpyEmailer struct {
	email string
}
func (s *SpyEmailer) send(message string) {
	s.email = message
}

type StubConfig struct {
	shouldSend bool
}
func (c StubConfig) sendEmailSummary() bool {
	return c.shouldSend
}

func TestProject_with_tests_that_deploys_successfully_with_email_notification(t *testing.T) {
	var project = Project{true, PASSING_TESTS}
	config := StubConfig{true}
	logger := &CapturingLogger{}
	emailer := &SpyEmailer{}
	var pipeline = Pipeline{config, emailer, logger}

	pipeline.run(project)

	expectedLog := []string{"INFO: Tests passed",
		"INFO: Deployment successful",
		"INFO: Sending email"}
	assert.Equal(t, expectedLog, logger.getLoggedLines())
	assert.Equal(t,"Deployment completed successfully", emailer.email)
}

