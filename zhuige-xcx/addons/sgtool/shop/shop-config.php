<?php
/**
 * 商品分发频道配置页面
 */

if (!defined('WPINC')) {
    die;
}

/**
 * 添加配置菜单
 */
function zhuige_shop_config_menu() {
    // 使用 'manage_options' 权限，这是最常用的管理页面权限
    // 同时将菜单添加到主菜单而不是子菜单，避免权限继承问题
    add_menu_page(
        '商品分发配置',
        '商品分发配置',
        'manage_options',
        'zhuige-shop-config',
        'zhuige_shop_config_page',
        'dashicons-cart',
        30
    );
}
add_action('admin_menu', 'zhuige_shop_config_menu');

/**
 * 配置页面回调函数
 */
function zhuige_shop_config_page() {
    // 使用标准的 WordPress 权限检查
    if (!current_user_can('manage_options')) {
        wp_die(__('您没有足够的权限访问此页面。'));
    }

    // 添加错误报告，帮助调试
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // 保存配置
    $save_message = '';
    if (isset($_POST['submit'])) {
        try {
            $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
            
            if ($category_id) {
                // 检查 nonce 以增加安全性
                if (!isset($_POST['zhuige_shop_config_nonce']) || !wp_verify_nonce($_POST['zhuige_shop_config_nonce'], 'zhuige_shop_config_action')) {
                    throw new Exception('安全验证失败，请重试。');
                }
                
                // 验证推荐商品数量
                $recommend1_products = isset($_POST['recommend1_products']) && !empty($_POST['recommend1_products']) ? 
                                      array_map('intval', explode(',', $_POST['recommend1_products'])) : array();
                $recommend2_products = isset($_POST['recommend2_products']) && !empty($_POST['recommend2_products']) ? 
                                      array_map('intval', explode(',', $_POST['recommend2_products'])) : array();
                
                // 检查是否选择了2个商品
                if (count($recommend1_products) != 2) {
                    throw new Exception('推荐配置1必须选择2个商品，请重新选择。');
                }
                
                if (count($recommend2_products) != 2) {
                    throw new Exception('推荐配置2必须选择2个商品，请重新选择。');
                }
                
                // 保存推荐配置1
                $recommend1 = array(
                    'title' => isset($_POST['recommend1_title']) ? sanitize_text_field($_POST['recommend1_title']) : '',
                    'desc' => isset($_POST['recommend1_desc']) ? sanitize_textarea_field($_POST['recommend1_desc']) : '',
                    'products' => $recommend1_products
                );
                update_term_meta($category_id, 'recommend1', $recommend1);

                // 保存推荐配置2
                $recommend2 = array(
                    'title' => isset($_POST['recommend2_title']) ? sanitize_text_field($_POST['recommend2_title']) : '',
                    'desc' => isset($_POST['recommend2_desc']) ? sanitize_textarea_field($_POST['recommend2_desc']) : '',
                    'products' => $recommend2_products
                );
                update_term_meta($category_id, 'recommend2', $recommend2);

                $save_message = '<div class="notice notice-success"><p>配置已保存。</p></div>';
            } else {
                $save_message = '<div class="notice notice-error"><p>请选择一个分类。</p></div>';
            }
        } catch (Exception $e) {
            $save_message = '<div class="notice notice-error"><p>错误: ' . esc_html($e->getMessage()) . '</p></div>';
        }
    }

    // 获取一级分类
    $categories = array();
    try {
        $categories = get_terms(array(
            'taxonomy' => 'shop_category',
            'hide_empty' => false,
            'parent' => 0,
            'meta_key' => 'category_sort_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        ));
        
        // 如果 get_terms 返回错误对象，转换为异常
        if (is_wp_error($categories)) {
            throw new Exception($categories->get_error_message());
        }
    } catch (Exception $e) {
        echo '<div class="notice notice-error"><p>获取分类失败: ' . esc_html($e->getMessage()) . '</p></div>';
        $categories = array();
    }

    // 获取当前选中的分类
    $current_category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : (isset($_POST['category_id']) ? intval($_POST['category_id']) : 0);
    
    // 获取当前分类的推荐配置
    $recommend1 = array('title' => '', 'desc' => '', 'products' => array());
    $recommend2 = array('title' => '', 'desc' => '', 'products' => array());
    
    if ($current_category_id) {
        try {
            $term_recommend1 = get_term_meta($current_category_id, 'recommend1', true);
            $term_recommend2 = get_term_meta($current_category_id, 'recommend2', true);
            
            if ($term_recommend1 && is_array($term_recommend1)) {
                $recommend1 = $term_recommend1;
            }
            if ($term_recommend2 && is_array($term_recommend2)) {
                $recommend2 = $term_recommend2;
            }
            
            // 确保 products 是数组
            if (!isset($recommend1['products']) || !is_array($recommend1['products'])) {
                $recommend1['products'] = array();
            }
            if (!isset($recommend2['products']) || !is_array($recommend2['products'])) {
                $recommend2['products'] = array();
            }
        } catch (Exception $e) {
            echo '<div class="notice notice-error"><p>获取推荐配置失败: ' . esc_html($e->getMessage()) . '</p></div>';
        }
    }

    // 获取所有商品
    $products = array();
    try {
        // 修改查询，只获取当前选中分类下的商品
        $args = array(
            'post_type' => 'shop_product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        // 如果有选中的分类，添加分类筛选条件
        if ($current_category_id) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'shop_category',
                    'field' => 'term_id',
                    'terms' => $current_category_id,
                    'include_children' => true // 包含子分类的商品
                )
            );
        }
        
        $products = get_posts($args);
        
        // 如果没有找到商品，显示提示信息
        if (empty($products)) {
            echo '<div class="notice notice-warning"><p>当前分类下没有商品，请先添加商品或选择其他分类。</p></div>';
        }
    } catch (Exception $e) {
        echo '<div class="notice notice-error"><p>获取商品失败: ' . esc_html($e->getMessage()) . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>商品分发配置</h1>
        
        <?php echo $save_message; ?>
        
        <form method="get" action="">
            <input type="hidden" name="page" value="zhuige-shop-config" />
            <select name="category_id" onchange="this.form.submit()">
                <option value="">请选择分类</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($current_category_id, $category->term_id); ?>>
                    <?php echo esc_html($category->name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($current_category_id): ?>
        <form method="post" action="">
            <?php wp_nonce_field('zhuige_shop_config_action', 'zhuige_shop_config_nonce'); ?>
            <input type="hidden" name="category_id" value="<?php echo esc_attr($current_category_id); ?>" />
            
            <h2>推荐配置1</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="recommend1_title">标题</label></th>
                    <td>
                        <input type="text" id="recommend1_title" name="recommend1_title" value="<?php echo esc_attr(isset($recommend1['title']) ? $recommend1['title'] : ''); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="recommend1_desc">推荐说明</label></th>
                    <td>
                        <textarea id="recommend1_desc" name="recommend1_desc" rows="3"><?php echo esc_textarea(isset($recommend1['desc']) ? $recommend1['desc'] : ''); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="recommend1_products">商品</label></th>
                    <td>
                        <div class="product-selector">
                            <select multiple="multiple" id="recommend1_select" style="width: 100%; height: 200px;">
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo esc_attr($product->ID); ?>" <?php echo in_array($product->ID, $recommend1['products']) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($product->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="recommend1_products" id="recommend1_products" value="<?php echo esc_attr(implode(',', $recommend1['products'])); ?>" />
                            <p class="description" style="color: #ff5722; font-weight: bold;">按住 Ctrl (Windows) 或 Command (Mac) 键可以多选，请选择推荐2个商品</p>
                        </div>
                    </td>
                </tr>
            </table>

            <h2>推荐配置2</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="recommend2_title">标题</label></th>
                    <td>
                        <input type="text" id="recommend2_title" name="recommend2_title" value="<?php echo esc_attr(isset($recommend2['title']) ? $recommend2['title'] : ''); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="recommend2_desc">推荐说明</label></th>
                    <td>
                        <textarea id="recommend2_desc" name="recommend2_desc" rows="3"><?php echo esc_textarea(isset($recommend2['desc']) ? $recommend2['desc'] : ''); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="recommend2_products">商品</label></th>
                    <td>
                        <div class="product-selector">
                            <select multiple="multiple" id="recommend2_select" style="width: 100%; height: 200px;">
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo esc_attr($product->ID); ?>" <?php echo in_array($product->ID, $recommend2['products']) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($product->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="recommend2_products" id="recommend2_products" value="<?php echo esc_attr(implode(',', $recommend2['products'])); ?>" />
                            <p class="description" style="color: #ff5722; font-weight: bold;">按住 Ctrl (Windows) 或 Command (Mac) 键可以多选，请选择推荐2个商品</p>
                        </div>
                    </td>
                </tr>
            </table>

            <script>
            jQuery(document).ready(function($) {
                // 监听推荐1的选择变化
                $('#recommend1_select').on('change', function() {
                    var selected = $(this).val() || [];
                    $('#recommend1_products').val(selected.join(','));
                    
                    // 检查是否选择了2个商品
                    if (selected.length != 2) {
                        alert('请选择2个商品！当前已选择 ' + selected.length + ' 个商品');
                    }
                });

                // 监听推荐2的选择变化
                $('#recommend2_select').on('change', function() {
                    var selected = $(this).val() || [];
                    $('#recommend2_products').val(selected.join(','));
                    
                    // 检查是否选择了2个商品
                    if (selected.length != 2) {
                        alert('请选择2个商品！当前已选择 ' + selected.length + ' 个商品');
                    }
                });
                
                // 表单提交前验证
                $('form').on('submit', function(e) {
                    var recommend1Products = $('#recommend1_products').val().split(',').filter(Boolean);
                    var recommend2Products = $('#recommend2_products').val().split(',').filter(Boolean);
                    
                    if (recommend1Products.length != 2 || recommend2Products.length != 2) {
                        e.preventDefault();
                        alert('每个推荐配置必须选择2个商品！\n推荐配置1: ' + recommend1Products.length + ' 个商品\n推荐配置2: ' + recommend2Products.length + ' 个商品');
                        return false;
                    }
                });
            });
            </script>

            <?php submit_button('保存配置'); ?>
        </form>
        <?php endif; ?>
    </div>
    <?php
}

// 添加直接访问链接
function zhuige_shop_config_plugin_action_links($links) {
    $settings_link = '<a href="admin.php?page=zhuige-shop-config">配置</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_zhuige-xcx/zhuige-xcx.php', 'zhuige_shop_config_plugin_action_links');

/**
 * 添加推荐配置字段
 */
function zhuige_shop_category_add_recommend_fields() {
    // 移除此函数，不在分类编辑界面显示推荐配置
}
add_action('shop_category_add_form_fields', 'zhuige_shop_category_add_recommend_fields', 999);

/**
 * 编辑推荐配置字段
 */
function zhuige_shop_category_edit_recommend_fields($term) {
    // 移除此函数，不在分类编辑界面显示推荐配置
}
add_action('shop_category_edit_form_fields', 'zhuige_shop_category_edit_recommend_fields', 999);

/**
 * 保存推荐配置
 */
function zhuige_shop_save_recommend_meta($term_id) {
    // 移除此函数，不在分类编辑界面保存推荐配置
}
add_action('created_shop_category', 'zhuige_shop_save_recommend_meta', 999);
add_action('edited_shop_category', 'zhuige_shop_save_recommend_meta', 999);