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


    'before_write_quotation' => [
        'app\\admin\\behavior\\DefaultClientInfo'
    ],
    'before_write_order' => [
        'app\\admin\\behavior\\DefaultClientInfo'
    ],
    'after_write_quotation' => [
        'app\\admin\\behavior\\UpdateItems'
    ],
    'before_copy_quotation' => [
        'app\\admin\\behavior\\PrepareCopy'
    ],
    'after_copy_quotation' => [
        'app\\admin\\behavior\\CopyQuotationItem'
    ],
    'before_write_quotation_item' => [
        'app\\admin\\behavior\\SaveItem',
        'app\\admin\\behavior\\CalculateQuotationPrice'
    ],
    'before_write_order_item' => [
        'app\\admin\\behavior\\SaveItem',
        'app\\admin\\behavior\\CalculateOrderPrice'
    ],
    'before_placeorder' => [
        'app\\admin\\behavior\\PlaceOrder'
    ],
    'after_placeorder' => [
        'app\\admin\\behavior\\PlaceOrderItems'
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
