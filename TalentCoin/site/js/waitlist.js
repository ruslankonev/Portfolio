'use strict';

(function () {
	// Модалки
	var btnWaitlistNode = document.querySelector('.btn--waitlist-modal');
	var btnWaitlistNode2 = document.querySelector('.btn--waitlist-modal2');
	var linkSubscribeNode = document.querySelector('.link--subscribe');
	var linkSubscribeNode2 = document.querySelector('.link--subscribe2');
	var modalWaitlistNode = document.querySelector('.modal--waitlist');
	var modalWaitlistNodeErc = document.querySelector('.modal--waitlist-erc');
	var modalSubscribeNode = document.querySelector('.modal--subscribe');
	var overlay = document.querySelector('.overlay');

	var modalWaitlist = new Modal(btnWaitlistNode, modalWaitlistNode, 'db', overlay);
	var modalWaitlist2 = new Modal(btnWaitlistNode2, modalWaitlistNode, 'db', overlay);
	var modalWaitlistErc = new Modal(null, modalWaitlistNodeErc, 'db', overlay);
	var modalSubscribe = new Modal(linkSubscribeNode, modalSubscribeNode, 'df', overlay);
	var modalSubscribe2 = new Modal(linkSubscribeNode2, modalSubscribeNode, 'df', overlay);


	// Логика отправки данных
	var joinButton = document.querySelector('.btn--waitlist-modal');
	var joinButtonText = joinButton.textContent;
	var form = document.querySelector('.form--waitlist');
	var formErc = document.querySelector('.form--waitlist-erc');
	var tokenInput = form.querySelector('input[name=ptoken]');
	var gaClientIdInput = form.querySelector('input[name=ga_clientid]');

	var timeout = null;
	var tries = 0;
	var setGaClientId = function (){
		if (timeout !== null) {
			clearTimeout(timeout);
			timeout = null;
		}
		if (typeof(window.ga) === 'function' && typeof(window.ga.getAll) === 'function') {
			gaClientIdInput.value = ga.getAll()[0].get('clientId');
			console.log('GA Client ID is set');
			tries = 0;
			enableJoinButton();
		} else {
			tries++;
			if (tries < 10) {
				timeout = setTimeout(setGaClientId, 500);
			} else {
				console.log('GA Client ID is not set');
				enableJoinButton();
			}
		}
	};

	var pushToGaDataLayer = function (response) {
		window.dataLayer.push({
			'event' : 'joinedWaitlist',
			'userId' : response.userId,  // здесь идет уникальный id нового пользователя присвоенного в бэкенде
			'tokensValue' : tokenInput.value  // здесь идет указанная сумма токенов
		});
	};

	// Делаем кнопку 'Add to whitelist' доступной
	var enableJoinButton = function () {
		window.loadingAnim.hideLoadingAnimation(joinButton);
		joinButton.disabled = false;
		joinButton.classList.remove('head__btn--wait');
		joinButton.textContent = joinButtonText;
	};

	// Делаем кнопку 'Add to whitelist' недоступной
	var disableJoinButton = function () {
		joinButton.disabled = true;
		joinButton.textContent = '';
		joinButton.classList.add('head__btn--wait');
		window.loadingAnim.showLoadingAnimation(joinButton);
	};

	var onFormSendSuccess = function (response) {
		var idInput = formErc.querySelector('input[name=id]');

		idInput.value = response.userId;

		pushToGaDataLayer(response);

		modalWaitlist.hide();
		modalWaitlistErc.show();
	};

	var onFormErcSendSuccess = function () {
		var timeout = setTimeout(function () {
			modalWaitlistErc.hide();
			clearTimeout(timeout);
		}, 5000);
	};

	disableJoinButton();
	setGaClientId();

	new window.AjaxFormSend(form, 'Success', 'Error', {success: onFormSendSuccess});
	new window.AjaxFormSend(formErc, 'Success', 'Error', {success: onFormErcSendSuccess});
})();
