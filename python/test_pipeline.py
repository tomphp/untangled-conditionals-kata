from capturing_logger import CapturingLogger
from pipeline import Pipeline
from project import Project
from status import PASSING_TESTS, NO_TESTS, FAILING_TESTS


class ConfigForTest:
    def __init__(self):
        self.can_send_email = False

    def will_send_email_summary(self):
        self.can_send_email = True

    def send_email_summary(self):
        return self.can_send_email


class EmailerForTest:
    def __init__(self):
        self.mails = []

    def send(self, summary):
        self.mails.append(summary)

    def get_logs(self):
        return self.mails


class TestPipeline:
    def setup_method(self):
        self.config = ConfigForTest()
        self.emailer = EmailerForTest()
        self.log = CapturingLogger()
        self.pipeline = Pipeline(self.config, self.emailer, self.log)

    def test_project_with_tests_that_deploys_successfully_with_email_notification(self):
        self.config.will_send_email_summary()

        project = Project.builder().set_test_status(PASSING_TESTS).set_deploys_successfully(True).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: Tests passed", "INFO: Deployment successful", "INFO: Sending email"]
        assert self.emailer.get_logs() == ["Deployment completed successfully"]

    def test_project_with_tests_that_deploys_successfully_without_email_notification(self):
        project = Project.builder().set_test_status(PASSING_TESTS).set_deploys_successfully(True).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: Tests passed", "INFO: Deployment successful", "INFO: Email disabled"]
        assert self.emailer.get_logs() == []

    def test_project_without_tests_that_deploys_successfully_with_email_notification(self):
        self.config.will_send_email_summary()

        project = Project.builder().set_test_status(NO_TESTS).set_deploys_successfully(True).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: No tests", "INFO: Deployment successful", "INFO: Sending email"]
        assert self.emailer.get_logs() == ["Deployment completed successfully"]

    def test_project_without_tests_that_deploys_successfully_without_email_notification(self):
        project = Project.builder().set_test_status(NO_TESTS).set_deploys_successfully(True).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: No tests", "INFO: Deployment successful", "INFO: Email disabled"]
        assert self.emailer.get_logs() == []

    def test_project_with_tests_that_fail_with_email_notification(self):
        self.config.will_send_email_summary()

        project = Project.builder().set_test_status(FAILING_TESTS).build()
        self.pipeline.run(project)

        assert self.log.get_logs() == ["ERROR: Tests failed", "INFO: Sending email"]
        assert self.emailer.get_logs() == ["Tests failed"]

    def test_project_with_tests_that_fail_without_email_notification(self):
        project = Project.builder().set_test_status(FAILING_TESTS).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["ERROR: Tests failed", "INFO: Email disabled"]
        assert self.emailer.get_logs() == []

    def test_project_with_tests_and_failing_build_with_email_notification(self):
        self.config.will_send_email_summary()

        project = Project.builder().set_test_status(PASSING_TESTS).set_deploys_successfully(False).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: Tests passed", "ERROR: Deployment failed", "INFO: Sending email"]
        assert self.emailer.get_logs() == ["Deployment failed"]

    def test_project_with_tests_and_failing_build_without_email_notification(self):
        project = Project.builder().set_test_status(PASSING_TESTS).set_deploys_successfully(False).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: Tests passed", "ERROR: Deployment failed", "INFO: Email disabled"]
        assert self.emailer.get_logs() == []

    def test_project_without_tests_and_failing_build_with_email_notification(self):
        self.config.will_send_email_summary()
        project = Project.builder().set_test_status(NO_TESTS).set_deploys_successfully(False).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: No tests", "ERROR: Deployment failed", "INFO: Sending email"]
        assert self.emailer.get_logs() == ["Deployment failed"]

    def test_project_without_tests_and_failing_build_without_email_notification(self):
        project = Project.builder().set_test_status(NO_TESTS).set_deploys_successfully(False).build()

        self.pipeline.run(project)

        assert self.log.get_logs() == ["INFO: No tests", "ERROR: Deployment failed", "INFO: Email disabled"]
        assert self.emailer.get_logs() == []
