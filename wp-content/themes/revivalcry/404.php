<?php get_header(); ?>

	<div id="content-container">
		<section id="content" role="main">

			<div id="post-0" class="post error404 not-found">
				<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
				<div class="entry-content">
					<h3><?php _e( 'Bummer, but the page you requested could not be found. Trying search!', 'twentyten' ); ?></h3>
					<div class="span-4">
						<?php get_search_form(); ?>
					</div>
					
					<div class="span-4">
						<?php wp_list_pages(); ?>
					</div>
					<div class="span-4 last">
						<?php wp_list_categories(); ?>
					</div>
					
					
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->

		</section><!-- #content -->
	</div><!-- #content-container -->
	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

<?php get_footer(); ?>