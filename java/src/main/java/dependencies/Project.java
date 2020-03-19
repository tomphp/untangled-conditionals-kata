package dependencies;

import static dependencies.TestStatus.*;

public class Project {
    private final boolean buildsSuccessfully;
    private final TestStatus testStatus;

    public static ProjectBuilder builder() {
        return new ProjectBuilder();
    }

    private Project(boolean buildsSuccessfully, TestStatus testStatus) {
        this.buildsSuccessfully = buildsSuccessfully;
        this.testStatus = testStatus;
    }

    public boolean hasTests() {
        return testStatus != NO_TESTS;
    }

    public String runTests() {
        return testStatus == PASSING_TESTS ? "success" : "failure";
    }

    public String deploy() {
        return buildsSuccessfully ? "success" : "failure";
    }

    public static class ProjectBuilder {
        private boolean buildsSuccessfully;
        private TestStatus testStatus;

        public ProjectBuilder setTestStatus(TestStatus testStatus) {
            this.testStatus = testStatus;
            return this;
        }

        public ProjectBuilder setDeploysSuccessfully(boolean buildsSuccessfully) {
            this.buildsSuccessfully = buildsSuccessfully;
            return this;
        }

        public Project build() {
            return new Project(buildsSuccessfully, testStatus);
        }
    }
}
