<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
     */

    'title' => 'Registros',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-favicon
    |
     */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-logo
    |
     */

    'logo' => '<b>AERO</b>',
    'logo_img' => 'img/logomini.png',
    'logo_img_class' => 'brand-image elevation-3 img-thumbnail',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    //'logo_img_alt' => 'AdminLTE',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-user-menu
    |
     */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-secondary',
    'usermenu_image' => true,
    'usermenu_desc' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-layout
    |
     */

    'layout_topnav' => null,
    'layout_topnav_right' => true,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-classes
    |
     */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-3',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-sidebar
    |
     */

    'sidebar_mini' => true,
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-control-sidebar-right-sidebar
    |
     */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-urls
    |
     */

    'use_route_url' => false,

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'login_url' => 'login',

    'register_url' => 'register',

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    'profile_url' => 'admin/view/profile',

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-laravel-mix
    |
     */

    'enabled_laravel_mix' => true,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-menu
    |
     */

    'menu' => [
        // ['header' => 'account_settings'],
        // ['header' => 'modules'],
        [
            'text' => 'Cadastro',
            'icon' => 'fas fa-fw fa fa-save',
            'submenu' => [
                [
                    'text' => 'Departamento',
                    'url' => 'register/sector',
                ],
                [
                    'text' => 'Local',
                    'url' => 'register/local',
                ],
                [
                    'text' => 'Função',
                    'url' => 'register/function',
                ],
                [
                    'text' => 'Procedimentos',
                    'url' => 'register/procedure',
                ],
                
            ],
        ],
        
        [
            'text' => 'Registros',
            'icon' => 'fas fa-fw fa fa-clipboard',
            'submenu' => [
                
                [
                    'text' => 'Lista de Registros',
                    'url' => 'occurrence/list/occurrence',
                ],
            ],
        ],
        [
            'text' => 'Formulários',
            'icon' => 'fas fa-fw fa fa-clipboard',
            'submenu' => [
                [
                    'text' => 'Reunião',
                    'icon' => 'fas fa-fw fa fa-clipboard',
                    'url' => 'event/list/meeting'
                ],
                [
                    'text' => 'Relatório de Turno',
                    'icon' => 'fas fa-fw fa fa-clipboard',
                    'url' => 'event/list/shiftreport'
                ],
                [
                    'text' => 'Conferências das suítes',
                    'icon' => 'fas fa-fw fa fa-clipboard',
                    'url' => 'event/check_suite'
                ],
                [
                    'text' => 'Vistorias das suítes',
                    'icon' => 'fas fa-fw fa fa-clipboard',
                    'url' => 'event/inspection_suite'
                ],
                [
                    'text' => 'Diário de Obras',
                    'icon' => 'fas fa-fw fa fa-clipboard',
                    'url' => 'event/work_diary'
                ],
            ],  
        ],
        [
            'text' => 'Admin',
            'url' => '#',
            'icon' => 'fas fa-fw fa-users-cog',
            'submenu' => [
                [
                    'text' => 'User',
                    'url' => 'admin/list/user',
                ],
                [
                    'text' => 'profile',
                    'url' => 'admin/list/profile',
                ],
            ],
        ],
        [
            'text' => 'Notificações',
            'url' => 'notification',
            'icon' => 'ml-1 mr-1 fas fa-bell bags_notification',
            'badge' => '3'
        ],
        ['header' => 'documentation'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#612-menu-filters
    |
     */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#613-plugins
    |
     */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/list.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/select2-bootstrap4-theme/select2-bootstrap4.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'JqueryMask',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js',
                ],
            ],
        ],
        [
            'name' => 'JqueryMaskMoney',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js',
                ],
            ],
        ],
        [
            'name' => 'JqueryValidate',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        [
            'name' => 'scriptListUser',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/list_user.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateUser',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/create_user.js',
                ],
            ],
        ],
        [
            'name' => 'scriptEditUser',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/edit_user.js',
                ],
            ],
        ],
        [
            'name' => 'scriptEditUserProfile',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/edit_user_profile.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateSector',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/sector/sector_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateSector',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/sector/sector_update.js',
                ],
            ],
        ],  
        [
            'name' => 'scriptListSector',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/sector/sector_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateFunction',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/function/function_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateFunction',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/function/function_update.js',
                ],
            ],
        ],  
        [
            'name' => 'scriptListFunction',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/function/function_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateProcedure',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/procedure/procedure_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateProcedure',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/procedure/procedure_update.js',
                ],
            ],
        ],  
        [
            'name' => 'scriptListProcedure',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/procedure/procedure_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateLocal',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/local/local_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateLocal',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/local/local_update.js',
                ],
            ],
        ],  
        [
            'name' => 'scriptListLocal',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/register/local/local_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateOccurrence',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/occurrence/occurrence_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptShowOccurrence',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/occurrence/occurrence_show.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateOccurrence',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/occurrence/occurrence_update.js',
                ],
            ],
        ],
        [
            'name' => 'scriptListOccurrence',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/occurrence/occurrence_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateMeeting',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/meeting/meeting.js',
                ],
            ],
        ],
        [
            'name' => 'scriptShiftReportList',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/shiftReport/shiftReport_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptShiftReport',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/shiftReport/shiftReport.js',
                ],
            ],
        ],
        [
            'name' => 'scriptShiftReportUpdate',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/shiftReport/shiftReportUpdate.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateMeeting',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/meeting/meeting_update.js',
                ],
            ],
        ],
        [
            'name' => 'scriptViewMeeting',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/meeting/meeting_view.js',
                ],
            ],
        ],
        //CHECK SUITE
        [
            'name' => 'scriptListCheckSuite',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/check_suite/check_suite_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateCheckSuite',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/check_suite/check_suite_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateCheckSuite',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/check_suite/check_suite_update.js',
                ],
            ],
        ],
        //INSPECTION SUITE
        [
            'name' => 'scriptListInspectionSuite',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/inspection_suite/inspection_suite_list.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateInspectionSuite',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/inspection_suite/inspection_suite_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateInspectionSuite',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/inspection_suite/inspection_suite_update.js',
                ],
            ],
        ],
        //DIARIO DE OBRAS
        [
            'name' => 'scriptListWorkDiary',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/work_diary/work_diary_list.js',
                ],
            ],
        ],
        
        [
            'name' => 'scriptCreateWorkDiary',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/work_diary/work_diary_create.js',
                ],
            ],
        ],
        [
            'name' => 'scriptUpdateWorkDiary',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/work_diary/work_diary_update.js',
                ],
            ],
        ],
        [
            'name' => 'scriptViewWorkDiary',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/work_diary/work_diary_view.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreateProfile',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/create_profile.js',
                ],
            ],
        ],
        [
            'name' => 'scriptCreatePermission',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/modules/admin/create_permission.js',
                ],
            ],
        ],
        [
            'name' => 'scriptDashboard',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/dashboard.js',
                ],
            ],
        ],
        [
            'name' => 'scriptNotification',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery/notification.js',
                ],
            ],
        ],
    ],
];
