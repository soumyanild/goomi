const i18n = require('i18n');

i18n.configure({
    locales: ['en', 'az'], // List of supported languages
    defaultLocale: 'en',   // Default language
    directory: __dirname + '/locales', // Directory where translation files are stored
});

module.exports = i18n;