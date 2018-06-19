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
 * @dev https://github.com/elephant-marketing/FBway_smart/blob/master/Smart_contract/contracts/ICO.sol */
contract MainICO is Ownable {
    
    ERC20 public token;
    
    using SafeMath for uint;
    
    address public backEndOperator = msg.sender;
    address founders = 0x7eDE8260e573d3A3dDfc058f19309DF5a1f7397E; // 20% - основантели проекта
    address bounty = 0x0cdb839B52404d49417C8Ded6c3E2157A06CdD37; // 3% - для баунти программы
    address consult = 0xC032D3fCA001b73e8cC3be0B75772329395caA49; // 7% - consult
    
    mapping(address=>bool) public whitelist;
    
    mapping(address => uint256) public investedEther;
    
    uint256 public startMainSale = now;
    uint256 public endMainSale = startMainSale + 5184000; // 60 days
    
    uint256 stage1Sale = startMainSale + 1123200; // 13 day
    uint256 stage2Sale = startMainSale + 2332800; // 27 day
    uint256 stage3Sale = startMainSale + 3542400; // 41 day
    
    uint256 public investors;
    uint256 public weisRaised;
    
    uint256 public softCapMainSale = 24200000*1e18; // 24,200,000 FBW
    uint256 public hardCapMainSale = 55000000*1e18; // 55,000,000 FBW
    
    uint256 public buyPrice; // 0.4 USD
    uint256 public dollarPrice;
    
    uint256 public soldTokensMainSale;
    
    event Authorized(address wlCandidate, uint timestamp);
    event Revoked(address wlCandidate, uint timestamp);
    event UpdateDollar(uint256 time, uint256 _rate);
    
    modifier backEnd() {
        require(msg.sender == backEndOperator || msg.sender == owner);
        _;
    }
    
    constructor(ERC20 _token, uint256 usdETH) public {
        token = _token;
        dollarPrice = usdETH;
        buyPrice = (1e17/dollarPrice)*4; // 0.4 usd
    }
    
    function setStartMainSale(uint256 newStartMainSale) public onlyOwner {
        startMainSale = newStartMainSale;
    }
    
    function setEndMainSale(uint256 newEndMainSale) public onlyOwner {
        endMainSale = newEndMainSale;
    }
    
    function setBackEndAddress(address newBackEndOperator) public onlyOwner {
        backEndOperator = newBackEndOperator;
    }
    
    function setBuyPrice(uint256 _dollar) public onlyOwner {
        dollarPrice = _dollar;
        buyPrice = (1e17/dollarPrice)*4; // 0.4 usd
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
    
    function isMainSale() public constant returns(bool) {
        return now >= startMainSale && now <= endMainSale;
    }
    
    function () public payable {
        require(isWhitelisted(msg.sender));
        require(isMainSale());
        mainSale(msg.sender, msg.value);
        require(soldTokensMainSale<=hardCapMainSale);
        investedEther[msg.sender] = investedEther[msg.sender].add(msg.value);
    }
    
    function mainSale(address _investor, uint256 _value) internal {
        uint256 tokens = _value.mul(1e18).div(buyPrice);
        uint256 tokensByDate = tokens.mul(bonusDate()).div(100);
        tokens = tokens.add(tokensByDate); // 70%
        token.mintFromICO(_investor, tokens);
        soldTokensMainSale = soldTokensMainSale.add(tokens);
        
        uint256 tokensFounders = tokens.mul(4).div(14); // 20 %
        token.mintFromICO(founders, tokensFounders);
        
        uint256 tokensBoynty = tokens.mul(3).div(70); // 3 %
        token.mintFromICO(bounty, tokensBoynty);
        
        uint256 tokensConsult = tokens.div(10);  // 7 %
        token.mintFromICO(consult, tokensConsult);
        
        weisRaised = weisRaised.add(_value);
    }
    
    function bonusDate() private view returns (uint256){
        if (now > startMainSale && now < stage1Sale) {  // 0 - 13 days mainSale
            return 7;
        }
        else if (now > stage1Sale && now < stage2Sale) { // 14 - 27 days mainSale
            return 4;
        }
        else if (now > stage2Sale && now < stage3Sale) { // 28 - 41 days mainSale
            return 2;
        }
        else {
            return 0; // 42 - 60
        }
    }
    
    function mintManual(address receiver, uint256 _tokens) public backEnd {
        token.mintFromICO(receiver, _tokens);
        soldTokensMainSale = soldTokensMainSale.add(_tokens);
        require(soldTokensMainSale<=hardCapMainSale);
        uint256 tokensFounders = _tokens.mul(4).div(14); // 20 %
        token.mintFromICO(founders, tokensFounders);
        
        uint256 tokensBoynty = _tokens.mul(3).div(70); // 3 %
        token.mintFromICO(bounty, tokensBoynty);
        
        uint256 tokensConsult = _tokens.div(10);  // 7 %
        token.mintFromICO(consult, tokensConsult);
    }
    
    function transferEthFromContract(address _to, uint256 amount) public onlyOwner {
        _to.transfer(amount);
    }
    
    function refundICO() public {
        require(soldTokensMainSale < softCapMainSale && now > endMainSale);
        uint rate = investedEther[msg.sender];
        require(investedEther[msg.sender] >= 0);
        investedEther[msg.sender] = 0;
        msg.sender.transfer(rate);
        weisRaised = weisRaised.sub(rate);
    }
}