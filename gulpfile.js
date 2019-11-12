const gulp = require('gulp');
const imagemin = require('gulp-imagemin');
const ftp = require('vinyl-ftp');
const gutil = require('gulp-util');
const minimist = require('minimist');
const args = minimist(process.argv.slice(2));

gulp.task('default', () =>
    gulp.src('images/*')
        .pipe(imagemin())
        .pipe(gulp.dest('source/images'))
);

gulp.task('deploy', function () {
    var remotePath = '/www/';
    var conn = ftp.create({
        host: args.server,
        user: args.user,
        password: args.password,
        log: gutil.log
    });

    gulp.src(['./public/*'])
        .pipe(conn.newer(remotePath))
        .pipe(conn.dest(remotePath));
});
