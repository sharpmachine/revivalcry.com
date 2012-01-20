<?php get_header(); ?>
		<div class="full-width-white">
			<div id="content-container" class="span-7 prepend-1">
				<section id="content" role="main">
	
					<h1 class="page-title"><?php
						printf( __( 'Tag Archives: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
					?></h1>
	
	<?php get_template_part( 'loop', 'blog-layout' ); ?>
				</section><!-- #content -->
			</div><!-- #content-container -->

<?php get_sidebar(); ?>
			<div class="clear"></div>
		</div><!-- .full-width-white -->
<?php get_footer(); ?>
