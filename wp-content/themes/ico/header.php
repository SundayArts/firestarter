<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<!-- ogp: general-->
<meta name="og:title" content="<?php bloginfo('name'); ?>">
<meta name="og:type" content="website">
<meta name="og:url" content="">
<meta name="og:image" content="">
<meta name="og:site_name" content="<?php bloginfo('name'); ?>">
<meta name="og:description" content="<?php bloginfo('description'); ?>">

<!-- ogp: twitter-->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="">
<meta name="twitter:title" content="<?php bloginfo('name'); ?>">
<meta name="twitter:creator" content="">
<meta name="twitter:description" content="<?php bloginfo('description'); ?>">
<meta name="twitter:image:src" content="">

<title><?php bloginfo('name'); ?></title>

<link rel="icon" href="">
<meta name="keywords" content="<?php bloginfo('keywords'); ?>">
<meta name="description" content="<?php bloginfo('description'); ?>">
<link rel="canonical" href="">
<link rel="stylesheet" href="/wp-content/themes/ico/common/css/bootstrap.min.css">
<link rel="stylesheet" href="/wp-content/themes/ico/common/css/style.css">
<?php echo getWovnCodeSnippet(); ?>
<?php wp_head(); ?>
</head>

<body id="body">
<div class="wrapper">

<header id="jsi-header" class="fixed-top header">
    <nav class="navbar navbar-toggleable-md navbar-light bg-faded navbar-expand-md">
        <h1 class="page-heading"><a class="navbar-brand" href="/"><?php bloginfo('name'); ?></a></h1>
<?php //echo esc_html($site_settingｓ->get_text()); ?>
<?php //var_dump($site_setting); ?>
<?php //var_dump(getSiteSetting()); ?>
        <button class="navbar-toggler navbar-toggler-right jsc-nav-trigger" type="button" data-toggle="collapse" data-target="#navmenu">
			<span class="navbar-toggler-icon"></span>
		</button>
        <div class="collapse navbar-collapse navmenu jac-navmenu" id="navmenu">
            <?php wp_nav_menu( array(
                    'theme_location'  => 'header_menu',
                    'container'       => false,
                    'menu_class'      => '',
                    'container_class' => '',
                    'items_wrap'      => '<ul class="navbar-nav ml-auto">%3$s</ul>'));
            ?>
<!--
            <ul class="navbar-nav ml-auto">
                <li class="nav-item pt-lg-0 pb-lg-0 pt-2 pb-2">
                    <a class="nav-link" href="/wp-content/themes/ico/common/pdf/whitepaper.pdf" target="_blank">ホワイトペーパー</a>
-->
                </li>
            </ul>
        </div>
    </nav>
</header>
