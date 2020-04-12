
#include "Config.h"
#include "Emailer.h"
#include "Logger.h"
#include "Project.h"

class Pipeline {
    Config &config;
    Emailer &emailer;
    Logger &log;

  public:
    Pipeline(Config &config, Emailer &emailer, Logger &log)
        : config(config)
        , emailer(emailer)
        , log(log)
    {
    }

    void run(Project project);
};
