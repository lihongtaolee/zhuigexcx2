<?php
/**
 * 商品分发频道
 */

if (!defined('WPINC')) {
    die;
}

/**
 * 激活商品分发频道
 */
function zhuige_shop_activate() {
    require_once dirname(__FILE__) . '/sql/install.php';
}

/**
 * 创建商品自定义文章类型
 */
function zhuige_shop_init() {
    $labels = array(
        'name'               => '商品',
        'singular_name'      => '商品',
        'menu_name'          => '商品管理',
        'add_new'           => '添加商品',
        'add_new_item'      => '添加新商品',
        'edit_item'         => '编辑商品',
        'new_item'          => '新商品',
        'view_item'         => '查看商品',
        'search_items'      => '搜索商品',
        'not_found'         => '未找到商品',
        'not_found_in_trash'=> '回收站中未找到商品',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'shop'),
        'capability_type'   => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 5,
        'supports'          => array('title', 'editor', 'thumbnail'),
        'menu_icon'         => 'dashicons-cart',
    );

    register_post_type('shop_product', $args);

    // 注册商品分类
    $category_labels = array(
        'name'              => '商品分类',
        'singular_name'     => '商品分类',
        'search_items'      => '搜索分类',
        'all_items'        => '所有分类',
        'parent_item'      => '父级分类',
        'parent_item_colon'=> '父级分类:',
        'edit_item'        => '编辑分类',
        'update_item'      => '更新分类',
        'add_new_item'     => '添加新分类',
        'new_item_name'    => '新分类名称',
        'menu_name'        => '分类',
    );

    $category_args = array(
        'hierarchical'      => true,
        'labels'           => $category_labels,
        'show_ui'          => true,
        'show_admin_column'=> true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'shop-category'),
    );

    register_taxonomy('shop_category', array('shop_product'), $category_args);
}
add_action('init', 'zhuige_shop_init');

/**
 * 添加商品元数据
 */
function zhuige_shop_add_meta_boxes() {
    add_meta_box(
        'shop_product_meta_box',
        '商品信息',
        'zhuige_shop_meta_box_callback',
        'shop_product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'zhuige_shop_add_meta_boxes');

/**
 * 商品元数据回调函数
 */
function zhuige_shop_meta_box_callback($post) {
    wp_nonce_field('zhuige_shop_save_meta_box_data', 'zhuige_shop_meta_box_nonce');

    $price = get_post_meta($post->ID, '_shop_price', true);
    $original_price = get_post_meta($post->ID, '_shop_original_price', true);
    $app_id = get_post_meta($post->ID, '_shop_app_id', true);
    $path = get_post_meta($post->ID, '_shop_path', true);
    $slides = get_post_meta($post->ID, '_shop_slides', true);
    $is_new = get_post_meta($post->ID, '_shop_is_new', true);
    $is_hot = get_post_meta($post->ID, '_shop_is_hot', true);
    $sales = get_post_meta($post->ID, '_shop_sales', true);
    
    if (!is_array($slides)) {
        $slides = array();
    }
    ?>
    <style>
    .shop-slides {
        margin: 20px 0;
    }
    .shop-slides-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .shop-slide-item {
        position: relative;
        width: 150px;
        height: 150px;
    }
    .shop-slide-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .shop-slide-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        line-height: 1;
        cursor: pointer;
    }
    .shop-meta-section {
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    .shop-meta-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 14px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    .shop-meta-row {
        display: flex;
        margin-bottom: 10px;
    }
    .shop-meta-row label {
        width: 150px;
        padding-top: 5px;
    }
    .shop-meta-row .shop-meta-input {
        flex: 1;
    }
    </style>

    <div class="shop-meta-section">
        <h3>商品幻灯片</h3>
        <div class="shop-slides">
            <input type="hidden" name="shop_slides" id="shop_slides" value="<?php echo esc_attr(implode(',', $slides)); ?>" />
            <button type="button" class="button" id="add_slide">添加幻灯片</button>
            <div class="shop-slides-list" id="slides_list">
                <?php foreach ($slides as $slide_id): ?>
                    <?php $image_url = wp_get_attachment_image_url($slide_id, 'full'); ?>
                    <?php if ($image_url): ?>
                    <div class="shop-slide-item" data-id="<?php echo $slide_id; ?>">
                        <img src="<?php echo esc_url($image_url); ?>" />
                        <button type="button" class="shop-slide-remove" onclick="removeSlide(this)">×</button>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="shop-meta-section">
        <h3>商品价格</h3>
        <div class="shop-meta-row">
            <label for="shop_price">商品价格：</label>
            <div class="shop-meta-input">
                <input type="number" step="0.01" id="shop_price" name="shop_price" value="<?php echo esc_attr($price); ?>" />
            </div>
        </div>
        <div class="shop-meta-row">
            <label for="shop_original_price">商品原价：</label>
            <div class="shop-meta-input">
                <input type="number" step="0.01" id="shop_original_price" name="shop_original_price" value="<?php echo esc_attr($original_price); ?>" />
            </div>
        </div>
    </div>

    <div class="shop-meta-section">
        <h3>商品标签和销量</h3>
        <div class="shop-meta-row">
            <label>商品标签：</label>
            <div class="shop-meta-input">
                <label><input type="checkbox" name="shop_is_new" value="1" <?php checked($is_new, '1'); ?> /> 新品</label>
                <label style="margin-left: 20px;"><input type="checkbox" name="shop_is_hot" value="1" <?php checked($is_hot, '1'); ?> /> 热销</label>
            </div>
        </div>
        <div class="shop-meta-row">
            <label for="shop_sales">销量：</label>
            <div class="shop-meta-input">
                <input type="number" id="shop_sales" name="shop_sales" value="<?php echo esc_attr($sales); ?>" min="0" />
            </div>
        </div>
    </div>

    <div class="shop-meta-section">
        <h3>小程序跳转</h3>
        <div class="shop-meta-row">
            <label for="shop_app_id">小程序 AppID：</label>
            <div class="shop-meta-input">
                <input type="text" id="shop_app_id" name="shop_app_id" value="<?php echo esc_attr($app_id); ?>" />
            </div>
        </div>
        <div class="shop-meta-row">
            <label for="shop_path">商品详情页路径：</label>
            <div class="shop-meta-input">
                <input type="text" id="shop_path" name="shop_path" value="<?php echo esc_attr($path); ?>" style="width: 100%;" />
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#add_slide').click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: '选择幻灯片图片',
                button: {
                    text: '使用此图片'
                },
                multiple: true
            });
            
            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toJSON();
                var currentSlides = $('#shop_slides').val() ? $('#shop_slides').val().split(',') : [];
                
                attachments.forEach(function(attachment) {
                    if (!currentSlides.includes(attachment.id.toString())) {
                        currentSlides.push(attachment.id);
                        $('#slides_list').append(
                            '<div class="shop-slide-item" data-id="' + attachment.id + '">' +
                            '<img src="' + attachment.url + '" />' +
                            '<button type="button" class="shop-slide-remove" onclick="removeSlide(this)">×</button>' +
                            '</div>'
                        );
                    }
                });
                
                $('#shop_slides').val(currentSlides.join(','));
            });
            
            mediaUploader.open();
        });
    });
    
    function removeSlide(button) {
        var item = jQuery(button).parent();
        var id = item.data('id');
        var slides = jQuery('#shop_slides').val().split(',');
        var index = slides.indexOf(id.toString());
        if (index > -1) {
            slides.splice(index, 1);
        }
        jQuery('#shop_slides').val(slides.join(','));
        item.remove();
    }
    </script>
    <?php
}

/**
 * 保存商品元数据
 */
function zhuige_shop_save_meta_box_data($post_id) {
    if (!isset($_POST['zhuige_shop_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['zhuige_shop_meta_box_nonce'], 'zhuige_shop_save_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array(
        'shop_price' => '_shop_price',
        'shop_original_price' => '_shop_original_price',
        'shop_app_id' => '_shop_app_id',
        'shop_path' => '_shop_path',
        'shop_slides' => '_shop_slides',
        'shop_sales' => '_shop_sales'
    );

    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            if ($field === 'shop_slides') {
                $slides = explode(',', sanitize_text_field($_POST[$field]));
                $slides = array_filter($slides);
                update_post_meta($post_id, $meta_key, $slides);
            } else {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    // 处理复选框字段
    $checkbox_fields = array(
        'shop_is_new' => '_shop_is_new',
        'shop_is_hot' => '_shop_is_hot'
    );
    
    foreach ($checkbox_fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $meta_key, '1');
        } else {
            update_post_meta($post_id, $meta_key, '');
        }
    }
}
add_action('save_post', 'zhuige_shop_save_meta_box_data');

/**
 * 添加分类图标字段
 */
function zhuige_shop_category_add_form_fields() {
    ?>
    <div class="form-field">
        <label for="category_icon">分类图标</label>
        <input type="text" name="category_icon" id="category_icon" />
        <p>请输入分类图标的URL地址</p>
    </div>
    <div class="form-field">
        <label for="category_sort_order">排序</label>
        <input type="number" name="category_sort_order" id="category_sort_order" value="0" min="0" />
        <p>数字越小排序越靠前，默认为0</p>
    </div>
    <?php
}
add_action('shop_category_add_form_fields', 'zhuige_shop_category_add_form_fields');

/**
 * 编辑分类图标字段
 */
function zhuige_shop_category_edit_form_fields($term) {
    $icon = get_term_meta($term->term_id, 'category_icon', true);
    $sort_order = get_term_meta($term->term_id, 'category_sort_order', true);
    if ($sort_order === '') {
        $sort_order = 0;
    }
    ?>
    <tr class="form-field">
        <th scope="row"><label for="category_icon">分类图标</label></th>
        <td>
            <input type="text" name="category_icon" id="category_icon" value="<?php echo esc_attr($icon); ?>" />
            <p class="description">请输入分类图标的URL地址</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="category_sort_order">排序</label></th>
        <td>
            <input type="number" name="category_sort_order" id="category_sort_order" value="<?php echo esc_attr($sort_order); ?>" min="0" />
            <p class="description">数字越小排序越靠前，默认为0</p>
        </td>
    </tr>
    <?php
}
add_action('shop_category_edit_form_fields', 'zhuige_shop_category_edit_form_fields');

/**
 * 保存分类图标
 */
function zhuige_shop_save_category_meta($term_id) {
    if (isset($_POST['category_icon'])) {
        update_term_meta($term_id, 'category_icon', sanitize_text_field($_POST['category_icon']));
    }
    
    if (isset($_POST['category_sort_order'])) {
        $sort_order = intval($_POST['category_sort_order']);
        if ($sort_order < 0) {
            $sort_order = 0;
        }
        update_term_meta($term_id, 'category_sort_order', $sort_order);
    }
}
add_action('created_shop_category', 'zhuige_shop_save_category_meta');
add_action('edited_shop_category', 'zhuige_shop_save_category_meta');

/**
 * 注册 REST API 接口
 */
add_action('rest_api_init', function () {
    // 获取分类列表
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/categories', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_categories',
        'permission_callback' => '__return_true'
    ));

    // 获取分类详情
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/category', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_category',
        'permission_callback' => '__return_true'
    ));

    // 获取二级分类
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/subcategories', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_subcategories',
        'permission_callback' => '__return_true'
    ));

    // 获取三级分类
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/thirdcategories', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_thirdcategories',
        'permission_callback' => '__return_true'
    ));

    // 获取推荐商品
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/recommend', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_recommend',
        'permission_callback' => '__return_true'
    ));

    // 获取商品列表
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/products', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_products',
        'permission_callback' => '__return_true'
    ));

    // 获取商品详情
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/product', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_product',
        'permission_callback' => '__return_true'
    ));

    // 获取热门商品
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/hotproducts', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_get_hot_products',
        'permission_callback' => '__return_true'
    ));

    // 搜索商品
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/search', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_search_products',
        'permission_callback' => '__return_true'
    ));
    
    // 更新商品销量
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/update-sales', array(
        'methods' => 'POST',
        'callback' => 'zhuige_xcx_shop_update_sales',
        'permission_callback' => '__return_true'
    ));
    
    // 测试API
    register_rest_route('zhuige-xcx/v1', '/sgtool/shop/test', array(
        'methods' => 'GET',
        'callback' => 'zhuige_xcx_shop_test',
        'permission_callback' => '__return_true'
    ));
});

/**
 * 获取分类列表
 */
function zhuige_xcx_shop_get_categories($request) {
    try {
        $args = array(
            'taxonomy' => 'shop_category',
            'hide_empty' => false,
            'parent' => 0,
            'meta_key' => 'category_sort_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        );

        $terms = get_terms($args);
        
        // 检查是否有错误
        if (is_wp_error($terms)) {
            error_log('获取分类列表错误: ' . $terms->get_error_message());
            return array(
                'code' => 500,
                'msg' => '获取分类失败: ' . $terms->get_error_message()
            );
        }
        
        // 检查是否为空
        if (empty($terms)) {
            error_log('没有找到分类');
            return array(
                'code' => 200,
                'msg' => '没有找到分类',
                'data' => array()
            );
        }

        $categories = array();
        foreach ($terms as $term) {
            $icon = get_term_meta($term->term_id, 'category_icon', true);
            $sort_order = get_term_meta($term->term_id, 'category_sort_order', true);
            
            $categories[] = array(
                'id' => $term->term_id,
                'name' => $term->name,
                'icon' => $icon ? $icon : '',
                'sort_order' => $sort_order ? intval($sort_order) : 0
            );
        }

        return array(
            'code' => 200,
            'msg' => '获取成功',
            'data' => $categories
        );
    } catch (Exception $e) {
        error_log('获取分类列表异常: ' . $e->getMessage());
        return array(
            'code' => 500,
            'msg' => '服务器错误: ' . $e->getMessage()
        );
    }
}

/**
 * 获取分类详情
 */
function zhuige_xcx_shop_get_category($request) {
    $category_id = $request->get_param('id');
    $term = get_term($category_id, 'shop_category');

    if (!$term || is_wp_error($term)) {
        return array(
            'code' => 404,
            'msg' => '分类不存在'
        );
    }

    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => array(
            'id' => $term->term_id,
            'name' => $term->name,
            'icon' => get_term_meta($term->term_id, 'category_icon', true),
            'sort_order' => get_term_meta($term->term_id, 'category_sort_order', true)
        )
    );
}

/**
 * 获取二级分类
 */
function zhuige_xcx_shop_get_subcategories($request) {
    $parent_id = $request->get_param('parent_id');
    
    $args = array(
        'taxonomy' => 'shop_category',
        'hide_empty' => false,
        'parent' => $parent_id,
        'meta_key' => 'category_sort_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );

    $terms = get_terms($args);
    $categories = array();

    foreach ($terms as $term) {
        $categories[] = array(
            'id' => $term->term_id,
            'name' => $term->name,
            'icon' => get_term_meta($term->term_id, 'category_icon', true),
            'sort_order' => get_term_meta($term->term_id, 'category_sort_order', true)
        );
    }

    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => $categories
    );
}

/**
 * 获取三级分类
 */
function zhuige_xcx_shop_get_thirdcategories($request) {
    $parent_id = $request->get_param('parent_id');
    
    $args = array(
        'taxonomy' => 'shop_category',
        'hide_empty' => false,
        'parent' => $parent_id,
        'meta_key' => 'category_sort_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );

    $terms = get_terms($args);
    $categories = array();

    foreach ($terms as $term) {
        $categories[] = array(
            'id' => $term->term_id,
            'name' => $term->name,
            'icon' => get_term_meta($term->term_id, 'category_icon', true),
            'sort_order' => get_term_meta($term->term_id, 'category_sort_order', true)
        );
    }

    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => $categories
    );
}

/**
 * 获取推荐商品
 */
function zhuige_xcx_shop_get_recommend($request) {
    $category_id = $request->get_param('category_id');
    
    // 获取分类的推荐配置
    $recommend1 = get_term_meta($category_id, 'recommend1', true);
    $recommend2 = get_term_meta($category_id, 'recommend2', true);
    
    $data = array();
    
    if ($recommend1) {
        // 确保有两个商品
        $products = isset($recommend1['products']) && is_array($recommend1['products']) ? $recommend1['products'] : array();
        if (count($products) > 0) {
            $data['recommend1'] = array(
                'title' => isset($recommend1['title']) ? $recommend1['title'] : '热门推荐',
                'desc' => isset($recommend1['desc']) ? $recommend1['desc'] : '',
                'products' => zhuige_xcx_shop_get_products_by_ids($products)
            );
        }
    }
    
    if ($recommend2) {
        // 确保有两个商品
        $products = isset($recommend2['products']) && is_array($recommend2['products']) ? $recommend2['products'] : array();
        if (count($products) > 0) {
            $data['recommend2'] = array(
                'title' => isset($recommend2['title']) ? $recommend2['title'] : '新品上市',
                'desc' => isset($recommend2['desc']) ? $recommend2['desc'] : '',
                'products' => zhuige_xcx_shop_get_products_by_ids($products)
            );
        }
    }
    
    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => $data
    );
}

/**
 * 根据ID获取商品信息
 */
function zhuige_xcx_shop_get_products_by_ids($product_ids) {
    if (empty($product_ids)) {
        return array();
    }

    $args = array(
        'post_type' => 'shop_product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post__in' => $product_ids,
        'orderby' => 'post__in'
    );

    $query = new WP_Query($args);
    $products = array();

    while ($query->have_posts()) {
        $query->the_post();
        $products[] = zhuige_xcx_shop_format_product(get_post());
    }

    wp_reset_postdata();
    return $products;
}

/**
 * 获取商品列表
 */
function zhuige_xcx_shop_get_products($request) {
    try {
        $category_id = $request->get_param('category_id');
        $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
        $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 10;

        $args = array(
            'post_type' => 'shop_product',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        if ($category_id) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'shop_category',
                    'field' => 'term_id',
                    'terms' => $category_id,
                    'include_children' => true
                )
            );
        }

        $query = new WP_Query($args);
        $products = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $products[] = zhuige_xcx_shop_format_product(get_post());
            }
        } else {
            error_log('没有找到商品');
        }

        wp_reset_postdata();

        return array(
            'code' => 200,
            'msg' => '获取成功',
            'data' => array(
                'list' => $products,
                'more' => $query->max_num_pages > $page,
                'total' => $query->found_posts,
                'total_pages' => $query->max_num_pages
            )
        );
    } catch (Exception $e) {
        error_log('获取商品列表异常: ' . $e->getMessage());
        return array(
            'code' => 500,
            'msg' => '服务器错误: ' . $e->getMessage()
        );
    }
}

/**
 * 格式化商品数据
 */
function zhuige_xcx_shop_format_product($post) {
    try {
        $slides = get_post_meta($post->ID, '_shop_slides', true);
        $slide_urls = array();
        
        if (is_array($slides) && !empty($slides)) {
            foreach ($slides as $slide_id) {
                $image_url = wp_get_attachment_image_url($slide_id, 'full');
                if ($image_url) {
                    $slide_urls[] = $image_url;
                }
            }
        }

        // 如果没有幻灯片，使用特色图片
        if (empty($slide_urls)) {
            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
            if ($thumbnail_url) {
                $slide_urls[] = $thumbnail_url;
            }
        }

        $price = get_post_meta($post->ID, '_shop_price', true);
        $original_price = get_post_meta($post->ID, '_shop_original_price', true);
        $app_id = get_post_meta($post->ID, '_shop_app_id', true);
        $path = get_post_meta($post->ID, '_shop_path', true);
        $is_new = get_post_meta($post->ID, '_shop_is_new', true) === '1';
        $is_hot = get_post_meta($post->ID, '_shop_is_hot', true) === '1';
        $sales = get_post_meta($post->ID, '_shop_sales', true);

        return array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'image' => get_the_post_thumbnail_url($post->ID, 'full') ?: '',
            'slides' => $slide_urls,
            'price' => $price ?: '0',
            'original_price' => $original_price ?: '0',
            'app_id' => $app_id ?: '',
            'path' => $path ?: '',
            'is_new' => $is_new,
            'is_hot' => $is_hot,
            'sales' => intval($sales) ?: 0
        );
    } catch (Exception $e) {
        error_log('格式化商品数据异常: ' . $e->getMessage());
        return array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'image' => '',
            'slides' => array(),
            'price' => '0',
            'original_price' => '0',
            'app_id' => '',
            'path' => '',
            'is_new' => false,
            'is_hot' => false,
            'sales' => 0
        );
    }
}

/**
 * 获取商品详情
 */
function zhuige_xcx_shop_get_product($request) {
    $product_id = $request->get_param('id');
    
    if (!$product_id) {
        return array(
            'code' => 400,
            'msg' => '缺少商品ID参数'
        );
    }
    
    $post = get_post($product_id);
    
    if (!$post || $post->post_type !== 'shop_product' || $post->post_status !== 'publish') {
        return array(
            'code' => 404,
            'msg' => '商品不存在或已下架'
        );
    }
    
    // 获取商品分类
    $categories = array();
    $terms = wp_get_post_terms($product_id, 'shop_category');
    foreach ($terms as $term) {
        $categories[] = array(
            'id' => $term->term_id,
            'name' => $term->name,
            'icon' => get_term_meta($term->term_id, 'category_icon', true),
            'sort_order' => get_term_meta($term->term_id, 'category_sort_order', true)
        );
    }
    
    // 获取商品详情
    $product = zhuige_xcx_shop_format_product($post);
    $product['categories'] = $categories;
    $product['content'] = apply_filters('the_content', $post->post_content);
    
    // 更新商品浏览次数
    $views = get_post_meta($product_id, '_shop_views', true);
    $views = intval($views) + 1;
    update_post_meta($product_id, '_shop_views', $views);
    $product['views'] = $views;
    
    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => $product
    );
}

/**
 * 获取热门商品
 */
function zhuige_xcx_shop_get_hot_products($request) {
    $limit = $request->get_param('limit') ? intval($request->get_param('limit')) : 10;
    
    // 这里可以根据实际需求定义热门商品的获取逻辑
    // 例如，可以根据浏览量、销量等指标来获取热门商品
    // 这里简单地获取最新的商品作为热门商品
    
    $args = array(
        'post_type' => 'shop_product',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $query = new WP_Query($args);
    $products = array();
    
    while ($query->have_posts()) {
        $query->the_post();
        $products[] = zhuige_xcx_shop_format_product(get_post());
    }
    
    wp_reset_postdata();
    
    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => $products
    );
}

/**
 * 搜索商品
 */
function zhuige_xcx_shop_search_products($request) {
    $keyword = $request->get_param('keyword');
    $category_id = $request->get_param('category_id');
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 10;
    
    if (empty($keyword)) {
        return array(
            'code' => 400,
            'msg' => '请输入搜索关键词'
        );
    }
    
    $args = array(
        'post_type' => 'shop_product',
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'paged' => $page,
        's' => $keyword,
        'orderby' => 'relevance',
        'order' => 'DESC'
    );
    
    if ($category_id) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'shop_category',
                'field' => 'term_id',
                'terms' => $category_id,
                'include_children' => true
            )
        );
    }
    
    $query = new WP_Query($args);
    $products = array();
    
    while ($query->have_posts()) {
        $query->the_post();
        $products[] = zhuige_xcx_shop_format_product(get_post());
    }
    
    wp_reset_postdata();
    
    return array(
        'code' => 200,
        'msg' => '获取成功',
        'data' => array(
            'list' => $products,
            'more' => $query->max_num_pages > $page,
            'total' => $query->found_posts,
            'total_pages' => $query->max_num_pages
        )
    );
}

/**
 * 更新商品销量
 */
function zhuige_xcx_shop_update_sales($request) {
    $product_id = $request->get_param('id');
    $increment = $request->get_param('increment') ? intval($request->get_param('increment')) : 1;
    
    if (!$product_id) {
        return array(
            'code' => 400,
            'msg' => '缺少商品ID参数'
        );
    }
    
    $post = get_post($product_id);
    
    if (!$post || $post->post_type !== 'shop_product' || $post->post_status !== 'publish') {
        return array(
            'code' => 404,
            'msg' => '商品不存在或已下架'
        );
    }
    
    // 更新商品销量
    $sales = get_post_meta($product_id, '_shop_sales', true);
    $sales = intval($sales) + $increment;
    update_post_meta($product_id, '_shop_sales', $sales);
    
    return array(
        'code' => 200,
        'msg' => '更新成功',
        'data' => array(
            'sales' => $sales
        )
    );
}

/**
 * 测试API
 */
function zhuige_xcx_shop_test() {
    return array(
        'code' => 200,
        'msg' => '成功',
        'data' => array(
            'time' => current_time('mysql'),
            'version' => '1.0.0',
            'name' => '商品分发频道'
        )
    );
} 