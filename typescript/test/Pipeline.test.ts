import {CapturingLogger} from "./CapturingLogger";
import {Config} from "../src/dependencies/Config";
import {Mailer} from "../src/dependencies/Mailer";
import {anyString, instance, mock, verify, when} from "ts-mockito";
import {Pipeline} from "../src/Pipeline";
import {Project} from "../src/dependencies/Project";
import {TestStatus} from "../src/dependencies/TestStatus";

describe("Pipeline running project", () => {

    let config: Config;
    let log: CapturingLogger;
    let mailer: Mailer;
    let pipeline: Pipeline;

    beforeEach(() => {
        log = new CapturingLogger();
        config = mock<Config>();
        mailer = mock<Mailer>();
        pipeline = new Pipeline(instance(config), instance(mailer), log);
    });

    it("with tests that deploys successfully with email notification", () => {
        when(config.sendEmailSummary()).thenReturn(true);
        const project = Project.builder().setTestStatus(TestStatus.PASSING_TESTS)
            .setDeploysSuccessfully(true)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: Tests passed",
            "INFO: Deployment successful",
            "INFO: Sending email"
        ]);
        verify(mailer.send("Deployment completed successfully")).called();
    });

    it("with tests that deploys successfully without email notification", () => {
        when(config.sendEmailSummary()).thenReturn(false);
        const project = Project.builder()
            .setTestStatus(TestStatus.PASSING_TESTS)
            .setDeploysSuccessfully(true)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: Tests passed",
            "INFO: Deployment successful",
            "INFO: Email disabled"
        ]);
        verify(mailer.send(anyString())).never();
    });

    it("without tests that deploys successfully with email notification", () => {
        when(config.sendEmailSummary()).thenReturn(true);
        const project = Project.builder()
            .setTestStatus(TestStatus.NO_TESTS)
            .setDeploysSuccessfully(true)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: No tests",
            "INFO: Deployment successful",
            "INFO: Sending email"
        ]);
        verify(mailer.send("Deployment completed successfully")).called();
    });

    it("without tests that deploys successfully without email notification", () => {
        when(config.sendEmailSummary()).thenReturn(false);
        const project = Project.builder()
            .setTestStatus(TestStatus.NO_TESTS)
            .setDeploysSuccessfully(true)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: No tests",
            "INFO: Deployment successful",
            "INFO: Email disabled"
        ]);
        verify(mailer.send(anyString())).never();
    });

    it("with tests that fail with email notification", () => {
        when(config.sendEmailSummary()).thenReturn(true);
        const project = Project.builder()
            .setTestStatus(TestStatus.FAILING_TESTS)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "ERROR: Tests failed",
            "INFO: Sending email"
        ]);
        verify(mailer.send("Tests failed")).called();
    });

    it("with tests that fail without email notification", () => {
        when(config.sendEmailSummary()).thenReturn(false);
        const project = Project.builder()
            .setTestStatus(TestStatus.FAILING_TESTS)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "ERROR: Tests failed",
            "INFO: Email disabled"
        ]);
        verify(mailer.send(anyString())).never();
    });

    it("with tests and failing build with email notification", () => {
        when(config.sendEmailSummary()).thenReturn(true);
        const project = Project.builder()
            .setTestStatus(TestStatus.PASSING_TESTS)
            .setDeploysSuccessfully(false)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: Tests passed",
            "ERROR: Deployment failed",
            "INFO: Sending email"
        ]);
        verify(mailer.send("Deployment failed")).called();
    });

    it("with tests and failing build without email notification", () => {
        when(config.sendEmailSummary()).thenReturn(false);
        const project = Project.builder()
            .setTestStatus(TestStatus.PASSING_TESTS)
            .setDeploysSuccessfully(false)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: Tests passed",
            "ERROR: Deployment failed",
            "INFO: Email disabled"
        ]);
        verify(mailer.send(anyString())).never();
    });

    it("without tests and failing build with email notification", () => {
        when(config.sendEmailSummary()).thenReturn(true);
        const project = Project.builder()
            .setTestStatus(TestStatus.NO_TESTS)
            .setDeploysSuccessfully(false)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: No tests",
            "ERROR: Deployment failed",
            "INFO: Sending email"
        ]);
        verify(mailer.send("Deployment failed")).called();
    });

    it("without tests and failing build without email notification", () => {
        when(config.sendEmailSummary()).thenReturn(false);
        const project = Project.builder()
            .setTestStatus(TestStatus.NO_TESTS)
            .setDeploysSuccessfully(false)
            .build();

        pipeline.run(project);

        expect(log.lines).toEqual([
            "INFO: No tests",
            "ERROR: Deployment failed",
            "INFO: Email disabled"
        ]);
        verify(mailer.send(anyString())).never();
    });
});
