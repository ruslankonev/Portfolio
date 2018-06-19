var ICO = artifacts.require("./MainSale.sol");
var Token = artifacts.require("./VionToken.sol");

contract('ICO.ICO', function(accounts) {
	it('Конструктор отрабатывает', async () => {
		//const tokenAddress = accounts[8]
		//const startDate = Math.floor(Date.now()/1000)
		//const endDate = Math.floor(Date.now()/1000) + 10000
		const ETHUSD = 600

		const contract = await ICO.new(ETHUSD)
		//const tokenDeployed = await contract.token()
		//const startDateDeployed = await contract.startDate()
		//const endDateDeployed = await contract.endDate()
		const ETHUSDDeployed = await contract.ETHUSD()

		//assert.equal(tokenAddress, tokenDeployed)
		//assert.equal(startDate, startDateDeployed)
		//assert.equal(endDate, endDateDeployed)
		assert.equal(ETHUSD, ETHUSDDeployed)
	})
})

contract('ICO.buyTokens', function(accounts) {

	it('Токены покупаются, деньги приходят', async () => {
		const token = await Token.deployed()
		const tokenAddress = await token.address

		//const wallet = accounts[1]
		//const startDate = Math.floor(Date.now()/1000)
		//const endDate = Math.floor(Date.now()/1000) + 10000
		const ETHUSD = 1000

		const buyer = accounts[2]
		const value = await web3.toWei('1', 'ether')

		const contract = await ICO.new(ETHUSD)
		const ICOAddress = await contract.address

		await contract.setToken(tokenAddress)
		await token.addSaleAgent(ICOAddress)

		const weiRaisedStart = (await contract.weiRaised()).toNumber()
		const walletEtherBalanceStart = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		const buyerTokenBalanceStart = (await token.balanceOf(buyer)).toNumber()
		await contract.buyTokens({from: buyer, value: value})

		const buyerTokenBalanceEnd = (await token.balanceOf(buyer)).toNumber()
		const weiRaisedEnd = (await contract.weiRaised()).toNumber()
		const walletEtherBalanceEnd = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		assert.equal(buyerTokenBalanceEnd, buyerTokenBalanceStart + (value * 1200), 'неправильное количество купленных токенов')
		assert.equal(weiRaisedEnd, weiRaisedStart + +value, 'неверно увеличилось значение weiRaised')
		assert.equal(walletEtherBalanceEnd, walletEtherBalanceStart + +web3.fromWei(value), 'не пришли деньги на кошелек сбора средств')
	})
})

contract('ICO.buyTokensWithReferal', function(accounts) {

	it('Токены покупаются, деньги приходят', async () => {
		const token = await Token.deployed()
		const tokenAddress = await token.address

		const wallet = accounts[1]
		const startDate = Math.floor(Date.now()/1000)
		const endDate = Math.floor(Date.now()/1000) + 10000
		const ETHUSD = 1000

		const referal = accounts[2]
		const newInvestor = accounts[3]
		const value = await web3.toWei('1', 'ether')

		const contract = await ICO.new(wallet, startDate, endDate, ETHUSD)
		const ICOAddress = await contract.address

		await contract.setToken(tokenAddress)
		await token.addSaleAgent(ICOAddress)

		const referalTokenBalanceStart = (await token.balanceOf(referal)).toNumber()
		const newInvestorTokenBalanceStart = (await token.balanceOf(newInvestor)).toNumber()
		await contract.buyTokensWithReferal(referal, {from: newInvestor, value: value})

		const referalTokenBalanceEnd = (await token.balanceOf(referal)).toNumber()
		const newInvestorTokenBalanceEnd = (await token.balanceOf(newInvestor)).toNumber()

		assert.equal(referalTokenBalanceEnd, referalTokenBalanceStart + (value * 1000 * 0.03), 'неправильное количество токенов для пригласившего')
		assert.equal(newInvestorTokenBalanceEnd, newInvestorTokenBalanceStart + (value * 1200) + (value * 1000 * 0.02), 'неправильное количество токенов для приглашенного')
	})
})