<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用行为扩展定义文件
return [
    // 应用结束
    'app_end'      => [
        'app\\admin\\behavior\\AdminLog',
    ],
    'after_write_client'  => [
        'app\\admin\\behavior\\AddContact'
    ],
    'after_write_product' => [
        'app\\admin\\behavior\\AddDescription'
    ],
    'after_write_variety' => [
        'app\\admin\\behavior\\AddDescription'
    ],
    'after_write_package' => [
        'app\\admin\\behavior\\AddDescription'
    ],
    'after_write_accessory' => [
        'app\\admin\\behavior\\AddDescription'
    ],
    'after_write_carton' => [
        'app\\admin\\behavior\\AddDescription'
    ],

    'after_write_page' => [
        'app\\admin\\behavior\\MultiLanguageContent'
    ],
    'after_write_blog' => [
        'app\\admin\\behavior\\MultiLanguageContent',
        'app\\admin\\behavior\\MultiLanguageImages',
        'app\\admin\\behavior\\RelateTags'
    ],
    'after_write_category' => [
        'app\\admin\\behavior\\MultiLanguageContent'
    ],
    'after_write_product_page' => [
        'app\\admin\\behavior\\MultiLanguageContent',
        'app\\admin\\behavior\\MultiLanguageImages',
        'app\\admin\\behavior\\RelateTags'
    ],
    'after_write_banner' => [
        'app\\admin\\behavior\\MultiLanguageImages'
    ],
    'after_write_widget' => [
        'app\\admin\\behavior\\MultiLanguageContent',
    ],
    'after_write_tag' => [
        'app\\admin\\behavior\\MultiLanguageContent',
    ],
    'after_add_menu' => [
        'app\\admin\\behavior\\AddCategoryChild'
    ],
];
