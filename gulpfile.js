require('dotenv').config()

var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var config      = require('./config');
var plugins     = require('gulp-load-plugins')({
    postRequireTransforms: {
        sass: function(sass) {
            return sass(require('node-sass'));
        }
    }
});

var sassLintHandler = (err) => {
    plugins.notify.onError({
        title: 'SCSS Linter failed!',
        message: '<%= error.message %>',
    })(err);
    this.emit('end');
};

gulp.task('build:sass', () => {
    return gulp.src(config.sassPath + '/**/*.scss')
        .pipe(plugins.sassLint({config: '.sass-lint.yml'}))
        .pipe(plugins.sassLint.format())
        .pipe(plugins.plumber({errorHandler: sassLintHandler}))
        .pipe(plugins.sassLint.failOnError())
        .pipe(plugins.plumber.stop())
        .pipe(plugins.sass({
            outputStyle: 'compressed',
            includePaths:[
                ...config.vendor.sass,
                config.sassPath,
            ],
            errLogToConsole: true
        }).on('error', plugins.notify.onError(error => {
            return `Error: ${error.message}`;
        })))
        .pipe(plugins.autoprefixer('last 5 version'))
        .pipe(gulp.dest(config.destPath + '/css'))
        .pipe(browserSync.stream());
});


gulp.task('build:javascript', () => {
    return gulp.src([
        ...config.vendor.js,
        config.jsPath + '/vendor/**/*.js',
        config.jsPath + '/**/_*.js',
        config.jsPath + '/**/*.js',
        config.jsPath + '/_*.js',
        config.jsPath + '/*.js',
    ])
    .pipe(plugins.concat('cfu.js'))
    .pipe(gulp.dest(config.destPath + '/js'))
    .pipe(plugins.rename('cfu.min.js'))
    .pipe(plugins.uglify({mangle: {toplevel: true}}))
    .pipe(gulp.dest(config.destPath + '/js'));
});


gulp.task('build:image', () => {
    return gulp.src([
        config.imgPath + '/**/*'
    ])
    .pipe(gulp.dest(config.destPath + '/img'));
});


gulp.task('clean', function () {
    return gulp.src([
            config.destPath
        ], {read: false, allowEmpty: true})
    .pipe(plugins.clean());
});


gulp.task('watch', function(){
    browserSync.init({
        files: [
            '**/*.php',
            '*.php',
            config.destPath + '**/*'
        ],
        proxy: config.devUrl
    });
    gulp.watch(config.sassPath + '/**/*.scss', gulp.series('build:sass'));
    gulp.watch(config.jsPath + '/**/*.js', gulp.series('build:javascript'));
    gulp.watch(config.imgPath + '/**/*', gulp.series('build:image'));
    gulp.watch(config.destPath + '/**/{*.css,*.js}').on('change', browserSync.reload);
});

gulp.task('build:watch', gulp.series('build:sass', 'build:javascript', 'build:image', 'watch'));
gulp.task('build',  gulp.series('clean', gulp.parallel('build:sass', 'build:javascript', 'build:image')));
gulp.task('default', gulp.series('build'));
