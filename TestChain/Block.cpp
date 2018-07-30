#include "Block.h"
#include "sha256.h"

Block::Block(uint32_t nIndexIn, const string &sDataIn) : _nIndex(nIndexIn), _sData(sDataIn){
    _nNonce = -1;
    _tTime = time(nullptr);
}

string Block::GetHash() {
    return _sHash;
}
// it's Magic
void Block::MineBlock(uint32_t nDifficulty) {
    char cstr[nDifficulty + 1]; // массив символов длиной больше, чем значение, указанное для nDifficulty
    for (uint32_t i = 0; i < nDifficulty; ++i) { // Цикл for используется для заполнения массива нулями
        cstr[i] = '0';
    }
    cstr[nDifficulty] = '\0'; // конечный элемент массива, которому присваивается символ окончания строки ( \ 0 )

    string str(cstr);

    do { // используется цикл (строки 10-13) для увеличения _nNonce
        // _sHash назначается выводом _CalculateHash, передняя часть хэша затем сравнивает строку нулей, которые мы только что создали
        // если совпадение не найдено, цикл повторяется до тех пор, пока не будет найдено совпадение
        // Как только совпадение найдено, сообщение отправляется в выходной буфер, чтобы сказать, что блок был успешно запущен
        _nNonce++;
        _sHash = _CalculateHash();
    } while (_sHash.substr(0, nDifficulty) != str);
    cout << "Block mined: " << _sHash << endl;
}

inline string Block::_CalculateHash() const {
    stringstream ss; // строковый поток
    ss << _nIndex << _tTime << _sData << _nNonce << sPrevHash;

    return sha256(ss.str());
}