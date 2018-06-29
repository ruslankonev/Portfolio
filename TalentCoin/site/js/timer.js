'use strict';

(function () {
	var path = window.location.pathname;
	var page = path.split("/").pop();

	var declension = {
		en: {
			days: 'days, day',
			hours: 'hours, hour',
			minutes: 'minutes, minute',
			seconds: 'seconds, second'
		},
		ru: {
			days: 'день, дня, дней',
			hours: 'час, часа, часов',
			minutes: 'минута, минуты, минут',
			seconds: 'секунда, секунды, секунд'
		},
		ja: {
			days: '日',
			hours: '時',
			minutes: '分',
			seconds: '秒'
		},
		in: {
			days: 'days, day',
			hours: 'hours, hour',
			minutes: 'minutes, minute',
			seconds: 'seconds, second'
		}
	};

	//var lang = page ? page.match(/([\s\S]*)\.[\s\S]*/)[1] : 'ru';
	var lang = document.querySelector('html').lang;
	var countDownDate = new Date("Sep 18, 2018 00:00:00").getTime();
	var now = new Date().getTime();
	var distance = countDownDate - now;
	var daysBadge = document.querySelector(".hero__timer-item--days");
	var hoursBadge = document.querySelector(".hero__timer-item--hours");
	var minutesBadge = document.querySelector(".hero__timer-item--minutes");
	var secondsBadge = document.querySelector(".hero__timer-item--seconds");

	var setTime = function (days, hours, minutes, seconds, lang) {
		var daysText = window.getDeclension(days, declension[lang].days, lang);
		var hoursText = window.getDeclension(hours, declension[lang].hours, lang);
		var minutesText = window.getDeclension(minutes, declension[lang].minutes, lang);
		var secondsText = window.getDeclension(seconds, declension[lang].seconds, lang);

		daysBadge.childNodes[1].textContent = daysText.time;
		daysBadge.childNodes[3].textContent = daysText.units;
		hoursBadge.childNodes[1].textContent = hoursText.time;
		hoursBadge.childNodes[3].textContent = hoursText.units;
		minutesBadge.childNodes[1].textContent = minutesText.time;
		minutesBadge.childNodes[3].textContent = minutesText.units;
		secondsBadge.childNodes[1].textContent = secondsText.time;
		secondsBadge.childNodes[3].textContent = secondsText.units;
	};

	var getRemainingTime = function (distance) {
		return {
			days: Math.floor(distance / (1000 * 60 * 60 * 24)),
			hours: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
			minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
			seconds: Math.floor((distance % (1000 * 60)) / 1000)
		};
	};

	var remainingTime = getRemainingTime(distance);
	setTime(
		remainingTime.days,
		remainingTime.hours,
		remainingTime.minutes,
		remainingTime.seconds,
		lang
	);

	var x = setInterval(function() {
		var now = new Date().getTime();
		var distance = countDownDate - now;
		var remainingTime = getRemainingTime(distance);
		setTime(
			remainingTime.days,
			remainingTime.hours,
			remainingTime.minutes,
			remainingTime.seconds,
			lang
		);
		if (distance < 0) {
			clearInterval(x);
			setTime(0, 0, 0, 0, lang);
		}
	}, 1000);

})();
