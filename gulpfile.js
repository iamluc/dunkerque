var gulp = require('gulp'),
    concat = require('gulp-concat'),
    sass = require('gulp-ruby-sass'),
    uglify = require('gulp-uglify');

/**
 * Config
 */
var config = {
    bowerDir: './bower_components',
    assetsSrc: './app/Resources/assets',
    assetsDest: './web/assets'
};

config.fonts = {
    src: config.bowerDir + '/bootstrap-sass-official/assets/fonts/bootstrap/*',
    dest: config.assetsDest + '/fonts/bootstrap'
};

config.sass = {
    src: config.assetsSrc + '/scss/main.scss',
    dest: config.assetsDest + '/css',
    loadPath: [
        config.bowerDir + '/bootstrap-sass-official/assets/stylesheets'
    ]
};

config.js = {
    src: [
        config.bowerDir + '/jquery/dist/jquery.js',
        config.bowerDir + '/bootstrap-sass-official/assets/javascripts/bootstrap.js',
        config.assetsSrc + '/js/*.js'
    ],
    dest: config.assetsDest + '/js'
};

/**
 * Tasks
 */
gulp.task('fonts', function() {
    return gulp
        .src(config.fonts.src)
        .pipe(gulp.dest(config.fonts.dest));
});

gulp.task('css', function() {
    return sass(config.sass.src, {
            style: 'compressed',
            loadPath: config.sass.loadPath
        })
        .on('error', sass.logError)
        .pipe(gulp.dest(config.sass.dest));
});

gulp.task('js', function () {
    return gulp
        .src(config.js.src)
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.js.dest));
});

// Rerun the task when a file changes
gulp.task('watch', function() {
    gulp.watch(config.assetsSrc + '/scss/*.scss', ['css']);
    gulp.watch(config.assetsSrc + '/js/*.js', ['js']);
});

gulp.task('default', ['fonts', 'css', 'js']);
