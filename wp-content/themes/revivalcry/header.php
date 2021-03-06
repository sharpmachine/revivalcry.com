<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]>
<html class="no-js ie6" <?php language_attributes(); ?>> 
<![endif]-->
<!--[if IE 7 ]>    
<html class="no-js ie7" <?php language_attributes(); ?>> 
<![endif]-->
<!--[if IE 8 ]>    
<html class="no-js ie8" <?php language_attributes(); ?>>
 <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> 
<html class="no-js" <?php language_attributes(); ?>> 
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
	<meta name="author" content="Jesse Kade of Sharp Machine Media">
	<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/images/icons/favicon.ico">
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/print.css" type="text/css" media="print">
    <!--<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wp-style.css">-->
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/style.css">
    <?php if (is_home() || is_front_page()): ?>
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wt-rotator.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <?php endif ?>
    <?php if (is_page('photo-gallery')): ?>
    	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/lightbox.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <?php endif ?>
	<!--[if lte IE 8]><link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie.css" type="text/css" media="screen, projection"><![endif]-->
	
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
	<script src="<?php bloginfo('template_directory'); ?>/js/modernizr-1.7.min.js"></script>
	<!--[if lte IE 8]>
		<script src="<?php bloginfo('template_directory'); ?>/js/selectivizr-min.js"></script>
	<![endif]--> 
	<link rel="self" type="application/rss+xml" title="Revival Cry &raquo; Events Feed" href="<?php bloginfo('url'); ?>/events/rss" />

<?php
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		
	wp_head();
?>
</head>
	<body <?php body_class(); ?>>
		<div class="wrapper">
			<header role="banner">
				<div id="top-menu">
						<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'top-menu' ) ); ?>
					</div><!-- #top-menu -->
			<div id="header">
				
				<hgroup>
					<h1 id="site-title">
						<span>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
								<?php if (is_home() || is_front_page()): ?>
									<img src="<?php bloginfo('template_directory'); ?>/images/logo1.png" width="223" height="43" alt="Logo">
									<?php else: ?>
									<img src="<?php bloginfo('template_directory'); ?>/images/logo2.png" width="223" height="43" alt="Logo">
								<?php endif; ?>
							</a>
						</span>
					</h1>
				</hgroup>
				<nav role="navigation">
					<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
					<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a></div>
						<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
						<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
				</nav><!-- nav -->
			</div><!-- #header -->	
			
			<?php if (is_home() || is_front_page()): ?>
			<div id="banner">
				<div class="wt-rotator">
					<div class="screen">
						<noscript>
							<!-- placeholder 1st image when javascript is off -->
							<img src="<?php bloginfo('template_directory'); ?>/images/madness_arch2.jpg"/>
						</noscript>
					</div>
					<div class="c-panel">
						<div class="thumbnails">
							<ul>
								<?php query_posts('post_type=banner'); ?>
								<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
								<li>
									<a href="<?php echo the_field('banner_image'); ?>" title="architecture"></a>
									<a href="<?php echo the_field('banner_link'); ?>"></a>    
								</li>
								<?php endwhile; ?>
								<?php else : ?>
								<img src="<?php bloginfo('template_directory'); ?>/images/madness_arch2.jpg"/>
								<?php endif; ?>
							</ul>
						</div>
						<div class="buttons">
							<div class="prev-btn"></div>
							<div class="play-btn"></div>
							<div class="next-btn"></div>
						</div>
					 </div>
				 </div>	
			</div><!-- #banner -->
<?php endif; ?>
		</header>
		<div class="container">
			<section id="page">