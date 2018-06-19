pragma solidity ^0.4.23;

/*** @title SafeMath
 * @dev https://github.com/OpenZeppelin/openzeppelin-solidity/blob/master/contracts/math/SafeMath.sol */
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

/**
 * @title CrowdSale
 * @dev https://github.com/
 */
contract PreICOSale is Ownable {

    ERC20 public token;

    using SafeMath for uint;

    address public backEndOperator = msg.sender;
    address team = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 33% - team и ранние инвесторы проекта
    address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 2% - для баунти программы
    address reserve = 0xC032D3fCA001b73e8cC3be0B75772329395caA49; // 5%  - для резерва

    mapping(address=>bool) public whitelist;

    mapping(address => uint256) public investedEther;

    uint256 public startPreSale = 1531699201; // Monday, 16-Jul-18 00:00:01 UTC

    uint256 public endPreSale = 1534463999; // Thursday, 16-Aug-18 23:59:59 UTC

    uint256 public investors; // общее количество инвесторов
    uint256 public weisRaised; // общее колиество собранных Ether

    uint256 public softCapPreSale = 142858*1e18; // $300,000 = 142,858 INM
    uint256 public hardCapPreSale = 300000*1e18; // 300,000 INM = 630,000 USD

    uint256 public buyPrice; // 2.1 USD
    uint256 public dollarPrice; // Ether by USD

    uint256 public soldTokens; // solded tokens - > 300000 INM

    event Authorized(address wlCandidate, uint timestamp);
    event Revoked(address wlCandidate, uint timestamp);
    event UpdateDollar(uint256 time, uint256 _rate);

    modifier backEnd() {
        require(msg.sender == backEndOperator || msg.sender == owner);
        _;
    }

    // конструктор контракта
    constructor(ERC20 _token, uint256 usdETH) public {
        token = _token;
        dollarPrice = usdETH;
        buyPrice = (1e17/dollarPrice)*21; // 2.10 usd
    }

    // изменение даты начала предварительной распродажи
    function setStartPreSale(uint256 newStartPreSale) public onlyOwner {
        startPreSale = newStartPreSale;
    }

    // изменение даты окончания предварительной распродажи
    function setEndPreSale(uint256 newEndPreSale) public onlyOwner {
        endPreSale = newEndPreSale;
    }

    // Изменение адреса оператора бекэнда
    function setBackEndAddress(address newBackEndOperator) public onlyOwner {
        backEndOperator = newBackEndOperator;
    }

    // Изменение курса доллра к эфиру
    function setBuyPrice(uint256 _dollar) public onlyOwner {
        dollarPrice = _dollar;
        buyPrice = (1e17/dollarPrice)*21; // 2.10 usd
        emit UpdateDollar(now, dollarPrice);
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

    function isPreSale() public constant returns(bool) {
        return now >= startPreSale && now <= endPreSale;
    }

    // callback функция контракта
    function () public payable {
        require(isWhitelisted(msg.sender));
        require(isPreSale());
        require(msg.value >= 23*buyPrice); // ~ 50 USD
        preSale(msg.sender, msg.value);
        require(soldTokens<=hardCapPreSale);
        investedEther[msg.sender] = investedEther[msg.sender].add(msg.value);
    }

    // выпуск токенов в период предварительной распродажи
    function preSale(address _investor, uint256 _value) internal {
        uint256 tokens = _value.mul(1e18).div(buyPrice);
        uint256 tokensByDate = tokens.mul(15).div(100);
        uint256 bonusSumTokens = tokens.mul(bonusSum(tokens)).div(100);
        tokens = tokens.add(tokensByDate).add(bonusSumTokens); // 60%
        token.mintFromICO(_investor, tokens);
        soldTokens = soldTokens.add(tokens);

        uint256 tokensTeam = tokens.mul(11).div(20); // 33 %
        token.mintFromICO(team, tokensTeam);

        uint256 tokensBoynty = tokens.div(30); // 2 %
        token.mintFromICO(bounty, tokensBoynty);

        uint256 tokensReserve = tokens.div(12);  // 5 %
        token.mintFromICO(reserve, tokensReserve);

        weisRaised = weisRaised.add(_value);
    }

    function bonusSum(uint256 _amount) pure private returns(uint256) {
        if (_amount > 95238*1e18) { // 200k+	10% INMCoin
            return 10;
        } else if (_amount > 23809*1e18) { // 50k - 200k	7% INMCoin
            return 7;
        } else if (_amount > 9523*1e18) { // 20k - 50k	5% INMCoin
            return 5;
        } else if (_amount > 2380*1e18) { // 5k - 20k	3% INMCoin
            return 3;
        } else {
            return 0;
        }
    }

    // Отправка эфира с контракта
    function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
        _to.transfer(amount);
    }

    /*******************************************************************************
     * Refundable
     */
    function refundPreICO() public {
        require(soldTokens < softCapPreSale && now > endPreSale);
        uint rate = investedEther[msg.sender];
        require(investedEther[msg.sender] >= 0);
        investedEther[msg.sender] = 0;
        msg.sender.transfer(rate);
        weisRaised = weisRaised.sub(rate);
    }
}