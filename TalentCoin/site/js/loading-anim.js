'use sctrict';

(function () {
	// TODO: сделать ООП реализацию с запоминанием текста на кнопке

	window.loadingAnim = {

		// Показываем анимацию загрузки на кнопке
		showLoadingAnimation: function (button) {
			var loadingBlock = document.createElement('div');
			var loading = document.createElement('div');
			loadingBlock.classList.add('loading-block');
			loading.classList.add('loading');
			loadingBlock.appendChild(loading);
			button.appendChild(loadingBlock);
		},

		// Скрываем анимацию загрузки на кнопке
		hideLoadingAnimation: function (button) {
			button.removeChild(button.childNodes[0]);
		}
	}
})();
