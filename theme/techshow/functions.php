<?php
if (!defined('ABSPATH')) { exit; }
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption']);
});
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('techshow', get_stylesheet_uri(), [], '1.0.0');
});
add_action('init', function () {
    register_post_type('product', [
        'labels' => ['name' => 'Sản phẩm', 'singular_name' => 'Sản phẩm', 'add_new_item' => 'Thêm sản phẩm'],
        'public' => true, 'has_archive' => true, 'show_in_rest' => true,
        'rewrite' => ['slug' => 'san-pham'], 'menu_icon' => 'dashicons-products',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
    ]);
    register_taxonomy('product_group', ['product'], [
        'labels' => ['name' => 'Nhóm sản phẩm'], 'public' => true,
        'hierarchical' => true, 'show_in_rest' => true,
        'rewrite' => ['slug' => 'nhom-san-pham'],
    ]);
});
function techshow_image($id) {
    if (has_post_thumbnail($id)) { return get_the_post_thumbnail_url($id, 'large'); }
    $name = get_post_meta($id, 'illustration', true);
    $allowed = ['headphones', 'keyboard', 'watch', 'speaker', 'camera', 'lamp'];
    if (!in_array($name, $allowed, true)) { $name = 'headphones'; }
    return get_template_directory_uri() . '/assets/' . $name . '.svg';
}
function techshow_card() { ?>
    <article class="product-card">
        <a class="product-picture" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url(techshow_image(get_the_ID())); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy"></a>
        <div class="card-content"><span class="eyebrow"><?php echo esc_html(get_post_meta(get_the_ID(), 'tagline', true)); ?></span>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p><?php echo esc_html(get_the_excerpt()); ?></p>
        <div class="card-bottom"><strong><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?></strong><a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr('Xem ' . get_the_title()); ?>">Khám phá ↗</a></div></div>
    </article>
<?php }

function techshow_seed_sample_data() {
    if (get_option('techshow_seeded')) {
        return;
    }
    $existing = get_posts(['post_type' => 'product', 'posts_per_page' => 1]);
    if (!empty($existing)) {
        update_option('techshow_seeded', 1);
        return;
    }

    $items = [
        ['AirSound Pro', 'headphones', 'Âm thanh', 'Âm thanh không giới hạn', '1.490.000 ₫', 'Tai nghe không dây dành cho ngày dài đầy cảm hứng.', 'Bluetooth 5.3|Pin đến 30 giờ|Thiết kế chụp tai|Khối lượng 245 g'],
        ['KeyFlow 75', 'keyboard', 'Góc làm việc', 'Gõ nhịp sáng tạo', '1.890.000 ₫', 'Bàn phím nhỏ gọn cho góc làm việc của riêng bạn.', 'Bố cục 75%|Kết nối USB-C và Bluetooth|Đèn nền tùy chỉnh|Keycap PBT'],
        ['LifeWatch S', 'watch', 'Thiết bị thông minh', 'Theo nhịp sống của bạn', '2.190.000 ₫', 'Đồng hồ thông minh đồng hành trong từng chuyển động.', 'Màn hình 1,5 inch|Pin đến 7 ngày|Theo dõi vận động|Dây đeo silicone'],
        ['SoundCube Mini', 'speaker', 'Âm thanh', 'Mang âm nhạc đi cùng', '790.000 ₫', 'Loa di động nhỏ gọn cho những cuộc hẹn ngẫu hứng.', 'Bluetooth 5.3|Công suất 10 W|Pin đến 12 giờ|Sạc USB-C'],
        ['PocketCam One', 'camera', 'Sáng tạo', 'Lưu giữ điều đáng nhớ', '3.490.000 ₫', 'Máy ảnh gọn nhẹ để kể câu chuyện theo cách của bạn.', 'Ảnh 24 MP|Video Full HD|Màn hình 2,8 inch|Lưu trữ thẻ SD'],
        ['GlowDesk', 'lamp', 'Góc làm việc', 'Thắp sáng cảm hứng', '590.000 ₫', 'Đèn bàn tối giản mang lại ánh sáng cho góc sáng tạo.', 'Ba mức nhiệt màu|Điều chỉnh độ sáng|Công suất 8 W|Cấp nguồn USB-C'],
    ];

    foreach ($items as $item) {
        list($title, $art, $group, $tag, $price, $excerpt, $specs) = $item;
        $slug = sanitize_title($title);
        $content = '<h2>Thiết kế cho trải nghiệm mỗi ngày</h2><p>' . esc_html($excerpt) . ' Sản phẩm kết hợp phong cách tinh giản và tính tiện dụng, phù hợp với không gian sống hiện đại.</p><h2>Thông số nổi bật</h2><ul>';
        foreach (explode('|', $specs) as $spec) {
            $content .= '<li>' . esc_html($spec) . '</li>';
        }
        $content .= '</ul><p>Sản phẩm chính hãng TechShow, bảo hành 12 tháng chính hãng trên toàn quốc.</p>';

        $post_id = wp_insert_post([
            'post_type'    => 'product',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_excerpt' => $excerpt,
            'post_content' => $content,
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'illustration', $art);
            update_post_meta($post_id, 'tagline', $tag);
            update_post_meta($post_id, 'price', $price);
            wp_set_object_terms($post_id, [$group], 'product_group');
        }
    }

    $pages = [
        ['Giới thiệu', 'gioi-thieu', '<h1>Về TechShow</h1><p>TechShow là không gian trưng bày và phân phối các sản phẩm công nghệ hiện đại. Chúng tôi chuyên cung cấp các thiết bị thông minh, phụ kiện bàn làm việc và âm thanh cá nhân cao cấp.</p>'],
        ['Liên hệ', 'lien-he', '<h1>Liên hệ với chúng tôi</h1><p>Nếu bạn có thắc mắc về sản phẩm hoặc hợp tác, vui lòng gửi thông tin qua email hoặc liên hệ với đội ngũ hỗ trợ.</p><ul><li>Email: contact@techshow.local</li><li>Điện thoại: 0123 456 789</li><li>Địa chỉ: Khu Công nghệ cao, Hà Nội</li></ul>'],
    ];

    foreach ($pages as $p) {
        list($ptitle, $pslug, $pcontent) = $p;
        $check = get_page_by_path($pslug);
        if (!$check) {
            wp_insert_post([
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $ptitle,
                'post_name'    => $pslug,
                'post_content' => $pcontent,
            ]);
        }
    }

    update_option('techshow_seeded', 1);
}
add_action('init', 'techshow_seed_sample_data', 20);
