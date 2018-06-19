pragma solidity ^0.4.23;


library SafeMath {
    function mul(uint256 a, uint256 b) internal pure returns (uint256) {
        if (a == 0) {
            return 0;
        }
        uint256 c = a * b;
        assert(c / a == b);
        return c;
    }
    function div(uint256 a, uint256 b) internal pure returns (uint256) {
        uint256 c = a / b;
        return c;
    }
    function sub(uint256 a, uint256 b) internal pure returns (uint256) {
        assert(b <= a);
        return a - b;
    }
    function add(uint256 a, uint256 b) internal pure returns (uint256) {
        uint256 c = a + b;
        assert(c >= a);
        return c;
    }
}


interface ERC20 {
  function transfer (address _beneficiary, uint256 _tokenAmount) external returns (bool);  
  function mint (address _to, uint256 _amount) external returns (bool);
}


interface PreICO {
  uint256 public weiRaised;
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


contract ICO is Ownable {
  using SafeMath for uint256;

  modifier onlyWhileOpen {
      require(now >= startDate && now < endDate);
      _;
  }

  // The token being sold
  ERC20 public token;

  // Address where funds are collected
  address public wallet;

  // Сколько токенов покупатель получает за 1 эфир
  uint256 public rate = 1000;

  // Amount of wei raised
  uint256 public weiRaised;

  // Минимальный объем привлечения средств в ходе ICO в центах
  uint256 public softcap = 300000000;

  // Потолок привлечения средств в ходе ICO в центах
  uint256 public hardcap = 2500000000;

  // Цена ETH в центах
  uint256 public ETHUSD;

  // Дата начала
  uint256 public startDate;

  // Дата окончания
  uint256 public endDate;

  // Бонус реферала, %
  uint8 public referalBonus = 3;

  // Бонус приглашенного рефералом, %
  uint8 public invitedByReferalBonus = 2;  

  // Инвесторы, которые купили токен
  mapping (address => uint256) public investors;
  
  event TokenPurchase(address indexed buyer, uint256 value, uint256 amount);

    constructor(
    address _wallet, 
    uint256 _startDate, 
    uint256 _endDate,
    uint256 _ETHUSD,
    PreICO _preICO
  ) public {
    require(_wallet != address(0));
    require(_startDate >= now);
    require(_endDate >= _startDate + 60 days);

    wallet = _wallet;
    startDate = _startDate;
    endDate = _endDate;
    ETHUSD = _ETHUSD;
    weiRaised = _preICO.weiRaised();
  }
  
  // Установить стоимость токена
  function setRate (uint16 _rate) public onlyOwner {
    require(_rate > 0);
    rate = _rate;
  }

  // Установить торгуемй токен
  function setToken (ERC20 _token) public onlyOwner {
    token = _token;
  }
  
  // Установить дату начала
  function setStartDate (uint256 _startDate) public onlyOwner {
    require(_startDate < endDate);
    startDate = _startDate;
  }

  // Установить дату окончания
  function setEndDate (uint256 _endDate) public onlyOwner {
    require(_endDate > startDate);
    endDate = _endDate;
  }

  // Установить стоимость эфира в центах
  function setETHUSD (uint256 _ETHUSD) public onlyOwner {
    ETHUSD = _ETHUSD;
  }

  function () external payable {
    buyTokens();
  }

  // Покупка токенов
  function buyTokens() public onlyWhileOpen payable {

    uint256 weiAmount = msg.value;
    _preValidatePurchase(weiAmount);

    address beneficiary = msg.sender;
    uint256 tokens = _getTokenAmountWithBonus(weiAmount);

    weiRaised = weiRaised.add(weiAmount);

    _deliverTokens(beneficiary, tokens);
    _forwardFunds();

    emit TokenPurchase(beneficiary, weiAmount, tokens);
  }

  // Покупка токенов с реферальным бонусом
  function buyTokensWithReferal(address _referal) public onlyWhileOpen payable {
    
    uint256 weiAmount = msg.value;
    _preValidatePurchase(weiAmount);

    address beneficiary = msg.sender;
    uint256 tokens = _getTokenAmountWithBonus(weiAmount).add(_getTokenAmountWithReferal(weiAmount, 2));
    uint256 referalTokens = _getTokenAmountWithReferal(weiAmount, 3);

    weiRaised = weiRaised.add(weiAmount);

    _deliverTokens(beneficiary, tokens);
    _deliverTokens(_referal, referalTokens);
    _forwardFunds();

    emit TokenPurchase(beneficiary, weiAmount, tokens);
  }

  // Узнать истек ли срок проведения
  function hasClosed() public view returns (bool) {
    return now > endDate;
  }

  /*
   * Внутренние методы
   */

   // Валидация перед покупкой токенов
  function _preValidatePurchase(uint256 _weiAmount) internal view onlyWhileOpen {
    require(_weiAmount != 0);
    require(now < endDate);
    require((weiRaised.add(_weiAmount)).mul(ETHUSD).div(10**18) <= hardcap);
  }

  // Подсчет количества токенов в зависимости от суммы платежа и бонусных программ
  function _getTokenAmountWithBonus(uint256 _weiAmount) internal view returns(uint256) {
    uint256 baseTokenAmount = _weiAmount.mul(rate);
    uint256 tokenAmount = baseTokenAmount;
    uint256 usdAmount = _weiAmount.mul(ETHUSD).div(10**18);

    // Считаем бонусы за объем инвестиций
    if(usdAmount >= 10000000){
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(7).div(100));
    } else if(usdAmount >= 5000000){
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(5).div(100));
    } else if(usdAmount >= 1000000){
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(3).div(100));
    }
    
    // Считаем бонусы за этап ICO
    if(now < startDate + 15 days) {
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(20).div(100));
    } else if(now < startDate + 28 days) {
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(15).div(100));
    } else if(now < startDate + 42 days) {
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(10).div(100));
    } else {
        tokenAmount = tokenAmount.add(baseTokenAmount.mul(5).div(100));
    }

    return tokenAmount;
  }

  function _getTokenAmountWithReferal(uint256 _weiAmount, uint8 _percent) internal view returns(uint256) {
    return _weiAmount.mul(rate).mul(_percent).div(100);
  }

  // Перевод токенов
  function _deliverTokens(address _beneficiary, uint256 _tokenAmount) internal {
    token.mint(_beneficiary, _tokenAmount);
  }

  // Перевод средств на кошелек компании
  function _forwardFunds() internal {
    wallet.transfer(msg.value);
  }
}