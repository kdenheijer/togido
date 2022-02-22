import gulp from 'gulp'
import imagemin from 'gulp-imagemin'
import sftp from 'gulp-sftp-up4'
import minimist from 'minimist'

const args = minimist(process.argv.slice(2));

gulp.task('default', () =>
    gulp.src('images/*')
        .pipe(imagemin())
        .pipe(gulp.dest('source/images'))
);

gulp.task('deploy', function () {
    const conn = sftp({
        host: args.server,
        user: args.user,
        pass: args.password,
        port: args.port,
        remotePath: '/www/',
    });

    return gulp.src(['public/*', 'public/**/*'] )
        .pipe(conn);
});
