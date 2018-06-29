'use strict';

(function () {
	var Modal = function (button, modal, display, overlay, cb) {
		this.openButton = button;
		this.modal = modal;
		this.display = display;
		this.overlay = overlay;
		this.cb = cb;
		this._prepareData();
		this._addListeners();
	};

	Modal.prototype._prepareData = function (evt) {
		this.closeButton = this.modal.querySelector('.modal__cross');
	};

	// openButton может быть как HTMLElement, так и NodeList или массивом
	Modal.prototype._addListeners = function () {
		if (this.openButton !== null) {
			if (this.openButton instanceof HTMLElement) {
				this.openButton.addEventListener('click', this.onOpenButtonClick.bind(this));
			} else {
				var addListener = function (button) {
					button.addEventListener('click', this.onOpenButtonClick.bind(this));
				}.bind(this);
				if (this.openButton instanceof NodeList) {
					[].forEach.call(this.openButton, addListener);
				} else if (Array.isArray(this.openButton)) {
					this.openButton.forEach(addListener);
				}
			}
		}
	};

	Modal.prototype.show = function () {
		this.overlay.style.display = 'block';
		this.modal.classList.add(this.display);
		this.closeButton.addEventListener('click', this.onCloseButtonClick.bind(this));
		window.addEventListener('keydown', this.onEscPress.bind(this));
		if (typeof this.cb === 'object' && typeof this.cb.onShow === 'function') {
			this.cb.onShow(this.modal);
		}
	};

	Modal.prototype.hide = function () {
		this.overlay.style.display = 'none';
		this.modal.classList.remove(this.display);
		this.closeButton.removeEventListener('click', this.onCloseButtonClick);
		this.modal.removeEventListener('keydown', this.onEscPress);
		if (typeof this.cb === 'object' && typeof this.cb.onHide === 'function') {
			this.cb.onHide(this.modal);
		}
	};

	Modal.prototype.onOpenButtonClick = function (evt) {
		evt.preventDefault();
		this.show();
	};

	Modal.prototype.onEscPress = function (evt) {
		if (evt.keyCode === 27) {
			evt.preventDefault();
			this.hide();
		}
	};

	Modal.prototype.onCloseButtonClick = function (evt) {
		evt.preventDefault();
		this.hide();
	};

	window.Modal = Modal;
})();
