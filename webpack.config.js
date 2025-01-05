const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/styles/app.css')
    .enablePostCssLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Enable single runtime chunk (recommended for most projects)
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();