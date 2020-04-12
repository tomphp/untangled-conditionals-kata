#pragma once

class Config {
    bool email_is_enabled;

  public:
    explicit Config(bool email_is_enabled = true)
        : email_is_enabled(email_is_enabled)
    {
    }

    bool sendEmailSummary() { return email_is_enabled; }
};
