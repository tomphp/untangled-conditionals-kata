package src

type TestStatus int

const (
	NO_TESTS      TestStatus = iota
	PASSING_TESTS            = iota
	FAILING_TESTS            = iota
)
