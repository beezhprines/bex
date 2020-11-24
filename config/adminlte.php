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

    "title" => "Beauty Expert",
    "title_prefix" => "",
    "title_postfix" => "",

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

    "use_ico_only" => false,
    "use_full_favicon" => false,

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

    "logo" => "Beauty Expert",
    "logo_img" => null,
    "logo_img_class" => "brand-image img-circle elevation-3",
    "logo_img_xl" => null,
    "logo_img_xl_class" => "brand-image-xs",
    "logo_img_alt" => "Beauty Expert",

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

    "usermenu_enabled" => false,
    "usermenu_header" => false,
    "usermenu_header_class" => "bg-primary",
    "usermenu_image" => false,
    "usermenu_desc" => false,
    "usermenu_profile_url" => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#71-layout
    |
    */

    "layout_topnav" => null,
    "layout_boxed" => null,
    "layout_fixed_sidebar" => true,
    "layout_fixed_navbar" => null,
    "layout_fixed_footer" => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#721-authentication-views-classes
    |
    */

    "classes_auth_card" => "card-outline card-primary",
    "classes_auth_header" => "",
    "classes_auth_body" => "",
    "classes_auth_footer" => "",
    "classes_auth_icon" => "",
    "classes_auth_btn" => "btn-flat btn-primary",

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#722-admin-panel-classes
    |
    */

    "classes_body" => "",
    "classes_brand" => "",
    "classes_brand_text" => "",
    "classes_content_wrapper" => "",
    "classes_content_header" => "",
    "classes_content" => "",
    "classes_sidebar" => "sidebar-dark-primary",
    "classes_sidebar_nav" => "nav-legacy",
    "classes_topnav" => "navbar-white navbar-light",
    "classes_topnav_nav" => "navbar-expand",
    "classes_topnav_container" => "container",

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#73-sidebar
    |
    */

    "sidebar_mini" => false,
    "sidebar_collapse" => false,
    "sidebar_collapse_auto_size" => false,
    "sidebar_collapse_remember" => false,
    "sidebar_collapse_remember_no_transition" => false,
    "sidebar_scrollbar_theme" => "os-theme-light",
    "sidebar_scrollbar_auto_hide" => "l",
    "sidebar_nav_accordion" => true,
    "sidebar_nav_animation_speed" => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#74-control-sidebar-right-sidebar
    |
    */

    "right_sidebar" => false,
    "right_sidebar_icon" => "fas fa-cogs",
    "right_sidebar_theme" => "dark",
    "right_sidebar_slide" => true,
    "right_sidebar_push" => true,
    "right_sidebar_scrollbar_theme" => "os-theme-light",
    "right_sidebar_scrollbar_auto_hide" => "l",

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-urls
    |
    */

    "use_route_url" => false,

    "dashboard_url" => "/",

    "logout_url" => "logout",

    "login_url" => "login",

    "register_url" => "register",

    "password_reset_url" => "password/reset",

    "password_email_url" => "password/email",

    "profile_url" => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#92-laravel-mix
    |
    */

    "enabled_laravel_mix" => true,
    "laravel_mix_css_path" => "css/app.css",
    "laravel_mix_js_path" => "js/app.js",

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#8-menu-configuration
    |
    */

    "menu" => [
        // websites menu
        [
            "text" => "Beauty Expert",
            "url"  => "/",
            "submenu" => [
                [
                    "text" => "Стрела Академия",
                    "url" => "http://sa.ezhprines.com"
                ],
            ],
            "can"  => ["can-owner", "can-host"],
            "topnav" => true
        ],
        [
            "text" => "Beauty Expert",
            "url"  => "/",
            "can"  => ["can-master", "can-marketer", "can-operator", "can-manager"],
            "topnav" => true
        ],

        // master pages
        [
            "text" => "Статистика",
            "route"  => "masters.statistics",
            "icon" => "fa fa-list-alt",
            "can"  => ["can-master"]
        ],

        // marketer pages
        [
            "text" => "Аналитика",
            "route"  => "marketers.analytics",
            "icon" => "fa fa-list-alt",
            "can"  => ["can-marketer"]
        ],
        [
            "text" => "Диаграммы",
            "route" => "marketers.diagrams",
            "icon" => "fa fa-chart-pie",
            "can"  => ["can-marketer"]
        ],

        // operators pages
        [
            "text" => "Статистика",
            "route" => "operators.statistics",
            "icon" => "fa fa-list-alt",
            "can"  => ["can-operator"]
        ],
        [
            "text" => "План продаж",
            "route" => "operators.salesplan",
            "icon" => "fa fa-chart-line",
            "can"  => ["can-operator"]
        ],

        // managers pages
        [
            "text" => "Недельный план",
            "route" => "managers.weekplan",
            "icon" => "fa fa-chart-line",
            "can"  => ["can-manager"]
        ],
        [
            "text" => "Статистика",
            "route" => "managers.statistics",
            "icon" => "fa fa-list-alt",
            "can"  => ["can-manager"]
        ],
        [
            "text" => "Диаграммы",
            "route" => "managers.diagrams",
            "icon" => "fa fa-chart-pie",
            "can"  => ["can-manager"]
        ],
        [
            "text" => "Мониторинг",
            "route" => "managers.monitoring",
            "icon" => "fa fa-binoculars",
            "can"  => ["can-manager"]
        ],
        [
            "text" => "Комиссии",
            "route" => "managers.comissions",
            "icon" => "fa fa-coins",
            "can"  => ["can-manager"]
        ],
        [
            "text" => "Услуги",
            "route" => "masters.services",
            "icon" => "fa fa-hand-holding-water",
            "can"  => ["can-manager"]
        ],
        [
            "text" => "Курсы валют",
            "route" => "managers.currencyRates",
            "icon" => "fa fa-ruble-sign",
            "can"  => ["can-manager"]
        ],

        // owner pages
        [
            "text" => "Учетные записи",
            "icon" => "fa fa-user-cog",
            "submenu" => [
                [
                    "text" => "Мастера",
                    "route" => "masters.index",
                    "icon" => "fa fa-user-tag",
                    "active" => ["masters"]
                ],
                [
                    "text" => "Косметологи",
                    "route" => "cosmetologists.index",
                    "icon" => "fa fa-user-tag",
                    "active" => ["cosmetologists"]
                ],
                [
                    "text" => "Операторы",
                    "route" => "operators.index",
                    'icon' => 'fa fa-headset',
                    "active" => ["operators"]
                ],
                [
                    "text" => "Маркетологи",
                    "route" => "marketers.index",
                    'icon' => 'fa fa-user-edit',
                    "active" => ["marketers"]
                ],
                [
                    "text" => "Менеджеры",
                    "route" => "managers.index",
                    'icon' => 'fa fa-user-tie',
                    "active" => ["managers"]
                ],
            ],
            "can"  => ["can-owner", "can-host"]
        ],

        [
            "text" => "Настройки",
            "icon" => "fa fa-cog",
            "submenu" => [
                [
                    "text" => "Команды",
                    "route" => "teams.index",
                    'icon' => 'fa fa-users',
                    "active" => ["teams"]
                ],
                [
                    "text" => "Города",
                    "route" => "cities.index",
                    'icon' => 'fa fa-city',
                    "active" => ["cities"]
                ],
                [
                    "text" => "Страны",
                    "route" => "countries.index",
                    'icon' => 'fa fa-globe-europe',
                    "active" => ["countries"]
                ],
                [
                    "text" => "Валюты",
                    "route" => "currencies.index",
                    'icon' => 'fa fa-ruble-sign',
                    "active" => ["currencies"]
                ],
                [
                    "text" => "Бонусы",
                    "route" => "configurations.bonuses",
                    'icon' => 'fa fa-percentage',
                    "active" => ["configurations*"]
                ],
                [
                    "text" => "Профиль",
                    "route" => "users.profile",
                    'icon' => 'fa fa-user-circle',
                    "active" => ["users/profile"]
                ]
            ],
            "can"  => ["can-owner", "can-host"]
        ],

        [
            "text" => "Финансы",
            'icon' => 'fa fa-donate',
            "submenu" => [
                [
                    "text" => "Статистика",
                    "route" => "finances.statistics",
                    'icon' => 'fa fa-money-check-alt',
                    "active" => ["finances/statistics"]
                ],
                [
                    "text" => "Расходы",
                    "route" => "finances.customOutcomes",
                    'icon' => 'fa fa-file-invoice-dollar',
                    "active" => ["finances/customOutcomes"]
                ],
                [
                    "text" => "Комиссии",
                    "route" => "managers.comissions",
                    "icon" => "fa fa-coins",
                    "active" => ["managers/comissions"]
                ],
                [
                    "text" => "Выплаты",
                    "route" => "finances.payments",
                    "icon" => "fa fa-money-bill-alt",
                    "active" => ["finances/payments"]
                ],
                [
                    "text" => "Недельный план",
                    "route" => "managers.weekplan",
                    "icon" => "fa fa-chart-line",
                    "active" => ["managers/weekplan"]
                ],
            ],
            "can"  => ["can-owner", "can-host"]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#83-custom-menu-filters
    |
    */

    "filters" => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
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
    | https://github.com/jeroennoten/Laravel-AdminLTE/#91-plugins
    |
    */

    "plugins" => [
        "bootstrap-datepicker" => [
            "active" => true,
            "files" => [
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
                ],
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ru.min.js"
                ],
                [
                    "type" => "css",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css"
                ],
            ]
        ],
        "bootstrap-select" => [
            "active" => true,
            "files" => [
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
                ],
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/i18n/defaults-ru_RU.min.js"
                ],
                [
                    "type" => "css",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css"
                ],
            ]
        ],
        "momentjs" => [
            "active" => true,
            "files" => [
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"
                ],
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/locale/ru.min.js"
                ],
            ]
        ],
        "Toastr" => [
            "active" => true,
            "files" => [
                [
                    "type" => "js",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"
                ],
                [
                    "type" => "css",
                    "asset" => false,
                    "location" => "//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css"
                ],
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#93-livewire
    */

    "livewire" => false,
];
