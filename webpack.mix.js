// DO NOT FORGET TO DO -> NPM INSTALL AGAIN (older version of laravel-mix)
const mix = require('laravel-mix');

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

mix
    .options({
        extractVueStyles: true
    })
    .copy('resources/fonts', 'public/fonts')
    .sass('resources/sass/app.scss', 'public/css/app.css')
    .combine([
        'public/css/app.css',
        'resources/css/fontello.css'
    ], 'public/css/app.css')
    .js('resources/js/app.js', 'public/js')
    .extract(['vue', 'vue-router', 'element-ui'])
    .disableNotifications()
    .webpackConfig({
        devtool: 'inline-source-map',
        output: {
            filename: '[name].js',
            chunkFilename: 'js/[name].js',
        },
        plugins: [
        ],
        resolve: {
            alias: {
                '@': path.resolve('resources/js'),
                'img': path.resolve('resources/img'),
                'sass': path.resolve('resources/sass'),
                'components': path.resolve('resources/js/components'),
                'helpers': path.resolve('resources/js/helpers'),
                'lang': path.resolve('resources/js/lang'),
                'layouts': path.resolve('resources/js/layouts'),
                'middlewares': path.resolve('resources/js/middlewares'),
                'mixins': path.resolve('resources/js/mixins'),
                'store': path.resolve('resources/js/store'),
                'views': path.resolve('resources/js/views')
            }
        }
    })
    .version();

if (!mix.inProduction()) {
    mix.sourceMaps();
}