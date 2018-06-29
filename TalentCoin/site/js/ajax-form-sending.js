'use strict';

(function () {
	var toggleButtonEnablement = function (button) {
		if (!button.disabled) {
			button.disabled = true;
			button.style.cursor = 'not-allowed';
		} else {
			button.disabled = false;
			button.style.cursor = 'pointer';
		}
	};

	var onFormSubmit = function (evt) {
		var form = this.form;
		var submitButton = form.querySelector('button[type=submit]');
		var submitButtonOrigTextContent = submitButton.textContent;

		var setBackToInitialStateTimeout = function () {
			var timeout = setTimeout(function () {
				submitButton.classList.remove('success');
				submitButton.classList.remove('fail');
				submitButton.textContent = submitButtonOrigTextContent;
				toggleButtonEnablement(submitButton);
				clearTimeout(timeout);
			}, 5000);
		};

		try {
			var formData = new FormData(form);
			var successText = this.successText;
			var errorText = this.errorText;
			var cb = this.cb;
			evt.preventDefault();
			toggleButtonEnablement(submitButton);
			submitButton.textContent = '';
			window.loadingAnim.showLoadingAnimation(submitButton);

			var setErrors = function (errors) {
				var placeholders = this.placeholders;
				errors.forEach(function (error) {
					if (error.field !== '') {
						var field = form.querySelector('[name=' + error.field + ']');
						field.classList.add('field-error');
						placeholders.push(field.placeholder);
						field.placeholder = error.message;
					}
				}.bind(this));
			};

			var clearErrors = function (errors) {
				var placeholders = this.placeholders;
				errors.forEach(function (error, i) {
					if (error.field !== '') {
						var field = form.querySelector('[name=' + error.field + ']');
						field.classList.remove('field-error');
						field.placeholder = placeholders[i];
					}
				}.bind(this));
			};

			var onLoadSuccess = function (response) {
				// Выполняем колбэк
				if ((typeof cb === 'object' && typeof cb.success === 'function') && typeof response === 'object') {
					cb.success(response, formData);
				}
				// Сбрасываем форму
				form.reset();
				window.loadingAnim.hideLoadingAnimation(submitButton);
				submitButton.classList.add('success');
				submitButton.textContent = successText;
				clearErrors = clearErrors.bind(this);
				if (this.errors.length > 0) clearErrors(this.errors);
				setBackToInitialStateTimeout();
			}.bind(this);

			var onLoadFail = function (message, response) {
				// Выполняем колбэк
				if ((typeof cb === 'object' && typeof cb.error === 'function') && typeof message === 'string') {
					cb.error(message, response, formData);
				}
				window.loadingAnim.hideLoadingAnimation(submitButton);
				submitButton.classList.add('fail');
				if (response && typeof response === 'object' && typeof response.errors === 'object') {
					this.errors = response.errors;
					submitButton.textContent = response.errors[0].field === '' ? 'DB ' + errorText : errorText;
					setErrors = setErrors.bind(this);
					setErrors(this.errors);
				} else {
					submitButton.textContent = errorText + ': ' + message;
				}
				setBackToInitialStateTimeout();
			}.bind(this);

			var onLoad = function (response) {
				try {
					if (response.message === 'Success') {
						onLoadSuccess(response);
					} else {
						onLoadFail(null, response)
					}
				} catch (e) {
					window.loadingAnim.hideLoadingAnimation(submitButton);
					submitButton.classList.add('fail');
					submitButton.textContent = errorText;
					window.dataLayer.push({
						event: 'jserror',
						err: e.stack
					});
					setBackToInitialStateTimeout();
				}
			};

			var onError = function (message) {
				try {
					onLoadFail(message);
				} catch (e) {
					window.loadingAnim.hideLoadingAnimation(submitButton);
					submitButton.classList.add('fail');
					submitButton.textContent = errorText;
					window.dataLayer.push({
						event: 'jserror',
						err: e.stack
					});
					setBackToInitialStateTimeout();
				}
			};

			window.backend.save(form.action + '?api=1', formData, onLoad.bind(this), onError.bind(this), 'json');
		} catch (e) {
			window.loadingAnim.hideLoadingAnimation(submitButton);
			submitButton.classList.add('fail');
			submitButton.textContent = this.errorText;
			window.dataLayer.push({
				event: 'jserror',
				err: e.stack
			});
			setBackToInitialStateTimeout();
		}

	};

	window.AjaxFormSend = function (form, successText, errorText, cb) {
		this.form = form;
		this.errors = [];
		this.placeholders = [];
		this.successText = successText || 'Sent';
		this.errorText = errorText || 'Error';
		this.cb = cb;
		this.form.addEventListener('submit', onFormSubmit.bind(this));
	};
})();
