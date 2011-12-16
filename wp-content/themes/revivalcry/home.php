<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'index' ); ?>
				
    <a href="#" class="hastip" title="I am the tooltip text">Foobar</a>
			</section><!-- #content -->
		</div><!-- #content-container -->
		
<?php get_footer(); ?>