(function () {
	var box = document.querySelector('.slider__container');
	var boxInner = box.querySelector('.slider__container-inner');
	var buttonPrev = document.querySelector('.button__prev');
	var buttonNext = document.querySelector('.button__next');
	var items = document.querySelectorAll('.slider__star');
	var texts = document.querySelectorAll('.slider__text');
	var itemWidthWithMargin = null;
	var shift = null;
	var step = 0;

	/*
	* Ниже параметры, с помощью которых можно контроллировать слайдер
	* */
	var startItemIndex = 3; // Элемент, который будет в центре слайдера при инициализации
	var margin = 70; // Расстояние между элементами

	var currentItemIndex = startItemIndex;

	// Инициализируем слайдер
	var initSlider = function () {
		// Ставим margin для элементов
		[].forEach.call(items, function (item) {
			item.style.margin = '0 ' + margin + 'px';
		});

		var itemWidth = items[0].childNodes[1].clientWidth;

		// Скэйлим элемент и показываем описание
		items[startItemIndex].classList.add('slider__star--current');
		texts[startItemIndex].classList.add('slider__text--current');

		// Ширина элемента вместе с марджинами
		itemWidthWithMargin = margin * 2 + itemWidth;

		// Смещение для transform = ширина элемента + марджины
		shift = itemWidthWithMargin;

		calculateBoxMargin();
	};

	// Вычисляем смещение (margin) для контейнреа с элементами
	var calculateBoxMargin = function () {
		var boxWidth = box.clientWidth;

		var totalItemsWidth = itemWidthWithMargin * startItemIndex;
		boxInner.style.marginLeft = -(totalItemsWidth + (-(boxWidth - itemWidthWithMargin) / 2)) + 'px';
	};

	// Инициализируем слайдер
	window.addEventListener('load', function (ev) {
		initSlider();
	});

	buttonNext.addEventListener('click', function (evt) {
		evt.preventDefault();
		if (currentItemIndex === items.length - 1) {
			boxInner.style.transform = 'translate(' + (shift * startItemIndex - 1) + 'px)';
			step = startItemIndex;
			texts[currentItemIndex].classList.remove('slider__text--current');
			items[currentItemIndex].classList.remove('slider__star--current');
			currentItemIndex = 0;
			items[currentItemIndex].classList.add('slider__star--current');
			texts[currentItemIndex].classList.add('slider__text--current');
			return;
		}
		step--;
		texts[currentItemIndex].classList.remove('slider__text--current');
		items[currentItemIndex].classList.remove('slider__star--current');
		items[++currentItemIndex].classList.add('slider__star--current');
		texts[currentItemIndex].classList.add('slider__text--current');
		boxInner.style.transform = 'translate(' + shift * step + 'px)';
	});

	buttonPrev.addEventListener('click', function (evt) {
		evt.preventDefault();
		if (currentItemIndex === 0 ) {
			boxInner.style.transform = 'translate(' + (-shift * (items.length - startItemIndex - 1)) + 'px)';
			step = -(items.length - startItemIndex - 1);
			texts[currentItemIndex].classList.remove('slider__text--current');
			items[currentItemIndex].classList.remove('slider__star--current');
			currentItemIndex = items.length - 1;
			items[currentItemIndex].classList.add('slider__star--current');
			texts[currentItemIndex].classList.add('slider__text--current');
			return;
		}
		step++;
		texts[currentItemIndex].classList.remove('slider__text--current');
		items[currentItemIndex].classList.remove('slider__star--current');
		items[--currentItemIndex].classList.add('slider__star--current');
		texts[currentItemIndex].classList.add('slider__text--current');
		boxInner.style.transform = 'translate(' + shift * step + 'px)';
	});

	// Отслеживаем изменение размера экрана и пересчитываем marginLeft
	// new window.resizeSensor.create(box, function(){
	// 	calculateBoxMargin();
	// });
})();
