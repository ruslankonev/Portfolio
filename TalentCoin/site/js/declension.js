'use strict';

(function () {
	var getDeclension = function (number, declensions, lang) {
		// Возвращает множественное или единственное число для getDeclension
		var getPlural = function (plural, lang) {
			switch (lang) {
				case 'en':
				case 'de':
				case 'nl':
				case 'se':
				case 'us':
					return plural === 1 ? 1 : 0;

				case 'fr':
					return plural > 1 ? 0 : 1;

				case 'ru':
				case 'ua':
					plural %= 100;
					return (5 <= plural && plural <= 20) ? 2 : ((1 === (plural %= 10)) ? 0 : ((2 <= plural && plural <= 4) ? 1 : 2));

				default:
					return 0;
			}
		};

		// Round number
		//number = Math.trunc(number); // For ES7 only
		number = ~~number; // For ES5
		declensions = declensions.split(',');
		var plural = getPlural(number, lang);
		var cnt = declensions.length;
		return {
			time: number,
			units: (cnt > 0 && plural < cnt) ? declensions[plural] : ''
		};
	};

	window.getDeclension = getDeclension;
})();
