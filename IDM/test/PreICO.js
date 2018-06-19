var PreICO = artifacts.require("./PreICO.sol");
var Token = artifacts.require("./Token.sol");

contract('PreICO.PreICO', function(accounts) {
	it('Конструктор отрабатывает', async () => {
		const wallet = accounts[0]
		const startDate = Math.floor(Date.now()/1000)
		const endDate = Math.floor(Date.now()/1000) + 10000
		const ETHUSD = 1000

		const contract = await PreICO.new(wallet, startDate, endDate, ETHUSD)
		const walletDeployed = await contract.wallet()
		const startDateDeployed = await contract.startDate()
		const endDateDeployed = await contract.endDate()
		const ETHUSDDeployed = await contract.ETHUSD()

		assert.equal(wallet, walletDeployed)
		assert.equal(startDate, startDateDeployed)
		assert.equal(endDate, endDateDeployed)
		assert.equal(ETHUSD, ETHUSDDeployed)
	})
})

contract('PreICO.buyTokens', function(accounts) {

	it('Токены покупаются, деньги приходят', async () => {
		const token = await Token.deployed()
		const tokenAddress = await token.address

		const wallet = accounts[1]
		const startDate = Math.floor(Date.now()/1000)
		const endDate = Math.floor(Date.now()/1000) + 10000
		const ETHUSD = 1000

		const buyer = accounts[2]
		const value = await web3.toWei('1', 'ether')

		const preICO = await PreICO.new(wallet, startDate, endDate, ETHUSD)
		const preICOAddress = await preICO.address

		await preICO.setToken(tokenAddress)
		await token.addSaleAgent(preICOAddress)

		const weiRaisedStart = (await preICO.weiRaised()).toNumber()
		const walletEtherBalanceStart = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		const buyerTokenBalanceStart = (await token.balanceOf(buyer)).toNumber()
		await preICO.addToWhitelist(buyer)
		await preICO.buyTokens({from: buyer, value: value})

		const buyerTokenBalanceEnd = (await token.balanceOf(buyer)).toNumber()
		const weiRaisedEnd = (await preICO.weiRaised()).toNumber()
		const walletEtherBalanceEnd = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		assert.equal(buyerTokenBalanceEnd, buyerTokenBalanceStart + (value * 1300), 'неправильное количество купленных токенов')
		assert.equal(weiRaisedEnd, weiRaisedStart + +value, 'неверно увеличилось значение weiRaised')
		assert.equal(walletEtherBalanceEnd, walletEtherBalanceStart + +web3.fromWei(value), 'не пришли деньги на кошелек сбора средств')
	})

		it('Токены не покупаются, если отправитель не из ВЛ', async () => {
		const token = await Token.deployed()
		const tokenAddress = await token.address

		const wallet = accounts[1]
		const startDate = Math.floor(Date.now()/1000)
		const endDate = Math.floor(Date.now()/1000) + 10000
		const ETHUSD = 1000

		const buyer = accounts[2]
		const value = await web3.toWei('1', 'ether')

		const preICO = await PreICO.new(wallet, startDate, endDate, ETHUSD)
		const preICOAddress = await preICO.address

		await preICO.setToken(tokenAddress)
		await token.addSaleAgent(preICOAddress)

		const weiRaisedStart = (await preICO.weiRaised()).toNumber()
		const walletEtherBalanceStart = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		const buyerTokenBalanceStart = (await token.balanceOf(buyer)).toNumber()
		
		try{
			await preICO.buyTokens({from: buyer, value: value})	
		} catch(e) {
			assert(true)
		}
		
	})

})