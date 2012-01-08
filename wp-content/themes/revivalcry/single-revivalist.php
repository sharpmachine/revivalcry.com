<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">
			<?php $ids = array(); while (have_posts()) : the_post(); ?>
				<article class="single-revivalist full-width-white">
					<div class="revivalist span-6">
						<h1><?php the_title(); ?></h1>
						<?php the_content(); ?>
						<a href="<?php bloginfo('url'); ?>/invite" class="button">Invite</a>
						<?php if (get_field("calendar_url")): ?>
							<a href="<?php the_field("calendar_url") ?>" class="button">Calendar</a>
						<?php endif ?>
					</div>
					<div class="span-4 prepend-1 last">
						<?php if (get_field("headshot")): ?>
					<img src="<?php the_field("headshot"); ?>" alt="<?php the_title(); ?>" width="300" height="200">
					<?php else: ?>
					<img src="<?php bloginfo('template_directory'); ?>/images/no-headshot.jpg" alt="<?php the_title(); ?>" width="300" height="200">
				<?php endif ?>
					</div>
					<div class="clear"></div>
				</article>
			<?php $ids[]= $post->ID; endwhile; ?>
			
				<section id="other-revivalists">
					<?php query_posts("post_type=revivalist");
						while (have_posts()) : the_post();
						if (!in_array($post->ID, $ids)) { ?>
				
					<article class="revivalists span-3">
						<?php if (get_field("headshot")): ?>
							<img src="<?php the_field("headshot"); ?>" alt="<?php the_title(); ?>" width="220" height="147">
							<?php else: ?>
							<img src="<?php bloginfo('template_directory'); ?>/images/no-headshot.jpg" alt="<?php the_title(); ?>" width="220" height="147">
						<?php endif ?>
						<h2><?php the_title(); ?></h2>
						<?php the_excerpt(); ?>
					</article>
						 <?php } endwhile; ?>
				</section><!-- #other-revivalists -->
			</section><!-- #content -->
		</div><!-- #content-container -->
<?php get_footer(); ?>