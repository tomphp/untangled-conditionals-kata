package src

type Logger interface {
	info(message string)

	error(message string)
}
