const gulp        = require('gulp');
const browserSync = require('browser-sync').create();
const config      = require('./config');
const plugins     = require('gulp-load-plugins')({
    postRequireTransforms: {
        sass: function(sass) {
            return sass(require('sass'));
        }
    }
});


gulp.task('build:sass', () => {
    return gulp.src(config.sassPath + '/**/*.scss')
        .pipe(plugins.sassLint({config: 'tests/sass-lint.yml'}))
        .pipe(plugins.sassLint.format())
        .pipe(plugins.sassLint.failOnError())
        .pipe(plugins.sass({
            outputStyle: 'compressed',
            includePaths:[
                ...config.vendor.sass,
                config.sassPath,
            ],
            errLogToConsole: true
        }))
        .pipe(plugins.autoprefixer('last 5 version'))
        .pipe(gulp.dest(config.destPath + '/css'))
        .pipe(browserSync.stream());
});


gulp.task('build:javascript-vendor', () => {
    return gulp.src([...config.vendor.js])
    .pipe(gulp.dest(config.destPath + '/js/vendor'));
});

gulp.task('build:javascript', () => {
    return gulp.src([
        config.jsPath + '/**/_*.js',
        config.jsPath + '/**/*.js',
        config.jsPath + '/_*.js',
        config.jsPath + '/*.js',
    ])
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

gulp.task('build',  gulp.series('clean', gulp.parallel(
    'build:sass',
    'build:javascript-vendor',
    'build:javascript',
    'build:image'
)));

gulp.task('default', gulp.series('build'));
