"use strict";

const gulp = require("gulp");

function moveToAssets() {
	return gulp
	.src("./node_modules/@fancyapps/fancybox/dist/*")
	.pipe(gulp.dest("./assets/fancybox/"))
}

const build = gulp.series(moveToAssets);

exports.default = build;
