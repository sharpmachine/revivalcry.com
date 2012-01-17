<?php 
/*
	Template Name: Media
*/
get_header(); ?>

		<div id="content-container">
			<section id="content" role="main" class="full-width-white">
			<div class="span-4 prepend-1 video-landing">
				<a href="<?php bloginfo('url'); ?>/media/videos"><img src="<?php bloginfo('template_directory'); ?>/images/videos.jpg" width="300" height="200" alt="Photo Gallery"></a>
				<h1><a href="<?php bloginfo('url'); ?>/media/videos">Videos</a></h1>
				<h2>New teachings by Dennis Reanier</h2>
				<a href="<?php bloginfo('url'); ?>/media/videos" class="button">See More</a>
			</div>
			
			<div class="span-4 prepend-2 last video-landing">
				<a href="<?php bloginfo('url'); ?>/media/photo-gallery"><img src="<?php bloginfo('template_directory'); ?>/images/photo-gallery.jpg" width="300" height="200" alt="Photo Gallery"></a>
				<h1><a href="<?php bloginfo('url'); ?>/media/photo-gallery">Photo Gallery</a></h1>
				<h2>Browse our photo gallery</h2>
				<a href="<?php bloginfo('url'); ?>/media/photo-gallery" class="button">See More</a>
			</div>
			<div class="clear"></div>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
