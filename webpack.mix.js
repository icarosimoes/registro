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

mix.react('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/js/Jquery/global.js', 'public/js/jquery')
    .js('resources/js/Jquery/modules/admin/list.js', 'public/js/jquery/modules/admin')
    // admin module
    .js('resources/js/Jquery/modules/admin/create_user.js', 'public/js/jquery/modules/admin')
    .js('resources/js/Jquery/modules/admin/edit_user.js', 'public/js/jquery/modules/admin')
    .js('resources/js/Jquery/modules/admin/edit_user_profile.js', 'public/js/jquery/modules/admin')
    .js('resources/js/Jquery/modules/admin/create_profile.js', 'public/js/jquery/modules/admin')
    .js('resources/js/Jquery/modules/admin/create_permission.js', 'public/js/jquery/modules/admin')
    //occurrence
    .js('resources/js/Jquery/modules/occurrence/occurrence_create.js', 'public/js/jquery/modules/occurrence')
    .js('resources/js/Jquery/modules/occurrence/occurrence_update.js', 'public/js/jquery/modules/occurrence')
    .js('resources/js/Jquery/modules/occurrence/occurrence_list.js', 'public/js/jquery/modules/occurrence')
    //meeting
    .js('resources/js/Jquery/modules/meeting/meeting.js', 'public/js/jquery/modules/meeting')
    .js('resources/js/Jquery/modules/meeting/meeting_update.js', 'public/js/jquery/modules/meeting')
    //shiftReport
    .js('resources/js/Jquery/modules/shiftReport/shiftReport.js', 'public/js/jquery/modules/shiftReport')
    .js('resources/js/Jquery/modules/shiftReport/shiftReportUpdate.js', 'public/js/jquery/modules/shiftReport')
    .js('resources/js/Jquery/modules/shiftReport/shiftReport_list.js', 'public/js/jquery/modules/shiftReport')
    //dashboard
    .js('resources/js/Jquery/dashboard.js', 'public/js/jquery');