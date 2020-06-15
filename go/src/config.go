package src

type Config interface {
	sendEmailSummary() bool
}
