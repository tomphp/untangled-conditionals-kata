package src

type TestStatus int

const (
	NoTests      TestStatus = iota
	PassingTests            = iota
	FailingTests            = iota
)
