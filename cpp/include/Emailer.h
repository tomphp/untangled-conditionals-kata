#pragma once

#include <string>

class Emailer {
  public:
    virtual void send(const std::string &message) {}
};
