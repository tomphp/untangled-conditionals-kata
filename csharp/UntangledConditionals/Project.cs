using System;

namespace UntangledConditionals
{
    public enum TestStatus {
        NO_TESTS, //
        PASSING_TESTS, //
        FAILING_TESTS
    }
    public class Project
    {
        private bool buildsSuccessfully;
        private TestStatus testStatus;

        public static ProjectBuilder builder() {
            return new ProjectBuilder();
        }

        private Project(bool buildsSuccessfully, TestStatus testStatus) {
            this.buildsSuccessfully = buildsSuccessfully;
            this.testStatus = testStatus;
        }

        public bool hasTests() {
            return testStatus != TestStatus.NO_TESTS;
        }

        public String runTests() {
            return testStatus == TestStatus.PASSING_TESTS ? "success" : "failure";
        }

        public String deploy() {
            return buildsSuccessfully ? "success" : "failure";
        }

        public class ProjectBuilder {
            public bool BuildsSuccessfully { get; set; }
            public TestStatus TestStatus { get; set; }

            public Project build() {
                return new Project(BuildsSuccessfully, TestStatus);
            }

            public ProjectBuilder SetTestStatus(TestStatus status)
            {
                this.TestStatus = status;
                return this;
            }

            public ProjectBuilder SetDeploysSuccessfully(bool deploys)
            {
                this.BuildsSuccessfully = deploys;
                return this;
            }
        }
    }
}