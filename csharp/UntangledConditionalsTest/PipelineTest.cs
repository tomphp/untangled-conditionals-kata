using System.Collections.Generic;
using Xunit;
using Moq;
using UntangledConditionals;

namespace TestProject1
{
    public class PipelineTest
    {
        private Pipeline pipeline;
         private CapturingLogger log = new CapturingLogger();
         private Mock<Config> _configMock;
         private Mock<Emailer> _emailerMock;

         public PipelineTest()
         {
             _configMock = new Mock<Config>();
             _emailerMock = new Mock<Emailer>();
             pipeline = new Pipeline(_configMock.Object, _emailerMock.Object, log);
         }

         [Fact]
         void project_with_tests_that_deploys_successfully_with_email_notification()
         {
             _configMock.Setup(c => c.sendEmailSummary()).Returns(true);
             
             Project project = Project.builder() //
                 .SetTestStatus(TestStatus.PASSING_TESTS) //
                 .SetDeploysSuccessfully(true) //
                 .build();

             pipeline.run(project);

             Assert.Equal(new List<string>(){
                 "INFO: Tests passed", //
                 "INFO: Deployment successful", //
                 "INFO: Sending email" //
             }, log.Lines);

             _emailerMock.Verify(m => m.send("Deployment completed successfully"));
         }
         
         [Fact]
    void project_with_tests_that_deploys_successfully_without_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(false);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.PASSING_TESTS) //
                .SetDeploysSuccessfully(true) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: Tests passed", //
                "INFO: Deployment successful", //
                "INFO: Email disabled" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send(It.IsAny<string>()), Times.Never);
    }

    [Fact]
    void project_without_tests_that_deploys_successfully_with_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(true);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.NO_TESTS) //
                .SetDeploysSuccessfully(true) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: No tests", //
                "INFO: Deployment successful", //
                "INFO: Sending email" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send("Deployment completed successfully"));
    }

    [Fact]
    void project_without_tests_that_deploys_successfully_without_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(false);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.NO_TESTS) //
                .SetDeploysSuccessfully(true) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: No tests", //
                "INFO: Deployment successful", //
                "INFO: Email disabled" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send(It.IsAny<string>()), Times.Never);

    }

    [Fact]
    void project_with_tests_that_fail_with_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(true);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.FAILING_TESTS) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "ERROR: Tests failed", //
                "INFO: Sending email" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send("Tests failed"));
    }

    [Fact]
    void project_with_tests_that_fail_without_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(false);
        Project project = Project.builder() //
                .SetTestStatus(TestStatus.FAILING_TESTS) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "ERROR: Tests failed", //
                "INFO: Email disabled" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send(It.IsAny<string>()), Times.Never);

    }

    [Fact]
    void project_with_tests_and_failing_build_with_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(true);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.PASSING_TESTS) //
                .SetDeploysSuccessfully(false) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: Tests passed", //
                "ERROR: Deployment failed", //
                "INFO: Sending email" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send("Deployment failed"));
    }

    [Fact]
    void project_with_tests_and_failing_build_without_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(false);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.PASSING_TESTS) //
                .SetDeploysSuccessfully(false) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: Tests passed", //
                "ERROR: Deployment failed", //
                "INFO: Email disabled" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send(It.IsAny<string>()), Times.Never);

    }

    [Fact]
    void project_without_tests_and_failing_build_with_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(true);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.NO_TESTS) //
                .SetDeploysSuccessfully(false) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: No tests", //
                "ERROR: Deployment failed", //
                "INFO: Sending email" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send("Deployment failed"));
    }

    [Fact]
    void project_without_tests_and_failing_build_without_email_notification() {
        _configMock.Setup(c => c.sendEmailSummary()).Returns(false);

        Project project = Project.builder() //
                .SetTestStatus(TestStatus.NO_TESTS) //
                .SetDeploysSuccessfully(false) //
                .build();

        pipeline.run(project);

        Assert.Equal(new List<string>(){ 
                "INFO: No tests", //
                "ERROR: Deployment failed", //
                "INFO: Email disabled" //
        }, log.Lines);

        _emailerMock.Verify(m => m.send(It.IsAny<string>()), Times.Never);

    }
    }
}