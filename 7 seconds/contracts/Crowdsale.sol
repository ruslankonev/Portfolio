pragma solidity ^0.4.23;

/**
 * @title SafeMath
 * @dev https://github.com/OpenZeppelin/openzeppelin-solidity/blob/master/contracts/math/SafeMath.sol
 */
library SafeMath {

    function mul(uint256 a, uint256 b) internal pure returns (uint256 c) {
        if (a == 0) {
            return 0;
        }
        c = a * b;
        assert(c / a == b);
        return c;
    }

    function div(uint256 a, uint256 b) internal pure returns (uint256) {
        return a / b;
    }

    function sub(uint256 a, uint256 b) internal pure returns (uint256) {
        assert(b <= a);
        return a - b;
    }

    function add(uint256 a, uint256 b) internal pure returns (uint256 c) {
        c = a + b;
        assert(c >= a);
        return c;
    }
}

interface ERC20 {
    function transfer (address _beneficiary, uint256 _tokenAmount) external returns (bool);
    function mintFromICO(address _to, uint256 _amount) external  returns(bool);
}
/**
 * @title Ownable
 * @dev https://github.com/OpenZeppelin/openzeppelin-solidity/blob/master/contracts/ownership/Ownable.sol
 */
contract Ownable {
    address public owner;

    constructor() public {
        owner = msg.sender;
    }

    modifier onlyOwner() {
        require(msg.sender == owner);
        _;
    }
}
contract MarkSale is Ownable {

    ERC20 public token;

    using SafeMath for uint;

    address public backEndOperator = msg.sender;
    address team = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 15% - основантели проекта
    address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 5% - для баунти программы
    address reserve = 0xCe66E79f59eafACaf4CaBaA317CaB4857487E3a1; // 5% - для резерва

    mapping(address=>bool) public whitelist;
    mapping(address => uint256) public investedEther;

    uint256 public startSale = now; // Monday, 01-Oct-18 00:00:01 UTC
    uint256 public endSale = 1535673600; // Friday, 31-Aug-18 00:00:00 UTC

    uint256 public investors; // общее количество инвесторов
    uint256 public weisRaised; // - общее количество эфира собранное в период сейла

    uint256 public softCap = 3000*1e18; // 3,000 ETHER
    uint256 step1 = 10000*1e18; // 10,000 ETHER
    uint256 step2 = 20000*1e18; // 20,000 ETHER;
    uint256 public hardCap = 40000*1e18; // 40,000 ETHER

    uint256 public MainSalePrice = 231000000000000; // 0.000231 ETH - цена токена на основной распродаже

    uint256 public soldTokens;

    event Finalized();
    event Authorized(address wlCandidate, uint timestamp);
    event Revoked(address wlCandidate, uint timestamp);

    modifier isUnderHardCap() {
        require(weisRaised <= hardCap);
        _;
    }

    modifier backEnd() {
        require(msg.sender == backEndOperator || msg.sender == owner);
        _;
    }
    // конструктор контракта
    constructor() public {}

    // авторизация токена/ или изменение адреса
    function setToken (ERC20 _token) public onlyOwner {
        token = _token;
    }

    // изменение даты начала основной распродажи
    function setStartSale(uint256 newStartSale) public onlyOwner {
        startSale = newStartSale;
    }
    // изменение даты окончания основной распродажи
    function setEndSale(uint256 newEndSaled) public onlyOwner {
        endSale = newEndSaled;
    }
    // Изменение адреса оператора бекэнда
    function setBackEndAddress(address newBackEndOperator) public onlyOwner {
        backEndOperator = newBackEndOperator;
    }

    /*******************************************************************************
     * Whitelist's section
     */
    // с сайта backEndOperator авторизует инвестора
    function authorize(address wlCandidate) public backEnd  {

        require(wlCandidate != address(0x0));
        require(!isWhitelisted(wlCandidate));
        whitelist[wlCandidate] = true;
        investors++;
        emit Authorized(wlCandidate, now);
    }
    // отмена авторизации инвестора в WL(только владелец контракта)
    function revoke(address wlCandidate) public  onlyOwner {
        whitelist[wlCandidate] = false;
        investors--;
        emit Revoked(wlCandidate, now);
    }
    // проверка на нахождение адреса инвестора в WL
    function isWhitelisted(address wlCandidate) internal view returns(bool) {
        return whitelist[wlCandidate];
    }
    /*******************************************************************************
     * Payable's section
     */

    function isMainSale() public constant returns(bool) {
        return now >= startSale && now <= endSale;
    }
    // callback функция контракта
    function () public payable isUnderHardCap
    {
        require(isWhitelisted(msg.sender));
        require(isMainSale());
        mainSale(msg.sender, msg.value);
        investedEther[msg.sender] = investedEther[msg.sender].add(msg.value);
    }

    // выпуск токенов в период распродажи
    function mainSale(address _investor, uint256 _value) internal {
        uint256 tokens = _value.mul(1e6).div(MainSalePrice); // 1e18*1e18/
        uint256 tokensCollect = tokens.mul(discountCollect()).div(100);
        tokens = tokens.add(tokensCollect);

        token.mintFromICO(_investor, tokens);

        uint256 tokensTeam = tokens.div(5); // 1/5
        token.mintFromICO(team, tokensTeam);

        uint256 tokensBoynty = tokens.div(15); // 1/15
        token.mintFromICO(bounty, tokensBoynty);

        uint256 tokenReserve = tokens.div(15); // 1/15
        token.mintFromICO(reserve, tokenReserve);

        weisRaised = weisRaised.add(msg.value);
        soldTokens = soldTokens.add(tokens);
    }

    function discountCollect() view private returns(uint256) {
        // До 3000 eth 50%, от 3000 до 10000-20%, от 10000 до 20000-10%
        // 50% скидка, если сумма сбора не привышает 3000 ETHER
        if(weisRaised <= softCap) {
            return 50;
        } // 20% скидка, если сумма сбора не привышает 10000 ETHER
        if(weisRaised <= step1) {
            return 20;
        } // 10% скидка, если сумма сбора не привышает 20000 ETHER
        if(weisRaised <= step2) {
            return 10;
        }
        return 0;
    }

    // Функция отправки токенов получателям в ручном режиме(только владелец контракта)
    function mintManual(address _recipient, uint256 _value) public backEnd {
        token.mintFromICO(_recipient, _value);

        uint256 tokensTeam = _value.div(5); // 1/5
        token.mintFromICO(team, tokensTeam);

        uint256 tokensBoynty = _value.div(15); // 1/15
        token.mintFromICO(bounty, tokensBoynty);

        uint256 tokenReserve = _value.div(15); // 1/15
        token.mintFromICO(reserve, tokenReserve);

        soldTokens = soldTokens.add(_value);
        //require(soldTokensPreSale <= hardCapPreSale);
        //require(soldTokensSale <= hardCapSale);
    }

    // Отправка эфира с контракта
    function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
        //require(weisRaised >= softCap);
        _to.transfer(amount);
    }

    /*******************************************************************************
     * Refundable
     */
    function refundICO() public {
        require(weisRaised < softCap && now > endSale);
        uint rate = investedEther[msg.sender];
        require(investedEther[msg.sender] >= 0);
        investedEther[msg.sender] = 0;
        msg.sender.transfer(rate);
        weisRaised = weisRaised.sub(rate);
    }
}