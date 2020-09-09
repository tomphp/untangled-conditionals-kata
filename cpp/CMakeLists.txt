cmake_minimum_required(VERSION 3.8)
project(pipeline)

set(CMAKE_CXX_STANDARD 14)

if("${CMAKE_C_COMPILER_ID}" MATCHES "(Apple)?[Cc]lang"
    OR "${CMAKE_CXX_COMPILER_ID}" MATCHES "(Apple)?[Cc]lang")
    message("Building with llvm code coverage")
    set(CMAKE_CXX_FLAGS "-fprofile-instr-generate -fcoverage-mapping")
elseif(CMAKE_COMPILER_IS_GNUCXX)
    message("Building with lcov code coverage")
    set(CMAKE_CXX_FLAGS "--coverage")
endif()

add_executable(pipeline
        src/Pipeline.cpp
        test/PipelineTest.cpp
        test/main.cpp
        )
target_include_directories(pipeline PRIVATE include)

include(CTest)

add_test (pipeline pipeline )
