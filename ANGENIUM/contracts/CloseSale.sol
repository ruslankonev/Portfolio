pragma solidity ^0.4.24;

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
 * @dev https://github.com/OpenZeppelin/openzeppelin-solidity/blob/master/contracts/ownership/Ownable.sol */
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
 * @dev https://github.com/elephant-marketing/*/

contract CloseSale is Ownable {
  
  ERC20 public token;
  
  using SafeMath for uint;
  
  address public backEndOperator = msg.sender;
  
  address team = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 10 % - founders
  
  address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 2 % - bounty
  
  
  mapping(address=>bool) public whitelist;
  
  mapping(address => uint256) public investedEther;
  
  uint256 public startCloseSale = 1533081600; // Wednesday, 01-Aug-18 00:00:00 UTC
  
  uint256 public endCloseSale = 1535759999; // Friday, 31-Aug-18 23:59:59 UTC
  
  uint256 public investors;
  
  uint256 public weisRaised;
  
  uint256 public buyPrice; // 1 USD
  
  uint256 public dollarPrice;
  
  uint256 public soldTokensCloseSale;
  
  uint256 public softcap = 420000*1e18; // 420,000 ANG
  
  uint256 public hardCap = 2070000*1e18; // 2,070,000 ANG
  
  event Authorized(address wlCandidate, uint timestamp);
  
  event Revoked(address wlCandidate, uint timestamp);
  
  event UpdateDollar(uint256 time, uint256 _rate);
  
  event Refund(uint256 sum, address investor);
  
  
  
  modifier backEnd() {
    require(msg.sender == backEndOperator || msg.sender == owner);
    _;
  }
  
  
  constructor(ERC20 _token, uint256 usdETH) public {
    token = _token;
    dollarPrice = usdETH;
    buyPrice = (1e18/dollarPrice); // 1 usd
  }
  
  
  function setStartCloseSale(uint256 newStartCloseSale) public onlyOwner {
    startCloseSale = newStartCloseSale;
  }
  
  function setEndCloseSale(uint256 newEndCloseSale) public onlyOwner {
    endCloseSale = newEndCloseSale;
  }
  
  function setBackEndAddress(address newBackEndOperator) public onlyOwner {
    backEndOperator = newBackEndOperator;
  }
  
  function setBuyPrice(uint256 _dollar) public onlyOwner {
    dollarPrice = _dollar;
    buyPrice = (1e18/dollarPrice); // 1 usd
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
  
  function isCloseSale() public constant returns(bool) {
    return now >= startCloseSale && now <= endCloseSale;
  }
  
  function () public payable {
    require(isWhitelisted(msg.sender));
    require(isCloseSale());
    closeSale(msg.sender, msg.value);
    require(soldTokensCloseSale<=hardCap);
    investedEther[msg.sender] = investedEther[msg.sender].add(msg.value);
  }
  
  function closeSale(address _investor, uint256 _value) internal {
    uint256 tokens = _value.mul(1e18).div(buyPrice);
    uint256 tokensByStage = tokens.div(2); // + 50% bonus
    
    tokens = tokens.add(tokensByStage); // 88%
    token.mintFromICO(_investor, tokens);
    soldTokensCloseSale = soldTokensCloseSale.add(tokens); // only sold
    
    uint256 tokensTeam = tokens.mul(5).div(44); // 10 %
    token.mintFromICO(team, tokensTeam);
    
    uint256 tokensBoynty = tokens.div(44); // 2 %
    token.mintFromICO(bounty, tokensBoynty);
    
    weisRaised = weisRaised.add(_value);
  }
  
  function mintManual(address receiver, uint256 _tokens) public backEnd {
    token.mintFromICO(receiver, _tokens);
    soldTokensCloseSale = soldTokensCloseSale.add(_tokens);
    
    uint256 tokensTeam = _tokens.mul(5).div(44); // 10 %
    token.mintFromICO(team, tokensTeam);
    
    uint256 tokensBoynty = _tokens.div(44); // 2 %
    token.mintFromICO(bounty, tokensBoynty);
  }
  
  function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
    _to.transfer(amount);
  }
  
  function refundSale() public {
    require(soldTokensCloseSale < softcap && now > endCloseSale);
    uint256 rate = investedEther[msg.sender];
    require(investedEther[msg.sender] >= 0);
    investedEther[msg.sender] = 0;
    msg.sender.transfer(rate);
    weisRaised = weisRaised.sub(rate);
    emit Refund(rate, msg.sender);
  }
}