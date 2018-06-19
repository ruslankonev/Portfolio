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


contract PreICO is Ownable {
  using SafeMath for uint256;

  modifier onlyWhileOpen {
      require(now >= startDate && now <= endDate);
      _;
  }

  // The token being sold
  ERC20 public token;

  // Address where funds are collected
  address public wallet;

  // Сколько токенов покупатель получает за 1 эфир
  uint256 public rate = 1300;

  //  Сколько эфиров привлечено (в wei)
  uint256 public weiRaised;

  // Цена ETH в центах
  uint256 public ETHUSD;

  // Дата начала
  uint256 public startDate;

  // Дата окончания
  uint256 public endDate;

  // Бонус за этап, %
  uint8 public stageBonus = 30;

  // Whitelist
  mapping(address => bool) public whitelist;

  event TokenPurchase(address indexed buyer, uint256 value, uint256 amount);

    constructor(
    address _wallet, 
    uint256 _startDate, 
    uint256 _endDate,
    uint256 _ETHUSD
  ) public {
    require(_wallet != address(0));
    require(_startDate >= now);
    require(_endDate >= _startDate);

    wallet = _wallet;
    startDate = _startDate;
    endDate = _endDate;
    ETHUSD = _ETHUSD;
  }

  // Установить стоимость токена
  function setRate (uint16 _rate) public onlyOwner {
    require(_rate > 0);
    rate = _rate;
  }

  // Установить торгуемый токен
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
  function buyTokens() public payable {
    address beneficiary = msg.sender;
    uint256 weiAmount = msg.value;

    _preValidatePurchase(beneficiary, weiAmount);

    // Считаем сколько токенов перевести
    uint256 tokens = weiAmount.mul(rate);

    // update state
    weiRaised = weiRaised.add(weiAmount);

    _deliverTokens(beneficiary, tokens);
    _forwardFunds();

     emit TokenPurchase(beneficiary, weiAmount, tokens);
  }

  // Добавить адрес в whitelist
  function addToWhitelist(address _beneficiary) public onlyOwner {
    whitelist[_beneficiary] = true;
  }

  // Добавить несколько адресов в whitelist
  function addManyToWhitelist(address[] _beneficiaries) public onlyOwner {
    for (uint256 i = 0; i < _beneficiaries.length; i++) {
      whitelist[_beneficiaries[i]] = true;
    }
  }

  // Исключить адрес из whitelist
  function removeFromWhitelist(address _beneficiary) external onlyOwner {
    whitelist[_beneficiary] = false;
  }

  // Узнать истек ли срок проведения
  function hasClosed() public view returns (bool) {
    return now > endDate;
  }

  /*
   * Внутренние методы
   */

   // Валидация перед покупкой токенов
  function _preValidatePurchase(address _beneficiary, uint256 _weiAmount) internal view onlyWhileOpen {
    require(_weiAmount != 0);
    require(now < endDate);
    require(whitelist[_beneficiary]);
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