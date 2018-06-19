var ICO = artifacts.require("./Sale.sol");
var Token = artifacts.require("./SimpleToken.sol");

contract('Sale.Sale', function(accounts) {
	it('Конструктор ', async () => {
	})
})

contract('Sale.buyTokens', function(accounts) {

	it('Токены покупаются, деньги приходят', async () => {
		const token = await Token.deployed()
		const tokenAddress = await token.address

		const buyer = accounts[2]
		const value = await web3.toWei('1', 'ether')

		const contract = await Sale.new(wallet, startDate, endDate, ETHUSD)

		await contract.setToken(token)
		await token.setSaleAddress(crowdsaleAddress)

		const weiRaisedStart = (await contract.weiRaised()).toNumber()
		const walletEtherBalanceStart = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		const buyerTokenBalanceStart = (await token.balanceOf(buyer)).toNumber()
		await contract.fallback({from: buyer, value: value})

		const buyerTokenBalanceEnd = (await token.balanceOf(buyer)).toNumber()
		const weiRaisedEnd = (await contract.weiRaised()).toNumber()
		const walletEtherBalanceEnd = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		assert.equal(buyerTokenBalanceEnd, buyerTokenBalanceStart + (value * 1200), 'неправильное количество купленных токенов')
		assert.equal(weiRaisedEnd, weiRaisedStart + +value, 'неверно увеличилось значение weiRaised')
		assert.equal(walletEtherBalanceEnd, walletEtherBalanceStart + +web3.fromWei(value), 'не пришли деньги на кошелек сбора средств')
	})
})

		it('Токены не покупаются, если отправитель не из ВЛ', async () => {
		const token = await Token.deployed()
		const tokenAddress = await token.address

		const msg.sender = accounts[2]
		const msg.value = await web3.toWei('1', 'ether')

		const Sale = await ICO.new()
		const saleAddress = await saleAddress.address

		await Sale.setToken(tokenAddress)
		await token.setSaleAddress(crowdsaleAddress)

		const weiRaisedStart = (await ICO.weiRaised()).toNumber()
		const walletEtherBalanceStart = (await web3.fromWei(web3.eth.getBalance(wallet))).toNumber()

		const buyerTokenBalanceStart = (await token.balanceOf(buyer)).toNumber()

		try{
			await ICO.fallback({from: buyer, value: value})
		} catch(e) {
			assert(true)
		}

	})

})