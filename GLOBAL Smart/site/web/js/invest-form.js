jQuery(document).ready(function($)
{
    var form = $("#invest-form"),
        currency = form.find('input[name="Transactions[currency_id]"]'),
        investments = form.find('input[name="Transactions[investments]"]'),
        cur_currency = form.find('input[name="Transactions[currency_id]"]:checked').attr("data-code");

    form.removeClass("processed");
    
    investments.on("focus", function()
    {
    	return initCurrency(cur_currency);
    });

    currency.on("change", function()
    {
    	form.addClass("processed");
    	initCurrency($(this).attr("data-code"));
    	tokensCount();
    });

    investments.on("input", function()
    {
    	var value = $(this).val();

    	if(value > 0)
    	{
    		form.addClass("processed");
    	}

    	tokensCount();
    });

    window.addEventListener("click", function()
    {
    	if(web3.eth.defaultAccount)
    	{
    		enableInvestmentsCount();
    	}
    });
});

/*
	В зависимости от валюты инвестирования инициализируем соот. ф-цию 
*/
function initCurrency(code)
{
	var form = $("#invest-form"),
		button = form.find("#transferFunds");

	form.find(":not([readonly]), :button, #investments-count").attr("disabled", false);
	form.find("#meta-mask").remove();
	removeAllEvents(button.get(0), "click");

	switch(code)
	{
		case "BTC":
			listenForClicks(code);
    	break;

    	case "ETH":
    		if(typeof web3 == "undefined")
    		{
				$.getScript("/js/web3/web3.min.js").done(function(script, textStatus)
				{
					if(typeof web3 !== 'undefined')
					{
						web3 = new Web3(web3.currentProvider);
						startApp(web3);
					}
					else
					{
						rejectMetaMask(true);
					}
			    }).error(function()
			    {
			    	rejectMetaMask(true);
			    });
			}
			else
			{
				if(typeof web3 !== "undefined")
				{
					startApp(web3);
				}
				else
				{
		       		rejectMetaMask(true);
				}
			}
    	break;

    	default:
    		swal({
                title: 'MetaMask',
                confirmButtonColor: '#4fa7f3',
                text: "Currency not selected",
                type: 'warning'
            });
	}
}

/*
	Рассчёт кол-ва токенов/бонусов на backend
*/
function tokensCount()
{
    var form = $("#invest-form"),
    	currency_id = form.find('input[name="Transactions[currency_id]"]:checked').val(),
    	investments_elem = form.find('input[name="Transactions[investments]"]'),
    	investments = investments_elem.val();

    $("#token-count").val("");
    $("#bonus-count").val("");
    $("#total-tokens-count").val("");

    if(investments > 0)
	{
    	$.ajax({
			url: "/ajax/tokenscount",
			dataType: 'json',
			data: {currency_id: currency_id,
				   investments: investments
			},
			success: function(data)
			{
				if(data)
				{
					var tname = data.name;
					
					if(data.tokens)
					{
						var tokens = divide(data.tokens);
	        			$("#token-count").val(tokens + " " + tname);
        			}

			        if(data.bonus >= 0)
			        {
			        	var bonus = divide(data.bonus);
						$("#bonus-count").val(bonus + " " + tname);
					}

					if(data.total)
					{
						  var total_tokens = divide(data.total);
						  $("#total-tokens-count").val(total_tokens + " " + tname);
					}
				}

				form.removeClass("processed");
			},
			errors: function(err)
			{
				form.removeClass("processed");
				console.log(err);
			}
		});
    }
    else
    {
    	investments_elem.focus();
    	form.removeClass("processed");
    }

    return false;
}

/*
	Округляем до сотых в поле Investments
*/
function roundInvestments(val)
{
	var reg = /(\d+\.\d{2})/,
		match = reg.exec(val)

	return (match) ? match[0] : val;
}

/*
	Разделяем тысячные в поле Investments
*/
function divideInvestments(val)
{
	return String(val).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
}

/*
	Добавление транзакции
*/
function addTransaction(txhash)
{
	var form = $("#invest-form"),
    	currency_id = form.find('input[name="Transactions[currency_id]"]:checked').val(),
    	investments = form.find('input[name="Transactions[investments]"]').val();

	$.ajax({
		url: "/ajax/addtransaction",
		type: "POST",
		data: {currency_id: currency_id,
				investments: investments,
				txhash: txhash
		},
		success: function(response)
		{
			if(response.error)
			{
				swal({
                    title: 'Website',
                    confirmButtonColor: '#4fa7f3',
                    text: response.msg,
                    type: 'warning'
                });

				//alert(response.msg);
			}

			form.removeClass("processed");
		},
		errors: function(err)
		{
			form.removeClass("processed");
			console.log(err);
		}
	});

	return false;
}

/*
	Добавление события элемента
*/
var _eventHandlers = {};

function addEvent(node, event, handler, capture)
{
    if(!(node in _eventHandlers))
    {
        // _eventHandlers stores references to nodes
        _eventHandlers[node] = {};
    }

    if(!(event in _eventHandlers[node]))
    {
        // each entry contains another entry for each event type
        _eventHandlers[node][event] = [];
    }

    // capture reference
    _eventHandlers[node][event].push([handler, capture]);
    node.addEventListener(event, handler, capture);
}

/*
	Удаления события элемента
*/
function removeAllEvents(node, event)
{
    if(node in _eventHandlers)
    {
        var handlers = _eventHandlers[node];

        if(event in handlers)
        {
            var eventHandlers = handlers[event];

            for(var i = eventHandlers.length; i--;)
            {
                var handler = eventHandlers[i];
                node.removeEventListener(event, handler[0], handler[1]);
            }
        }
    }
}

/*
	Добавления события клика на кнопку инвестирования
*/
function listenForClicks(code, web3, data)
{
	var form = $("#invest-form"),
		button = form.find("#transferFunds");

	form.removeClass("processed");
	
	addEvent(button.get(0), "click", function(event)
	{
        event.preventDefault();

        var elem = form.find('input[name="Transactions[investments]"]'),
        	investments = elem.val();

        if((investments != "") && (investments != "0"))
        {
        	switch(code)
		   	{
		   		case "BTC":
		   			BTCAction();
		   			break;
		   		case "ETH":
			        ETHAction(web3, data, investments);
			        break;
		   	}
        }
        else
        {
        	elem.focus();
        }
       	
    }, false);

	return false;
}

/*
	Определит браузер
*/
function browserDetect()
{
	var browser = '';

	if(/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent))
	{
	    browser = 'Opera';
	}
	else if(/MSIE (\d+\.\d+);/.test(navigator.userAgent))
	{
	    browser = 'MSIE';
	}
	else if(/Navigator[\/\s](\d+\.\d+)/.test(navigator.userAgent))
	{
	    browser = 'Netscape';
	}
	else if(/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent))
	{
	    browser = 'Chrome';
	}
	else if(/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent))
	{
	    browser = 'Safari';
	}
	else if(/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent))
	{
	    browser = 'Firefox';
	}

	return browser;
}

/*
	MetaMask not installed

	TODO

	Перенести в backend для реализации перевода
	Проверять с помощью get_browser()

	@param int detect - проверять барузер или нет
*/
function rejectMetaMask(detect)
{
	var form = $("#invest-form"),
    	button = form.find("#transferFunds"),
    	msg = "",
    	plugin = "//metamask.io",
    	browser = browserDetect();

    form.removeClass("processed");

   	form.find(":input[type='tel']:not([readonly]), :button").attr("disabled", true);
   	removeAllEvents(button.get(0), "click");

   	if(detect)
   	{
	   	switch(browser)
	   	{
	   		case "Opera":
	   		case "Firefox":
		        plugin = "//addons.mozilla.org/en-US/firefox/addon/ether-metamask/";
		        break;
		    case "Chrome":
		        plugin = "//chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn";
		        break;
	   	}

	   	msg =  '<div id="meta-mask" class="alert alert-icon alert-white alert-warning alert-dismissible fade in" role="alert">\n' +
	    			'<i class="mdi mdi-alert"></i>\n' +
	    			'<strong>MetaMask not installed!</strong><br> to work correctly, you need to install the plugin <a href="' + plugin + '" target="_blank">MetaMask</a>\n' +
	        	'</div>';

	   	return form.prepend(msg);
	}
	else
	{
		return false;
	}
}

/*
	Получение ETH адреса получателя
	и дефолтной сети ETH из конфига
*/
function startApp(web3)
{
	if(!web3.eth.defaultAccount)
	{
		web3.eth.getAccounts(function(error, accounts)
		{
		    if(error != null || accounts.length == 0)
		    {
		    	rejectMetaMask();

	    		swal({
		            title: 'Metamask',
		            confirmButtonColor: '#4fa7f3',
		            text: "Log in to MetaMask and refresh the page",
		            type: 'warning'
		        });

		        return false;
		    }

		    web3.eth.defaultAccount = accounts[0];
		});
	}

	/*
		Проверка ETH адреса на наличие в WL
		При true - Получение адреса для инвестирования (address_to)
	*/
	isWhitelisted(web3, function(response)
	{
		if(response)
		{
			/*
				Получение адреса для инвестирования (address_to)
			*/
			$.ajax({
				url: "/ajax/getaddressto",
				dataType: 'json',
				success: function(data)
				{
					if(data)
					{
						getNetwork(web3, function(network)
		                {
		                    if(data.network == network)
		                    {
					    		listenForClicks("ETH", web3, data);
					    	}
					    	else
					    	{
					    		rejectMetaMask();

					    		swal({
		                            title: 'MetaMask',
		                            confirmButtonColor: '#4fa7f3',
		                            text: "Current network is <b>" + network + "</b>. <br/>You must select a network " + data.network,
		                            type: 'warning'
		                        });
					    	}
					    });
					}
					else
					{
						console.log("Invalid address");
						return false;
					}

					var form = $("#invest-form");
					form.removeClass("processed");
				},
				errors: function(err)
				{
					rejectMetaMask();
					console.log(err);
					return false;
				}
			});
		}
	});
}

/*
	Проверка ETH адреса на наличие в WL

	@param obj web3
    @param function callback
*/
function isWhitelisted(web3, callback)
{
	var form = $("#invest-form"),
		address = web3.eth.defaultAccount;
	
	if(address != null && address !== "")
	{
		form.addClass("processed");

		$.ajax({
			url: "/ajax/iswhitelisted",
			data: {address: address},
			success: function(response)
			{
				if(!response)
				{
					rejectMetaMask();

					swal({
   						title: 'MetaMask',
   						confirmButtonColor: '#4fa7f3',
   						text: "The selected wallet address of MetaMask does not match the profile settings.<br/> Please change the account in MetaMask or change the profile settings",
   						type: 'warning'
					});
				}
				
				return callback(response);
			},
			error: function(err)
			{
				form.removeClass("processed");
				console.log(err);
			}
		});
	}
	else
	{
		rejectMetaMask();
	}
	
	return false;
}

/*
	Запускаем MetaMask
	Расчёт gas
	Отправка транзакции
*/
function ETHAction(web3, data, investments)
{
	var toWei = web3.toWei(investments, "ether"),
		address_from = web3.eth.defaultAccount,
		address_to = data.address_to;
		
    web3.eth.estimateGas({
    	from: address_from,
    	value: toWei
    }, function(err, gas)
    {
    	if(!err)
    	{
    		web3.eth.sendTransaction({
			    from: address_from,
			    to: address_to,
			    value: toWei,
			    gas: parseInt(gas * 10)
			}, function(error, txhash)
			{
				if(!error)
				{
					addTransaction(txhash);
				}
				else
				{
					swal({
		                title: 'MetaMask',
		                confirmButtonColor: '#4fa7f3',
		                text: "The request was rejected in MetaMask",
		                type: 'warning'
		            });

					console.log(error);
				}
			});
    	}
    	else
    	{
    		swal({
                title: 'MetaMask',
                confirmButtonColor: '#4fa7f3',
                text: "Invalid transaction data",
                type: 'warning'
            });

    		console.log(err);
    	}
    });
}

/*
	BTC Action
*/
function BTCAction()
{
    addTransaction(); 
}

/*
    Определение выбранной сети Ethereum

    @param obj web3
    @param function callback
*/
function getNetwork(web3, callback)
{
    var network = '';

    web3.version.getNetwork(function(err, netId){
        if(!err)
        {
            switch(netId)
            {
                case "1":
                    network = "Mainnet";
                    break
                case "2":
                    network = "Morden";
                    break
                case "3":
                    network = "Ropsten";
                    break
                case "4":
                    network = "Rinkeby";
                    break
                case "42":
                    network = "Kovan";
                    break
                default:
                    network = "Undefined";
            }

            return callback(network);
        }
    });
}

/*
    Округляем до сотых
*/
function round(val)
{
    var reg = /(\d+\.\d{2})/,
        match = reg.exec(val);

    return (match) ? match[0] : val;
}

/*
    Разделяем тысячные
*/
function divide(val)
{
    return String(val).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
}

/*
	Делает поле investmentsCount доступным
*/
function enableInvestmentsCount() 
{
	var form = $("#invest-form");
	form.find("#investments-count, #transferFunds").attr("disabled", false);

	return false;
}