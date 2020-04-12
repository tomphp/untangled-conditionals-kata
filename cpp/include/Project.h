#pragma once

#include <string>
#include "TestStatus.h"

class Project {
    bool buildsSuccessfully;
    TestStatus testStatus;

  public:
    Project(bool buildsSuccessfully, TestStatus testStatus)
        : buildsSuccessfully(buildsSuccessfully)
        , testStatus(testStatus)
    {
    }

    bool hasTests() { return testStatus != NO_TESTS; }

    std::string runTests()
    {
        return testStatus == PASSING_TESTS ? "success" : "failure";
    }

    std::string deploy() { return buildsSuccessfully ? "success" : "failure"; }
};
