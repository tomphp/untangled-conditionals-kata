import {TestStatus} from "./TestStatus";

export class Project {
    private readonly buildsSuccessfully: boolean;
    private readonly testStatus: TestStatus;

    static builder(): ProjectBuilder {
        return new ProjectBuilder();
    }

    constructor(buildsSuccessfully: boolean, testStatus: TestStatus) {
        this.buildsSuccessfully = buildsSuccessfully;
        this.testStatus = testStatus;
    }

    hasTests(): boolean {
        return this.testStatus != TestStatus.NO_TESTS;
    }

    runTests(): string {
        return this.testStatus == TestStatus.PASSING_TESTS ? "success" : "failure";
    }


    deploy(): string {
        return this.buildsSuccessfully ? "success" : "failure";
    }
}

export class ProjectBuilder {
    private buildsSuccessfully: boolean;
    private testStatus: TestStatus;

    setTestStatus(testStatus: TestStatus): ProjectBuilder {
        this.testStatus = testStatus;
        return this;
    }

    setDeploysSuccessfully(buildsSuccessfully: boolean): ProjectBuilder {
        this.buildsSuccessfully = buildsSuccessfully;
        return this;
    }

    build(): Project {
        return new Project(this.buildsSuccessfully, this.testStatus);
    }
}
