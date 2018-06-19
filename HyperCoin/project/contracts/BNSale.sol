pragma solidity ^0.4.23;

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
    function transfer(address _beneficiary, uint256 _tokenAmount) external returns (bool);
    function transferFromICO(address _to, uint256 _value) external returns(bool);
}

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

contract MainSale is Ownable {

    using SafeMath for uint;

    ERC20 public token;

    address reserve = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37;
    address promouters = 0xCe66E79f59eafACaf4CaBaA317CaB4857487E3a1;
    address bounty = 0xC032D3fCA001b73e8cC3be0B75772329395caA49;

    uint256 public constant decimals = 18;
    uint256 constant dec = 10**decimals;

    mapping(address=>bool) whitelist;

    uint256 public startCloseSale = now; // start // 16.04.2018 10:00 UTC
    uint256 public endCloseSale = 1532995199; // Monday, 30-Jul-18 23:59:59 UTC

    uint256 public startStage1 = 1532995201; // Tuesday, 31-Jul-18 00:00:01 UTC
    uint256 public endStage1 = 1533081599; // Tuesday, 31-Jul-18 23:59:59 UTC

    uint256 public startStage2 = 1533081600; // Wednesday, 01-Aug-18 00:00:00 UTC
    uint256 public endStage2 = 1533686399; // Tuesday, 07-Aug-18 23:59:59 UTC

    uint256 public startStage3 = 1533686400; // Wednesday, 08-Aug-18 00:00:00 UTC
    uint256 public endStage3 = 1535759999; // Friday, 31-Aug-18 23:59:59 UTC

    uint256 public buyPrice = 10000000000000000; // 0.01 Ether

    uint256 public weisRaised = 0;

    string public stageNow = "NoSale";

    event Authorized(address wlCandidate, uint timestamp);
    event Revoked(address wlCandidate, uint timestamp);

    constructor() public {}

    function setToken (ERC20 _token) public onlyOwner {
        token = _token;
    }

    /*******************************************************************************
     * Whitelist's section
     */
    function authorize(address wlCandidate) public onlyOwner  {
        require(wlCandidate != address(0x0));
        require(!isWhitelisted(wlCandidate));
        whitelist[wlCandidate] = true;
        emit Authorized(wlCandidate, now);
    }

    function revoke(address wlCandidate) public  onlyOwner {
        whitelist[wlCandidate] = false;
        emit Revoked(wlCandidate, now);
    }

    function isWhitelisted(address wlCandidate) public view returns(bool) {
        return whitelist[wlCandidate];
    }

    /*******************************************************************************
     * Setter's Section
     */

    function setStartCloseSale(uint256 newStartSale) public onlyOwner {
        startCloseSale = newStartSale;
    }

    function setEndCloseSale(uint256 newEndSale) public onlyOwner{
        endCloseSale = newEndSale;
    }

    function setStartStage1(uint256 newsetStage2) public onlyOwner{
        startStage1 = newsetStage2;
    }

    function setEndStage1(uint256 newsetStage3) public onlyOwner{
        endStage1 = newsetStage3;
    }

    function setStartStage2(uint256 newsetStage4) public onlyOwner{
        startStage2 = newsetStage4;
    }

    function setEndStage2(uint256 newsetStage5) public onlyOwner{
        endStage2 = newsetStage5;
    }

    function setStartStage3(uint256 newsetStage5) public onlyOwner{
        startStage3 = newsetStage5;
    }

    function setEndStage3(uint256 newsetStage5) public onlyOwner{
        endStage3 = newsetStage5;
    }

    function setPrices(uint256 newPrice) public onlyOwner {
        buyPrice = newPrice;
    }

    /*******************************************************************************
     * Payable Section
     */
    function ()  public payable {

        require(msg.value >= buyPrice);

        if (now >= startCloseSale || now <= endCloseSale) {
            require(isWhitelisted(msg.sender));
            closeSale(msg.sender, msg.value);
            stageNow = "Close Sale for Whitelist's members";

        } else if (now >= startStage1 || now <= endStage1) {
            sale1(msg.sender, msg.value);
            stageNow = "Stage 1";

        } else if (now >= startStage2 || now <= endStage2) {
            sale2(msg.sender, msg.value);
            stageNow = "Stage 2";

        } else if (now >= startStage3 || now <= endStage3) {
            sale3(msg.sender, msg.value);
            stageNow = "Stage 3";

        } else {
            stageNow = "No Sale";
            revert();
        }
    }

    // выпуск токенов в период закрытой распродажи
    function closeSale(address _investor, uint256 _value) internal {

        uint256 tokens = _value.mul(1e18).div(buyPrice); // 68%
        uint256 bonusTokens = tokens.mul(30).div(100); // + 30% за стадию
        tokens = tokens.add(bonusTokens);
        token.transferFromICO(_investor, tokens);
        weisRaised = weisRaised.add(msg.value);

        uint256 tokensReserve = tokens.mul(15).div(68); // 15 %
        token.transferFromICO(reserve, tokensReserve);

        uint256 tokensBoynty = tokens.div(34); // 2 %
        token.transferFromICO(bounty, tokensBoynty);

        uint256 tokensPromo = tokens.mul(15).div(68); // 15%
        token.transferFromICO(promouters, tokensPromo);
    }

    // выпуск токенов в период 1 распродажи
    function sale1(address _investor, uint256 _value) internal {

        uint256 tokens = _value.mul(1e18).div(buyPrice); // 66%

        uint256 bonusTokens = tokens.mul(10).div(100); // + 10% за стадию
        tokens = tokens.add(bonusTokens); // 66 %

        token.transferFromICO(_investor, tokens);

        uint256 tokensReserve = tokens.mul(5).div(22); // 15 %
        token.transferFromICO(reserve, tokensReserve);

        uint256 tokensBoynty = tokens.mul(2).div(33); // 4 %
        token.transferFromICO(bounty, tokensBoynty);

        uint256 tokensPromo = tokens.mul(5).div(22); // 15%
        token.transferFromICO(promouters, tokensPromo);

        weisRaised = weisRaised.add(msg.value);
    }

    // выпуск токенов в период 2 распродажи
    function sale2(address _investor, uint256 _value) internal {

        uint256 tokens = _value.mul(1e18).div(buyPrice); // 64 %

        uint256 bonusTokens = tokens.mul(5).div(100); // + 5% за стадию распродажи
        tokens = tokens.add(bonusTokens);

        token.transferFromICO(_investor, tokens);

        uint256 tokensReserve = tokens.mul(15).div(64); // 15 %
        token.transferFromICO(reserve, tokensReserve);

        uint256 tokensBoynty = tokens.mul(3).div(32); // 6 %
        token.transferFromICO(bounty, tokensBoynty);

        uint256 tokensPromo = tokens.mul(15).div(64); // 15%
        token.transferFromICO(promouters, tokensPromo);

        weisRaised = weisRaised.add(msg.value);
    }

    // выпуск токенов в период 3 распродажи
    function sale3(address _investor, uint256 _value) internal {

        uint256 tokens = _value.mul(1e18).div(buyPrice); // 62 %
        token.transferFromICO(_investor, tokens);

        uint256 tokensReserve = tokens.mul(15).div(62); // 15 %
        token.transferFromICO(reserve, tokensReserve);

        uint256 tokensBoynty = tokens.mul(4).div(31); // 8 %
        token.transferFromICO(bounty, tokensBoynty);

        uint256 tokensPromo = tokens.mul(15).div(62); // 15%
        token.transferFromICO(promouters, tokensPromo);

        weisRaised = weisRaised.add(msg.value);
    }

    /*******************************************************************************
     * Manual Management
     */
    function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
        _to.transfer(amount);
    }
}