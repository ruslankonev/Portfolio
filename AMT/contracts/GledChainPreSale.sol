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
 * @title MainSale Contract
 * @dev https/www.github.com/elephantmarketing
 */
contract GledChainPreSale is Ownable {
    
    ERC20 public token;
    
    using SafeMath for uint256;
    
    
    address public backEndOperator = msg.sender;
    
    address team = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 12 % - for founders
    
    address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 1 % - for bounty
    
    address reserve = 0x0B529De38cF76901451E540A6fEE68Dd1bc2b4B3; // 17% - for reserve
    
    
    mapping(address=>bool) public whitelist;
    
    mapping(address => uint256) public investedEther;
    
    
    uint256 public startPreSale = 1536537601; // Monday, 10-Sep-18 00:00:01 UTC
    
    uint256 public endPreSale = 1539215999; // Wednesday, 10-Oct-18 23:59:59 UTC
    
    uint256 public investors;
    
    uint256 public weisRaised;
    
    uint256 public softCap = 1680000*1e18; // $504,000 = 1,680,000 GLC
    
    uint256 public hardCap = 4800000*1e18; // 4,800,000 GLC
    
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
        startPreSale = newStartSale;
        emit UpdateDataContract(block.timestamp, "update start Sale date");
    }
    
    function setEndSale(uint256 newEndSale) public onlyOwner {
        endPreSale = newEndSale;
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
    
    function isPreSale() public constant returns(bool) {
        return block.timestamp >= startPreSale && block.timestamp <= endPreSale;
    }
    
    function () public payable {
        require(isWhitelisted(msg.sender));
        require(isPreSale());
        preSale(msg.sender, msg.value);
        require(soldTokens<=hardCap);
        investedEther[msg.sender] = investedEther[msg.sender].add(msg.value);
    }
    
    
    function preSale(address _investor, uint256 _value) internal {
        uint256 tokens = _value.mul(1e18).div(buyPrice);
        uint256 tokensByStage = tokens.mul(30).div(100);
        tokens = tokens.add(tokensByStage);
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
    
    
    function refundPreSale() public {
        require(soldTokens < softCap && now > endPreSale);
        uint256 rate = investedEther[msg.sender];
        require(investedEther[msg.sender] >= 0);
        investedEther[msg.sender] = 0;
        msg.sender.transfer(rate);
        weisRaised = weisRaised.sub(rate);
        emit Refund(rate, msg.sender);
    }
}