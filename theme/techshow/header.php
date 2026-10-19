<?php if (!defined('ABSPATH')) { exit; } ?><!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip" href="#main">Đến nội dung</a>
<div class="topbar">Không gian trải nghiệm công nghệ · Sản phẩm chính hãng & Bảo hành toàn quốc</div>
<header class="site-header"><div class="wrap nav"><a class="brand" href="<?php echo esc_url(home_url('/')); ?>">tech<span>show.</span></a>
<nav aria-label="Điều hướng chính"><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a><a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Sản phẩm</a><a href="<?php echo esc_url(home_url('/gioi-thieu/')); ?>">Giới thiệu</a><a class="nav-contact" href="<?php echo esc_url(home_url('/lien-he/')); ?>">Liên hệ ↗</a></nav></div></header>
