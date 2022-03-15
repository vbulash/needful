const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    /* CSS */
    .styles([
        'resources/plugins/bootstrap/css/bootstrap.min.css',
        'resources/css/app.css',
        'resources/sass/main.css',
        'resources/sass/dashmix/themes/xsmooth.css',
    ], 'public/css/app.css')
    .copy([
        'resources/plugins/bootstrap/css/bootstrap.min.css.map',
        'resources/sass/dashmix/themes/xsmooth.css.map'
    ], 'public/css')

    /* JS */
    .js([
        //'resources/plugins/jquery/jquery-3.6.0.min.js',
        //'resources/plugins/bootstrap/js/bootstrap.bundle.min.js',
		'resources/plugins/bootstrap-notify/bootstrap-notify.min.js',
        'resources/js/app.js',
        'resources/js/dashmix/app.js',
        'resources/plugins/pusher/pusher.min.js',
    ], 'public/js/app.js')
    .copy([
        'resources/plugins/bootstrap/js/bootstrap.bundle.min.js.map',
		'resources/plugins/jquery/jquery-3.6.0.min.map'
    ], 'public/js')
	.copyDirectory([
		'resources/js/dashmix/modules'
	], 'public/js/modules')

    /* Page JS */
    .js('resources/js/pages/tables_datatables.js', 'public/js/pages/tables_datatables.js')

    /* Media */
    .copyDirectory('resources/img/photos', 'public/media/photos')

    /* Tools */
    .browserSync('localhost:8000')
    //.disableNotifications()

    /* Options */
    .options({
        processCssUrls: true
    });

