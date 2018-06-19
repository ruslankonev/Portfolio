pragma solidity 0.4.24;

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
 * @title GledChainMainSale Contract
 * @dev https/www.github.com/elephantmarketing
 */
contract GledChainSale is Ownable {
    
    ERC20 public token;
    
    using SafeMath for uint256;
    
    address public backEndOperator = msg.sender;
    
    address team = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 12 % - for founders
    
    address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 1 % - for bounty
    
    address reserve = 0x0B529De38cF76901451E540A6fEE68Dd1bc2b4B3; // 17% - for reserve
    
    
    mapping(address=>bool) public whitelist;
    
    mapping(address => uint256) public investedEther;
    
    
    uint256 public startSale = 1544832000; //Saturday, 15-Dec-18 00:00:00 UTC
    
    uint256 public endSale = 1548979199; // Thursday, 31-Jan-19 23:59:59 UTC
    
    uint256 stage1Sale = startSale + 7 days; // 0 - 7 days
    
    uint256 stage2Sale = startSale + 14 days; // 8 - 14 days
    
    uint256 stage3Sale = startSale + 21 days; // after 21 days  -
    
    uint256 public investors;
    
    uint256 public weisRaised;
    
    uint256 public softCap =  33462000*1e18; // 33,462,000 GLC = $10 038 600
    
    uint256 public hardCap = 79200000*1e18; // 79,200,000 GLC
    
    uint256 public buyPrice; // 0.3 USD
    
    uint256 public dollarPrice;
    
    uint256 public soldTokens;
    
    
    event Authorized(address wlCandidate, uint256 timestamp);
    
    event Revoked(address wlCandidate, uint256 timestamp);
    
    event UpdateDollar(uint256 time, uint256 _rate);
    
    event UpdateDataContract(uint256 time, string events);
    
    event Refund(uint256 sum, address investor);
    
    
    modifier backEnd() {
        require(msg.sender == backEndOperator || msg.sender == owner);
        _;
    }
    
    
    constructor(ERC20 _token, uint256 usdETH) public {
        token = _token;
        dollarPrice = usdETH;
        buyPrice = (1e17/dollarPrice)*3; // 0.3 usd
    }
    
    function setStartSale(uint256 newStartSale) public onlyOwner {
        startSale = newStartSale;
        emit UpdateDataContract(block.timestamp, "update start Sale date");
    }
    
    function setEndSale(uint256 newEndSale) public onlyOwner {
        endSale = newEndSale;
        emit UpdateDataContract(block.timestamp, "update end Sale date");
    }
    
    function setBackEndAddress(address newBackEndOperator) public onlyOwner {
        backEndOperator = newBackEndOperator;
        emit UpdateDataContract(block.timestamp, "update backEndAddress");
    }
    
    function setBuyPrice(uint256 _dollar) public onlyOwner {
        dollarPrice = _dollar;
        buyPrice = (1e17/dollarPrice)*3; // 0.3 usd
        emit UpdateDollar(now, dollarPrice);
    }
    
    /*******************************************************************************
	 * Whitelist's section */
    
    function authorize(address wlCandidate) public backEnd  {
        require(wlCandidate != address(0x0));
        require(!isWhitelisted(wlCandidate));
        whitelist[wlCandidate] = true;
        investors++;
        emit Authorized(wlCandidate, now);
    }
    
    function revoke(address wlCandidate) public  onlyOwner {
        whitelist[wlCandidate] = false;
        investors--;
        emit Revoked(wlCandidate, now);
    }
    
    function isWhitelisted(address wlCandidate) internal view returns(bool) {
        return whitelist[wlCandidate];
    }
    
    /*******************************************************************************
	 * Payable's section */
    
    function isSale() public constant returns(bool) {
        return block.timestamp >= startSale && block.timestamp <= endSale;
    }
    
    function () public payable {
        require(isWhitelisted(msg.sender));
        require(isSale());
        sale(msg.sender, msg.value);
        require(soldTokens<=hardCap);
        investedEther[msg.sender] = investedEther[msg.sender].add(msg.value);
    }
    
    function sale(address _investor, uint256 _value) internal {
        uint256 tokens = _value.mul(1e18).div(buyPrice);
        uint256 tokensByDate = tokens.mul(bonusDate()).div(100);
        tokens = tokens.add(tokensByDate);
        token.mintFromICO(_investor, tokens);
        soldTokens = soldTokens.add(tokens);
        
        uint256 tokensTeam = tokens.mul(12).div(70); // 12 %
        token.mintFromICO(team, tokensTeam);
        
        uint256 tokensBoynty = tokens.div(70); // 1 %
        token.mintFromICO(bounty, tokensBoynty);
        
        uint256 tokensForReserve = tokens.mul(17).div(70); // 17%
        token.mintFromICO(reserve, tokensForReserve);
        
        weisRaised = weisRaised.add(_value);
    }
    
    
    function bonusDate() private view returns (uint256){
        if (block.timestamp >= startSale && block.timestamp < stage1Sale) {  // 0-7 days +25%
            return 25;
        }
        else if (block.timestamp > stage1Sale && block.timestamp < stage2Sale) { // 8 - 14 days + 15%
            return 15;
        }
        else if (block.timestamp > stage2Sale && block.timestamp < stage3Sale) { // 15-21  days + 5%
            return 5;
        }
        else { // after 22 days - 0 %
            return 0;
        }
    }
    
    function mintManual(address receiver, uint256 _tokens) public backEnd {
        token.mintFromICO(receiver, _tokens);
        soldTokens = soldTokens.add(_tokens);
        require(soldTokens<=hardCap);
        
        uint256 tokensTeam = _tokens.mul(12).div(70); // 12 %
        token.mintFromICO(team, tokensTeam);
        
        uint256 tokensBoynty = _tokens.div(70); // 1 %
        token.mintFromICO(bounty, tokensBoynty);
        
        uint256 tokensForReserve = _tokens.mul(17).div(70); // 17%
        token.mintFromICO(reserve, tokensForReserve);
    }
    
    function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
        _to.transfer(amount);
    }
    
    function refundSale() public {
        require(soldTokens < softCap && now > endSale);
        uint256 rate = investedEther[msg.sender];
        require(investedEther[msg.sender] >= 0);
        investedEther[msg.sender] = 0;
        msg.sender.transfer(rate);
        weisRaised = weisRaised.sub(rate);
        emit Refund(rate, msg.sender);
    }
}