let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/bootstrap.js', 'public/js')
   .sass('resources/assets/sass/bootstrap.scss', 'public/css')
   .sass('resources/assets/sass/global.scss', 'public/css')
   .sass('resources/assets/sass/welcome.scss', 'public/css')
   .sass('resources/assets/sass/auth.scss', 'public/css')
   .sass('resources/assets/sass/home.scss', 'public/css')
   .sass('resources/assets/sass/profile.scss', 'public/css')
   .sass('resources/assets/sass/settings.scss', 'public/css')
   .sass('resources/assets/sass/messenger.scss', 'public/css')
   .sass('resources/assets/sass/notifications.scss', 'public/css')
   .js('resources/assets/js/axios.js', 'public/js')
   .js('resources/assets/js/jquery.js', 'public/js')
   .js('resources/assets/js/popper.js', 'public/js')
   .js('resources/assets/js/vue.js', 'public/js');
