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
    .js('resources/js/app.js', 'public/js').vue()
    .js('resources/js/multiselect.js', 'public/js').vue()
    .webpackConfig({
        output: {
            filename: '[name].js',
            chunkFilename: 'js/chunks/[name].[chunkhash].js'
        },
    }).version();

if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true
                },
                output: {
                    comments: false
                }
            }
        }
    });
}
