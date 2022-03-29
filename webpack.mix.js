const mix = require("laravel-mix");

require("laravel-mix-tailwind");

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
mix.copyDirectory('resources/img/', 'public/img/');

// // FontAwesome Main CSS + Webfonts / SVG
mix.copyDirectory(
    'node_modules/@fortawesome/fontawesome-pro/webfonts',
    'public/vendors/fontawesome-pro/webfonts'
);
mix.copyDirectory(
    'node_modules/@fortawesome/fontawesome-pro/svgs',
    'public/vendors/fontawesome-pro/svgs'
);

mix.copy(
    'node_modules/@fortawesome/fontawesome-pro/css/all.min.css',
    'public/vendors/fontawesome-pro/css/all.min.css'
);

mix.js("resources/js/app.js", "public/js/app.js")
    .sass("resources/sass/app.scss", "public/css/app.css")
    .tailwind("./tailwind.config.js")
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}
