<?php 
/*
	Template Name: Revivalists
*/
get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">
				
			<?php query_posts("post_type=revivalist"); ?>
			
			<?php if (have_posts()) : ?>
			
	<?php while (have_posts()) : the_post(); ?>
		
		<article class="revivalists span-3">
			<?php if (get_field("headshot")): ?>
				<img src="<?php the_field("headshot"); ?>" alt="<?php the_title(); ?>" width="220" height="147">
				<?php else: ?>
				<img src="<?php bloginfo('template_directory'); ?>/images/no-headshot.jpg" alt="<?php the_title(); ?>" width="220" height="147">
			<?php endif ?>
			<h2><?php the_title(); ?></h2>
			<?php the_excerpt(); ?>
		</article>
			
		
			
	<?php endwhile; ?>
			
		<?php // Navigation ?>
			
	<?php else : ?>
			
		<?php // No Posts Found ?>
			
<?php endif; ?>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
