'use strict';

(function () {
	var RESPONSE_TYPE = 'json';
	var CONNECTION_TIMEOUT = 10000;
	var HTTP_OK_STATUS = 200;
	var ERROR_TEXTS = {
		STATUS_NOT_OK: 'Server return status: ',
		CONNECTION_ERROR: 'Error connecting to server',
		CONNECTION_TIMEOUT: 'Server timeout (' + CONNECTION_TIMEOUT + ' ms) has been exceeded.'
	};

	// Инициализирует объект XMLHttpRequest
	var initializeXhr = function (onLoad, onError, responseType) {
		var xhr = new XMLHttpRequest();
		//xhr.responseType = RESPONSE_TYPE;

		xhr.addEventListener('load', function () {
			//console.dir(xhr);
			var response = null;
			if (xhr.status === HTTP_OK_STATUS) {
				if (responseType === 'json') {
					try {
						response = JSON.parse(xhr.response);
					} catch (e) {
						console.log('Response is not JSON');
						window.dataLayer.push({
							event: 'jserror',
							err: e.stack
						});
					}
				} else {
					response = xhr.responseText;
				}
				onLoad(response);
			} else {
				onError(ERROR_TEXTS.STATUS_NOT_OK + xhr.status + ' ' + xhr.statusText);
			}
		});

		xhr.addEventListener('error', function () {
			onError(ERROR_TEXTS.CONNECTION_ERROR);
		});

		xhr.addEventListener('timeout', function () {
			onError(ERROR_TEXTS.CONNECTION_TIMEOUT);
		});

		xhr.timeout = CONNECTION_TIMEOUT;
		return xhr;
	};

	window.backend = {
		// Скачивает данные с сервера
		load: function (url, onLoad, onError, responseType) {
			var xhr = initializeXhr(onLoad, onError, responseType);

			xhr.open('GET', url);
			xhr.send();
		},
		// Загружает данные на сервер
		save: function (url, data, onLoad, onError, responseType) {
			var xhr = initializeXhr(onLoad, onError, responseType);

			xhr.open('POST', url);
			xhr.send(data);
		}
	};
})();
