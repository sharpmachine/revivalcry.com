<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">
				
				<section class="span-4">
					<article class="feature-box">
						<a href="<?php bloginfo('url'); ?>/about">
							<img src="<?php bloginfo('template_directory'); ?>/images/learn-more.jpg" width="90" height="90" alt="Learn More">
							<div class="content">
								<h2>Learn More</h2>
								<span>Revival is our heart</span>
							</div>
						</a>
					</article>
					<article class="feature-box">
						<a href="<?php bloginfo('url'); ?>/store">
							<img src="<?php bloginfo('template_directory'); ?>/images/products.jpg" width="90" height="90" alt="Products">
							<div class="content">
								<h2>Products</h2>
								<span>See our latest</span>
							</div>
						</a>
					</article>
					<article class="feature-box no-mb">
						<a href="<?php bloginfo('url'); ?>/events">
							<img src="<?php bloginfo('template_directory'); ?>/images/events.jpg" width="90" height="90" alt="Events">
							<div class="content">
								<h2>All Events</h2>
								<span>Complete Calendar</span>
						</div>
						</a>
					</article>
				</section>
				
				<section class="span-8 last">

<iframe src="http://www.xpmedia.com/embed/11909" frameborder="0" width="620" height="345"></iframe>


	<!--<?php query_posts("post_type=videos&posts_per_page=1"); ?>

		<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
			<iframe width="620" height="345" src="http://www.youtube.com/embed/<?php the_field('youtube_video_id'); ?>?rel=0" frameborder="0" allowfullscreen></iframe>
		<?php endwhile; ?>

		<?php // Navigation ?>

		<?php else : ?>

		<?php // No Posts Found ?>

	<?php endif; ?> -->
</section>
				
			</section><!-- #content -->
		</div><!-- #content-container -->
		
<?php get_footer(); ?>