//
// Created by Ivan Borisov on 28.07.2018.
//

#ifndef TESTCHAIN_BLOCKCHAIN_H
#define TESTCHAIN_BLOCKCHAIN_H

#include<cstdint>
#include <vector>
#include "Block.h"

using namespace std;

class Blockchain {
public:
    Blockchain(); // конструктор

    void AddBlock(Block bNew);

private:
    uint32_t _nDifficulty;
    vector<Block>_vChain;

    Block _GetLastBlock() const;
};


#endif //TESTCHAIN_BLOCKCHAIN_H