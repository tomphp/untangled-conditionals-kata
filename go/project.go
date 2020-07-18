package src

type Project struct {
	buildsSuccessfully bool
	testStatus         TestStatus
}

func (p *Project) SetTestStatus(testStatus TestStatus) {
	p.testStatus = testStatus
}

func (p Project) hasTests() bool {
	return p.testStatus != NoTests
}

func (p Project) runTests() string {
	if p.testStatus == PassingTests {
		return "success"
	}
	return "failure"
}
func (p Project) deploy() string {
	if p.buildsSuccessfully {
		return "success"
	}
	return "failure"
}

type ProjectBuilder struct {
	buildsSuccessfully bool
	testStatus         TestStatus
}

func builder() ProjectBuilder {
	return ProjectBuilder{}
}
