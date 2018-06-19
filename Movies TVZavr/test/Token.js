var Token = artifacts.require("./SimpleToken.sol");

contract('Token.Token', function(accounts) {
	it('Конструктор', async () => {
		const owner = accounts[0]
		const symbol = 'MVS'
		const name = 'Movies Chain'

		const token = await Token.deployed()
		const ownerDeployed = await token.owner()
		const nameDeployed = await token.name()
		const symbolDeployed = await token.symbol()

		assert.equal(owner, ownerDeployed)
		assert.equal(symbol, symbolDeployed)
		assert.equal(name, nameDeployed)
	})
})

contract('Token.transfer', function(accounts) {
	it('Должен срабаотывать, если unpause', async () => {
		const amount = 10;
		const first = accounts[0];
		const second = accounts[1];

		const token = await Token.deployed()
		await token.mint(first, amount)

		const firstBalanceStart = (await token.balanceOf(first)).toNumber()
		const secondBalanceStart = (await token.balanceOf(second)).toNumber()

		await token.unpause()
		await token.transfer(second, amount, {from: first})

		const firstBalanceEnd = (await token.balanceOf(first)).toNumber()
		const secondBalanceEnd = (await token.balanceOf(second)).toNumber()

		assert.equal(firstBalanceEnd, firstBalanceStart - amount)
		assert.equal(secondBalanceEnd, secondBalanceStart + amount)
	})

	it('Не должен срабатывать, если pause', () => {
		var token
		var amount = 10;
		var first = accounts[0];
		var second = accounts[1];

		return Token.deployed().then(instance => {
			token = instance;
			return token.pause().then(result => {
				return token.transfer(second, amount, {from: first}).then(()=>
					assert.throw('Не должен давать перевести токены'),
					e => assert.isAtLeast(e.message.indexOf('revert'), 0)
				)
			})
		})
	})

	it('Не должен срабатывать, если не хватает средств', async () => {

		const amount = 10;
		const first = accounts[0];
		const second = accounts[1];

		const token = await Token.deployed()
		await token.mint(first, amount)
		await token.unpause()

		try {
			await token.transfer(second, amount * 2, {from: first})
		} catch(e) {
			assert.isAtLeast(e.message.indexOf('revert'), 0)
		}
	})
})

contract('Token.transferFrom', function(accounts) {
	it('Должен срабатывать, если есть права на перевод такого количества средств', async () => {

		const amount = 10;
		const first = accounts[0];
		const second = accounts[1];
		const third = accounts[2];

		const token = await Token.deployed()
		await token.mint(first, amount)
		await token.unpause()

		const firstBalanceStart = (await token.balanceOf(first)).toNumber()
		const secondBalanceStart = (await token.balanceOf(second)).toNumber()

		await token.approve(third, amount, {from: first})
		await token.transferFrom(first, second, amount, {from: third})

		const firstBalanceEnd = (await token.balanceOf(first)).toNumber()
		const secondBalanceEnd = (await token.balanceOf(second)).toNumber()

		assert.equal(firstBalanceEnd, firstBalanceStart - amount)
		assert.equal(secondBalanceEnd, secondBalanceStart + amount)
	})

	it('Не должен срабатывать, если нет прав на перевод такого количества средств', async () => {

		const amount = 10;
		const first = accounts[0];
		const second = accounts[1];
		const third = accounts[2];

		const token = await Token.deployed()
		await token.mint(first, amount)
		await token.approve(third, amount, {from: first})

		try {
			await token.transferFrom(first, second, amount * 2, {from: third})
		} catch(e) {
			assert.isAtLeast(e.message.indexOf('revert'), 0)
		}
	})
})

contract('Token.approve, Token.allowance', function(accounts) {
	it('Должен срабатывать, если есть права на перевод такого количества средств', async () => {

		const amount = 10;
		const first = accounts[0];
		const second = accounts[1];
		const token = await Token.deployed()

		await token.approve(second, amount, {from: first})

		const allowance = (await token.allowance(first, second)).toNumber()

		assert.equal(amount, allowance, 'Реальные и делегированные права на перевод токенов не совпадают')
	})
})

contract('Token.increaseApproval, Token.decreaseApproval', function(accounts) {
	it('Должен увеличивать и снижать количество токенов для использования', async () => {

		const token = await Token.deployed()

		const amount = 1000
		const first = accounts[0]
		const second = accounts[1]

		let allowanceStart = await token.allowance(first, second)
		allowanceStart = allowanceStart.toNumber()

		await token.increaseApproval(second, amount)

		let allowanceIncreased = await token.allowance(first, second)
		allowanceIncreased = allowanceIncreased.toNumber()

		assert.equal(allowanceIncreased, allowanceStart + amount, 'увеличенное количество не совпадает')

		await token.decreaseApproval(second, amount/2)

		let allowanceDecreased = await token.allowance(first, second)
		allowanceDecreased = allowanceDecreased.toNumber()

		assert.equal(allowanceDecreased, allowanceIncreased - (amount/2), 'сниженное количество не совпадает')

		await token.decreaseApproval(second, amount*2)

		let allowanceEnd = await token.allowance(first, second)
		allowanceEnd = allowanceEnd.toNumber()

		assert.equal(allowanceEnd, 0, 'сниженное больше 0 не равно нулю')
	})
})

contract('Token.burn', function(accounts) {
	it('Должен сжигать токены со счета отправителя и вычитать их из totalSupply', async () => {

		const token = await Token.deployed()
		const burner = accounts[0]
		const amount = 100

		await token.mint(burner, amount)

		const totalSupplyStart = (await token.totalSupply()).toNumber()
		const burnerBalanceStart = (await token.balanceOf(burner)).toNumber()

		await token.burn(amount, {from: burner})

		const totalSupplyEnd = (await token.totalSupply()).toNumber()
		const burnerBalanceEnd = (await token.balanceOf(burner)).toNumber()

		assert.equal(burnerBalanceEnd, burnerBalanceStart - amount, 'сожглись не все токены')
		assert.equal(totalSupplyEnd, totalSupplyStart - amount, 'токены не вычлись из totalSupply')
	})
})


contract('Token.unpause, Token.pause', function(accounts) {
	it('Должен включать перевод токенов', async () => {

		const token = await Token.deployed()
		await token.unpause()
		const paused = await token.paused()

		assert(paused, 'трансфер токенов не сработал')
	})

	it('Должен заморозить перевод токенов', async () => {

		const token = await Token.deployed()
		await token.pause()
		const paused = await token.paused()

		assert(!paused, 'трансфер токенов не сработал')
	})

	it('Должен падать, если вызвал не owner', async () => {

		const token = await Token.deployed()
		const notOwner = accounts[1]

		token.unpause({from: notOwner})
		.then(assert.fail)
		.catch(e => assert(true))
	})
})

contract('Token.mint', function(accounts) {
	it('Должен выпускать токены', async () => {

		const token = await Token.deployed()
		const first = accounts[0]
		const amount = 100

		const firstBalanceStart = (await token.balanceOf(first)).toNumber()

		await token.mint(first, amount)

		const firstBalanceEnd = (await token.balanceOf(first)).toNumber()

		assert.equal(firstBalanceEnd, firstBalanceStart + amount)
	})
})

contract('Token.finishMinting', function(accounts) {
	it('Должен запрещать дальнейший выпуск токенов', async () => {

		const token = await Token.deployed()
		const first = accounts[0]

		await token.mint(first, 1)
		await token.finishMinting()

		try{
			await token.mint(first, 1)
		} catch(e) {
			assert.isAtLeast(e.message.indexOf('revert'), 0)
		}
	})
})