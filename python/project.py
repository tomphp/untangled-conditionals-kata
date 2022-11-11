from status import NO_TESTS, PASSING_TESTS


class Project:
    @staticmethod
    def builder():
        return ProjectBuilder()

    def __init__(self, builds_successfully, test_status):
        self.builds_successfully = builds_successfully
        self.test_status = test_status

    def has_tests(self):
        return self.test_status != NO_TESTS

    def run_tests(self):
        return "success" if self.test_status == PASSING_TESTS else "failure"

    def deploy(self):
        return "success" if self.builds_successfully else "failure"


class ProjectBuilder:
    def __init__(self):
        self.test_status = False
        self.builds_successfully = False

    def set_test_status(self, test_status):
        self.test_status = test_status
        return self

    def set_deploys_successfully(self, builds_successfully):
        self.builds_successfully = builds_successfully
        return self

    def build(self):
        return Project(self.builds_successfully, self.test_status)
