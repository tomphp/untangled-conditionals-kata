package src

type CapturingLogger struct {
	lines []string
}

func (c *CapturingLogger) info(message string) {
	c.lines = append(c.lines, "INFO: "+message)
}
func (c *CapturingLogger) error(message string) {
	c.lines = append(c.lines, "ERROR: "+message)
}
func (c *CapturingLogger) getLoggedLines() []string {
	return c.lines
}
