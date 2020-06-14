#include "Logger.h"

#include <vector>

class CapturingLogger : public Logger {
    std::vector<std::string> lines;

  public:
    void info(const std::string &message) override
    {
        lines.emplace_back("INFO: " + message);
    }

    void error(const std::string &message) override
    {
        lines.emplace_back("ERROR: " + message);
    }

    std::vector<std::string> getLoggedLines() { return lines; }
};
