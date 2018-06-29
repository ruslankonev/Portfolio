var gulp = require('gulp'),
		stylus = require('gulp-stylus'),
		browserSync = require('browser-sync'),
		del = require('del'),  // функция удаления
		run = require('run-sequence'),  // порядок загрузки
		mincss = require('gulp-csso'),  // минификация CSS, но лучше попробывать СОВРЕМЕННЫЙ http://refresh-sf.com/ на основе Clean-CSS
		rename = require("gulp-rename"),  // переименование файлов
		notify = require('gulp-notify'),  // уведомления при ошибках, для W нужен Growl
		imagemin = require('gulp-imagemin'),  // оптимизация img вкл. несколько модулей
		prefixer = require('gulp-autoprefixer'),  // вендорные префиксы от Evil Martians
		uglify = require('gulp-uglify'),  // минификация JS
		svgSprite = require('gulp-svg-sprite'),  // создание спрайтов
		plumber = require('gulp-plumber');  // сборщик ошибок
		concat = require('gulp-concat'); // конкатенация
		sourcemaps = require('gulp-sourcemaps'); // sourcemaps
		i18n = require('gulp-html-i18n');  // internationalization
		rsync = require('gulp-rsync');  // rsync plugin for deploying


// version 1.4 Production [11.04.18]
// time build 91.5s [packages 944]


//	task
//	for
//		~ PRODUCTIONS ~

gulp.task('clean', function() {
	return del(['../app/**', '!../app']  // удаляет все в папке app, при этом не удаляя саму папку app
		,{
			force: true  // начинает удалять за пределами текущей dir
		});
});

gulp.task('copy', function() {
	return gulp.src([
		'fonts/**',
		'img/**',
		'css/fonts.css',
		'video/**',
		'svg-sprites/**',
		'svg-icons/**',
		'*.png',
		'*.svg',
		'php/**',
		'browserconfig.xml',
		'site.webmanifest',
		'.htaccess'
		], {
			base: "."  // что бы копировались целыми папками
		})
	.pipe(gulp.dest('../app'));
});

gulp.task('stylus', function() {
	return gulp.src('stylus/style.styl')
		.pipe(stylus())
		.pipe(prefixer({
			browsers: ['last 2 version'],
			cascade: false
		}))
		.pipe(mincss({
			restructure: false  // что бы анализатор не разбивал и не связывал cелекторы как ему нравится
		}))
		.pipe(rename('style.min.css'))
		.pipe(gulp.dest('../app/css'))
		.pipe(browserSync.reload({stream: true}))
});

gulp.task('img-min', function() {
	return gulp.src("../app/img/**/*.{png,jpg,gif}")
		.pipe(imagemin([
			imagemin.optipng({optimizationLevel: 3}),
			imagemin.jpegtran({progressive: true}),
			imagemin.gifsicle({interlaced: true}),
			imagemin.svgo({plugins: [{removeViewBox: true}]})
		]))
		.pipe(gulp.dest("../app/img"));
});

gulp.task('js-min', function() {
	return gulp.src(['js/*.js', '!js/bundle.min.js'])
			.pipe(uglify())
			.pipe(concat('bundle.min.js'))
			.pipe(gulp.dest('../app/js'))
			.pipe(browserSync.reload({stream: true}))
});

gulp.task('bs', function() {
	browserSync({
		server: {
			baseDir: '../app'  //запускаемся из папки app на 3000 порту
		}
	});
});

gulp.task('i18n', function () {
	return gulp.src('${{opt.name}}$.html')
		.pipe(i18n({
			langDir: './lang',
			filenameI18n: true
		}))
		.pipe(gulp.dest('../app'))
		.pipe(browserSync.reload({stream: true}));
});

gulp.task('wt',['bs'], function() {
	gulp.watch('../app/css/**/*.css', browserSync.reload);
	gulp.watch('../app/*.html', browserSync.reload);
	gulp.watch('../app/js/**/*.js', browserSync.reload);
});

gulp.task('build', function(fn) {
	run('clean', 'copy', 'stylus', 'js-min', 'img-min', 'wt', 'i18n', fn);
});

gulp.task('build-wo-wt', function(fn) {
	run('clean', 'copy', 'stylus', 'js-min', 'img-min', 'i18n', fn);
});



//	task
//	for
//		~ DEVELOPER ~

gulp.task('dev-stylus', function() {
	return gulp.src('stylus/style.styl')
		.pipe(stylus())
		.on('error', notify.onError())  // ловим ошибки .styl и не даем упасть серверу
		.pipe(gulp.dest('css'))
		.pipe(prefixer({
			browsers: ['last 2 version'],
			cascade: false
		}))
		.pipe(rename('no-prefix.css'))
		.pipe(gulp.dest('css'))
		.pipe(mincss({
			restructure: false
		}))
		.pipe(rename('style.min.css'))
		.pipe(gulp.dest('css'))
		.pipe(browserSync.reload({stream: true}))
});

gulp.task('dev-js-min', function() {
	return gulp.src(['js/*.js', '!js/bundle.min.js'])
		.pipe(sourcemaps.init())
		.pipe(uglify())
		.pipe(concat('bundle.min.js'))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('js'))
		.pipe(browserSync.reload({stream: true}))
});

gulp.task('dev-bs', function() {
	browserSync({
		server: {
			baseDir: '.'
		},
		ui: {
			port: 3336
		},
		port: 3333,
		logPrefix: "BS-DEV",
		logConnections: true
	})
});

gulp.task('dev-i18n', function () {
	return gulp.src('${{opt.name}}$.html')
		.pipe(i18n({
			langDir: './lang',
			trace: true,
			filenameI18n: true
		}))
		.pipe(gulp.dest(''))
		.pipe(browserSync.reload({stream: true}));
});

gulp.task('dev', ['dev-bs', 'dev-stylus', 'dev-js-min', 'dev-i18n'],  function() {
	gulp.watch('stylus/**/*.styl', ['dev-stylus']);
	gulp.watch('*.html', ['dev-i18n']);
	gulp.watch('lang/**/*.json', ['dev-i18n']);
	gulp.watch('js/**/*.js', ['dev-js-min']);
	gulp.watch('svg-icons/**', browserSync.reload);
});

gulp.task('spr-svg', function() {
	return gulp.src([
		'svg-icons/svg-*.svg',
		'svg-icons/s-*.svg'
	])
	.pipe(svgSprite({
		shape: {
			dimension: {
				maxWidth: 500,
				maxHeight: 500
			},
			spacing: {
				padding: 0
			}
		},
		mode: {
			symbol: {
				dest: '.',  // по умолчанию создается папка css/svg/symbol, сейчас папка не создается
				sprite: 'sprite-symbol.svg'  // навзание итогово спрайта
			}
		}
	}))
	.pipe(gulp.dest('svg-sprites'));
});

gulp.task('deploy', function() {
	return gulp.src('../app')
			.pipe(rsync({
				hostname: '207.148.97.35',
				username: 'root',
				root: '../app',
				destination: '/var/www/admin/data/www/myidm.io',
				progress: true,
				incremental: true,
				relative: true,
				emptyDirectories: true,
				recursive: true,
				clean: true,
				exclude: []
			}));
});

gulp.task('bnd', function (fn) {
	run('build-wo-wt', 'deploy', fn);
});

// gulp.task('spr-bg', function() {
// 	return gulp.src('svg-icons/**/*.svg')
// 		.pipe(svgSprite({
// 			mode: {
// 				css: {
// 					dest: '.',
// 					bust: false,
// 					sprite: 'sprite.svg',
// 					layout: 'vertical',
// 					prefix: '$-',
// 					dimenssions: true,
// 					render: {
// 						styl: {
// 							dest: 'spr-bg.styl'
// 						}
// 					}
// 				}
// 			}
// 		}))
// 		.pipe(gulp.dest('svg-sprites-bg'));
// });
