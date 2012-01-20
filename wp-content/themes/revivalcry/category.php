<?php get_header(); ?>
		<div class="full-width-white">
			<div id="content-container" class="span-7 prepend-1">
				<section id="content" role="main">
	
					<h1 class="page-title"><?php
						printf( __( 'Category Archives: %s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
					?></h1>
					<?php
						$category_description = category_description();
						if ( ! empty( $category_description ) )
							echo '<div class="archive-meta">' . $category_description . '</div>';
	
					get_template_part( 'loop', 'blog-layout' );
					?>
	
				</section><!-- #content -->
			</div><!-- #content-container -->

<?php get_sidebar(); ?>
			<div class="clear"></div>
		</div><!-- .full-width-white -->
<?php get_footer(); ?>
