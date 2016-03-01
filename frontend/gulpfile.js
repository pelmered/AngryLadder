var gulp 			= require( 'gulp' ),
    postcss 		= require('gulp-postcss'),
	cssnext 		= require('postcss-cssnext'),
	precss 			= require('precss'),
	watch 			= require('gulp-watch'),
	cssnano 		= require('gulp-cssnano'),
	concatCss 		= require('gulp-concat-css');

gulp.task('postcss', function () {
	var processors = [
			cssnext,
			precss
		];
  return gulp.src('./assets/css/src/*.css')
    .pipe(postcss(processors))
    .pipe(concatCss("style.css"))
    .pipe(cssnano())
    .pipe(gulp.dest('./assets/css/'));
});

gulp.watch( './assets/css/src/*.css', function() {
        gulp.start( 'postcss' );
    });

gulp.task('default', ['postcss']);