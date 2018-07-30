cmake_minimum_required(VERSION 3.10)
project(TestChain)

set(CMAKE_CXX_STANDARD 14)

add_executable(TestChain main.cpp Block.h Blockchain.h sha256.h sha256.cpp Block.cpp Blockchain.cpp)