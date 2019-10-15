"use strict";

const gulp = require("gulp");
const gulpif = require("gulp-if");
const rename = require("gulp-rename");
const yargs = require("yargs");
const webpack = require("webpack");
const webpackStream = require("webpack-stream");

const production = !!yargs.argv.production;
const webpackConfig = (function() {
    let config = require('./webpack.config.js');

    config.mode = production ? 'production' : 'development';
    config.devtool = production ? false : "source-map";

    return config;
})();

function moveVendorToAssets() {
	return gulp
		.src("./node_modules/@fancyapps/fancybox/dist/*")
		.pipe(gulp.dest("./assets/fancybox/"))
}

function webpackBuild() {
	return gulp.src([webpackConfig.entry.public])
        .pipe(webpackStream(webpackConfig), webpack)
        .pipe(gulpif(production, rename({ suffix: ".min" })))
        .pipe(gulp.dest('./assets/'));
}

const build = gulp.series(moveVendorToAssets, webpackBuild);

exports.default = build;
