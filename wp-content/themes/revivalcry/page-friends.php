<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'page' ); ?>
			<?php wp_list_bookmarks('title_li=&categorize=0'); ?>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>