<?php
/**
 * 商品分发频道数据库安装文件
 */

if (!defined('WPINC')) {
    die;
}

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();

// 商品分类排序表
$table_name = $wpdb->prefix . 'zhuige_shop_category_order';
$sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    term_id bigint(20) NOT NULL,
    parent_id bigint(20) NOT NULL DEFAULT 0,
    sort_order int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (id),
    KEY term_id (term_id),
    KEY parent_id (parent_id)
) $charset_collate;";

// 商品推荐配置表
$table_name = $wpdb->prefix . 'zhuige_shop_recommend';
$sql .= "CREATE TABLE IF NOT EXISTS $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    category_id bigint(20) NOT NULL,
    type varchar(20) NOT NULL,
    title varchar(100) NOT NULL,
    products text NOT NULL,
    sort_order int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (id),
    KEY category_id (category_id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql); 