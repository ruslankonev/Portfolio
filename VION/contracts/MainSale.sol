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
 * @dev https://github.com/elephant-marketing/VION_smart/blob/master/project/contracts/MainSale.sol
 */
contract MainSale is Ownable {
  
  ERC20 public token;
  
  using SafeMath for uint256;
  
  address public backEndOperator = msg.sender;
  address founders = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 20 % - for founders
  address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 1,5 % - for bounty
  
  mapping(address=>bool) public whitelist;
  
  mapping(address => uint256) public investedEther;
  
  uint256 public startSale = 1542240000; // Thursday, 15-Nov-18 00:00:00 UTC
  uint256 public endSale = 1547596799; // Tuesday, 15-Jan-19 23:59:59 UTC
  
  uint256 stage1Sale = startSale + 20 days; // 20 day
  uint256 stage2Sale = startSale + 40 days; // 40 day
  uint256 stage3Sale = startSale + 60 days; // 60 day
  
  uint256 public investors;
  uint256 public weisRaised;
  
  uint256 public softCap = 42900000*1e18; // 42,900,000 Vion
  uint256 public hardCap = 413250000*1e18; // 413,250,000 Vion
  
  uint256 public buyPrice; // 0.1 USD
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
    buyPrice = (1e17/dollarPrice); // 0.1 usd
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
    buyPrice = (1e17/dollarPrice); // 0.1 usd
    emit UpdateDollar(now, dollarPrice);
  }
  
  /*******************************************************************************
   * Whitelist's section     */
  
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
    tokens = tokens.add(tokensByDate); // 78,5%
    token.mintFromICO(_investor, tokens);
    soldTokens = soldTokens.add(tokens);
    
    uint256 tokensFounders = tokens.mul(40).div(157); // 20 %
    token.mintFromICO(founders, tokensFounders);
    
    uint256 tokensBoynty = tokens.mul(3).div(157); // 1,5 %
    token.mintFromICO(bounty, tokensBoynty);
    
    weisRaised = weisRaised.add(_value);
  }
  
  function bonusDate() private view returns (uint256){
    if (block.timestamp >= startSale && block.timestamp < stage1Sale) {  // 0 - 20 days mainSale
      return 15;
    }
    else if (block.timestamp > stage1Sale && block.timestamp < stage2Sale) { // 21 - 40 days mainSale
      return 12;
    }
    else if (block.timestamp > stage2Sale && block.timestamp < stage3Sale) { // 41 - 60 days mainSale
      return 10;
    }
    else if(block.timestamp > stage3Sale && block.timestamp <= endSale) { // 61 - 90 days
      return 5;
    }
    else {
      return 0;
    }
  }
  
  function mintManual(address receiver, uint256 _tokens) public backEnd {
    token.mintFromICO(receiver, _tokens);
    soldTokens = soldTokens.add(_tokens);
    require(soldTokens<=hardCap);
    
    uint256 tokensFounders = _tokens.mul(40).div(157); // 20 %
    token.mintFromICO(founders, tokensFounders);
    
    uint256 tokensBoynty = _tokens.mul(3).div(157); // 1,5 %
    token.mintFromICO(bounty, tokensBoynty);
  }
  
  function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
    _to.transfer(amount);
  }
  
  function refundICO() public {
    require(soldTokens < softCap && now > endSale);
    uint256 rate = investedEther[msg.sender];
    require(investedEther[msg.sender] >= 0);
    investedEther[msg.sender] = 0;
    msg.sender.transfer(rate);
    weisRaised = weisRaised.sub(rate);
    emit Refund(rate, msg.sender);
  }
}