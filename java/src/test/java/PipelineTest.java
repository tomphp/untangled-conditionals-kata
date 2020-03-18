import dependencies.Config;
import dependencies.Emailer;
import dependencies.Project;
import dependencies.TestStatus;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.mockito.*;

import java.util.logging.Logger;

import static dependencies.TestStatus.*;
import static org.mockito.Mockito.*;
import static org.mockito.Mockito.verify;

class PipelineTest {
    @Mock
    private Config config;

    @Mock
    private Logger log;

    @Mock
    private Emailer emailer;

    @BeforeEach
    void setUp() {
        MockitoAnnotations.initMocks(this);
    }

    @InjectMocks
    private Pipeline pipeline;

    @Test
    void project_with_tests_that_deploys_successfully_with_email_notification() {
        when(config.sendEmailSummary()).thenReturn(true);

        Project project = Project.builder()
                .setTestStatus(PASSING_TESTS)
                .setDeploysSuccessfully(true)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("Tests passed");
        orderedLog.verify(log).info("Deployment successful");
        orderedLog.verify(log).info("Sending email");

        verify(emailer).send("Deployment completed successfully");
    }

    @Test
    void project_with_tests_that_deploys_successfully_without_email_notification() {
        when(config.sendEmailSummary()).thenReturn(false);

        Project project = Project.builder()
                .setTestStatus(PASSING_TESTS)
                .setDeploysSuccessfully(true)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("Tests passed");
        orderedLog.verify(log).info("Deployment successful");
        orderedLog.verify(log).info("Email disabled");

        verify(emailer, never()).send(any());
    }

    @Test
    void project_without_tests_that_deploys_successfully_with_email_notification() {
        when(config.sendEmailSummary()).thenReturn(true);

        Project project = Project.builder()
                .setTestStatus(NO_TESTS)
                .setDeploysSuccessfully(true)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("No tests");
        orderedLog.verify(log).info("Deployment successful");
        orderedLog.verify(log).info("Sending email");

        verify(emailer).send("Deployment completed successfully");
    }

    @Test
    void project_without_tests_that_deploys_successfully_without_email_notification() {
        when(config.sendEmailSummary()).thenReturn(false);

        Project project = Project.builder()
                .setTestStatus(NO_TESTS)
                .setDeploysSuccessfully(true)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("No tests");
        orderedLog.verify(log).info("Deployment successful");
        orderedLog.verify(log).info("Email disabled");

        verify(emailer, never()).send(any());
    }

    @Test
    void project_with_tests_that_fail_with_email_notification() {
        when(config.sendEmailSummary()).thenReturn(true);

        Project project = Project.builder()
                .setTestStatus(FAILING_TESTS)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).severe("Tests failed");
        orderedLog.verify(log).info("Sending email");

        verify(emailer).send("Tests failed");
    }

    @Test
    void project_with_tests_that_fail_without_email_notification() {
        when(config.sendEmailSummary()).thenReturn(false);

        Project project = Project.builder()
                .setTestStatus(FAILING_TESTS)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).severe("Tests failed");
        orderedLog.verify(log).info("Email disabled");

        verify(emailer, never()).send(any());
    }

    @Test
    void project_with_tests_and_failing_build_with_email_notification() {
        when(config.sendEmailSummary()).thenReturn(true);

        Project project = Project.builder()
                .setTestStatus(PASSING_TESTS)
                .setDeploysSuccessfully(false)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("Tests passed");
        orderedLog.verify(log).severe("Deployment failed");
        orderedLog.verify(log).info("Sending email");

        verify(emailer).send("Deployment failed");
    }

    @Test
    void project_with_tests_and_failing_build_without_email_notification() {
        when(config.sendEmailSummary()).thenReturn(false);

        Project project = Project.builder()
                .setTestStatus(PASSING_TESTS)
                .setDeploysSuccessfully(false)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("Tests passed");
        orderedLog.verify(log).severe("Deployment failed");
        orderedLog.verify(log).info("Email disabled");

        verify(emailer, never()).send(any());
    }

    @Test
    void project_without_tests_and_failing_build_with_email_notification() {
        when(config.sendEmailSummary()).thenReturn(true);

        Project project = Project.builder()
                .setTestStatus(NO_TESTS)
                .setDeploysSuccessfully(false)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("No tests");
        orderedLog.verify(log).severe("Deployment failed");
        orderedLog.verify(log).info("Sending email");

        verify(emailer).send("Deployment failed");
    }

    @Test
    void project_without_tests_and_failing_build_without_email_notification() {
        when(config.sendEmailSummary()).thenReturn(false);

        Project project = Project.builder()
                .setTestStatus(NO_TESTS)
                .setDeploysSuccessfully(false)
                .build();

        pipeline.run(project);

        InOrder orderedLog = inOrder(log);
        orderedLog.verify(log).info("No tests");
        orderedLog.verify(log).severe("Deployment failed");
        orderedLog.verify(log).info("Email disabled");

        verify(emailer, never()).send(any());
    }
}