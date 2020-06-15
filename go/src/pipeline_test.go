package src

import (
	"github.com/stretchr/testify/assert"
	"testing"
)

type SpyEmailer struct {
	email     string
	wascalled bool
}

func (s *SpyEmailer) send(message string) {
	s.email = message
	s.wascalled = true
}

type StubConfig struct {
	shouldSend bool
}

func (c StubConfig) sendEmailSummary() bool {
	return c.shouldSend
}

var doSendEmail = StubConfig{true}
var noEmail = StubConfig{false}
var buildSuccess = true
var buildFailure = false

func TestProject_with_tests_that_deploys_successfully_with_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(doSendEmail)

	var project = Project{buildSuccess, PASSING_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: Tests passed",
		"INFO: Deployment successful",
		"INFO: Sending email"}, logger.getLoggedLines())
	assert.Equal(t, "Deployment completed successfully", emailer.email)
}

func TestProject_with_tests_that_deploys_successfully_without_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(noEmail)

	var project = Project{buildSuccess, PASSING_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: Tests passed",
		"INFO: Deployment successful",
		"INFO: Email disabled"}, logger.getLoggedLines())
	assert.False(t, emailer.wascalled)
}

func TestProject_without_tests_that_deploys_successfully_with_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(doSendEmail)

	var project = Project{buildSuccess, NO_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: No tests",
		"INFO: Deployment successful",
		"INFO: Sending email"}, logger.getLoggedLines())
	assert.Equal(t, "Deployment completed successfully", emailer.email)
}

func TestProject_without_tests_that_deploys_successfully_without_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(noEmail)

	var project = Project{buildSuccess, NO_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: No tests",
		"INFO: Deployment successful",
		"INFO: Email disabled"}, logger.getLoggedLines())
	assert.False(t, emailer.wascalled)
}

func TestProject_without_tests_that_fail_with_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(doSendEmail)

	var project = Project{buildSuccess, FAILING_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"ERROR: Tests failed",
		"INFO: Sending email"}, logger.getLoggedLines())
	assert.Equal(t, "Tests failed", emailer.email)
}

func TestProject_without_tests_that_fail_withou_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(noEmail)

	var project = Project{buildSuccess, FAILING_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"ERROR: Tests failed",
		"INFO: Email disabled"}, logger.getLoggedLines())
	assert.False(t, emailer.wascalled)
}

func TestProject_with_tests_that_fails_build_with_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(doSendEmail)

	var project = Project{buildFailure, PASSING_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: Tests passed",
		"ERROR: Deployment failed",
		"INFO: Sending email"}, logger.getLoggedLines())
	assert.Equal(t, "Deployment failed", emailer.email)
}

func TestProject_with_tests_that_fails_build_without_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(noEmail)

	var project = Project{buildFailure, PASSING_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: Tests passed",
		"ERROR: Deployment failed",
		"INFO: Email disabled"}, logger.getLoggedLines())
	assert.False(t, emailer.wascalled)
}

func TestProject_withou_tests_that_fails_build_with_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(doSendEmail)

	var project = Project{buildFailure, NO_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: No tests",
		"ERROR: Deployment failed",
		"INFO: Sending email"}, logger.getLoggedLines())
	assert.Equal(t, "Deployment failed", emailer.email)
}

func TestProject_without_tests_that_fails_build_without_email_notification(t *testing.T) {
	logger, emailer, pipeline := newPipelineAndSpies(noEmail)

	var project = Project{buildFailure, NO_TESTS}
	pipeline.run(project)

	assert.Equal(t, []string{
		"INFO: No tests",
		"ERROR: Deployment failed",
		"INFO: Email disabled"}, logger.getLoggedLines())
	assert.False(t, emailer.wascalled)
}

func newPipelineAndSpies(config StubConfig) (*CapturingLogger, *SpyEmailer, Pipeline) {
	logger := &CapturingLogger{}
	emailer := &SpyEmailer{}
	var pipeline = Pipeline{config, emailer, logger}
	return logger, emailer, pipeline
}
