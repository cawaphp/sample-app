/**
 * guild --type=production
 */

var gulp = require('gulp'),
    debug = require('gulp-debug'),
    sourcemaps = require('gulp-sourcemaps'),
    browserSync = require('browser-sync'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    sass = require('gulp-sass'),
    imagemin = require('gulp-imagemin'),
    minifycss = require('gulp-minify-css'),
    gutil = require('gulp-util'),
    concat = require('gulp-concat'),
    size = require('gulp-size'),
    del = require('del');


// http://stackoverflow.com/questions/24236314/gulpjs-combine-two-tasks-into-a-single-task?answertab=active#tab-top
// guild build --type=production
// gulp scripts --env development
// https://github.com/gulpjs/gulp/blob/master/docs/recipes/browserify-uglify-sourcemap.md
// http://webstoemp.com/blog/gulp-setup/

var BUILD_PATH = 'public/static',
    JS_PATH = './assets/**/*.js',
    IMG_PATH = './assets/**/*.{gif,jpg,png,svg}',
    SASS_PATH = './assets/**/*.scss';

// gulp.submodule("./vendor/cawa/module-swagger-server/gulpfile.js", {})
require("module-swagger-server")(gulp, BUILD_PATH);


// clear all compilation file
gulp.task('clean', function () {
    del([BUILD_PATH, "./assets/modules"]);
});


// clear all compilation file
gulp.task('clear', ['clean'], function () {
    del(['./bower_components', "./assets/modules", './vendor']);
});


// build js
gulp.task('js', function () {
    gulp.src(JS_PATH);
    return gulp.src(JS_PATH)
        .pipe(sourcemaps.init())
        .pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(BUILD_PATH))
        .pipe(size({title: 'js'}));
});

// jshint
gulp.task('jshint', function() {
  return gulp.src(JS_PATH)
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe(jshint.reporter('fail'));
});


// image
gulp.task('image', function () {
    return gulp.src(IMG_PATH)
        .pipe(imagemin({
            progressive: true,
            interlaced: true,
            svgoPlugins: [ {removeViewBox:false}, {removeUselessStrokeAndFill:false} ]
        }))
        .pipe(gulp.dest(BUILD_PATH))
        .pipe(size({title: 'image'}));
});


// sass
gulp.task('sass', function () {
    return gulp.src(SASS_PATH)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gutil.env.type === 'production' ? minifycss() : gutil.noop())
        .pipe(gulp.dest(BUILD_PATH))
        .pipe(size({title: 'sass'}))
        .pipe(browserSync.stream());
});


// create a task that ensures the task is complete before reloading browsers
gulp.task('js:watch', ['js'], browserSync.reload);
gulp.task('sass:watch', ['sass'], browserSync.reload);


// serve
gulp.task('serve', ['js', 'sass', 'image'], function() {
    browserSync.init({
        proxy: {
            target: "http://localhost:80"
        },
        logFileChanges: true
    });

    // add browserSync.reload to the tasks array to make all browsers reload after tasks are complete.
    gulp.watch(JS_PATH, ['js:watch']);
    gulp.watch(SASS_PATH, ['sass']);
    gulp.watch(IMG_PATH, ['image']);
});


// default = build
gulp.task('default', ['clean', 'module-swagger-server', 'js', 'sass', 'image']);
