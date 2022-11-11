class CapturingLogger:
    def __init__(self):
        self.lines = []

    def info(self, message):
        self.lines.append("INFO: " + message)

    def error(self, message):
        self.lines.append("ERROR: " + message)

    def get_logs(self):
        return self.lines
