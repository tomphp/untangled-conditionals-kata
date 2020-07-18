package src

type Emailer interface {
	send(message string)
}
