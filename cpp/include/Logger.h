#pragma once

#include <string>

class Logger {
  public:
    virtual void info(const std::string &message) {}

    virtual void error(const std::string &message) {}
};
