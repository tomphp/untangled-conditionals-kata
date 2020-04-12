#pragma once

#include <vector>

#include "Emailer.h"

class CapturingEmailer : public Emailer {
    std::vector<std::string> emails;

  public:
    void send(const std::string &message) override
    {
        emails.emplace_back(message);
    }

    std::vector<std::string> getMail() { return emails; }
};
