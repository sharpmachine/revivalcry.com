<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">
			<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
				<article class="single-revivalist">
					<div class="revivalist span-6">
						<h2><?php the_title(); ?></h2>
						<?php the_content(); ?>
						<a href="<?php bloginfo('url'); ?>/invite" class="button">Invite</a>
						<a href="<?php the_field("calendar_url") ?>" class="button">Calendar</a>
					</div>
					<div class="span-4 prepend-1 last">
						<?php if (get_field("headshot")): ?>
					<img src="<?php the_field("headshot"); ?>" alt="<?php the_title(); ?>" width="300" height="200">
					<?php else: ?>
					<img src="<?php bloginfo('template_directory'); ?>/images/no-headshot.jpg" alt="<?php the_title(); ?>" width="300" height="200">
				<?php endif ?>
					</div>
				</article>
			<?php endwhile; ?>
				<section id="other-revivalists">
					<?php query_posts('post_type=revivalist'); ?>
						<?php while (have_posts()) : the_post();
						if (in_array ($post->ID, $do_not_duplicate)) continue;
						update_post_caches($post);
						 ?>
					<article class="revivalist span-3">
						<?php if (get_field("headshot")): ?>
							<img src="<?php the_field("headshot"); ?>" alt="<?php the_title(); ?>" width="220" height="147">
							<?php else: ?>
							<img src="<?php bloginfo('template_directory'); ?>/images/no-headshot.jpg" alt="<?php the_title(); ?>" width="220" height="147">
						<?php endif ?>
						<h2><?php the_title(); ?></h2>
						<?php the_excerpt(); ?>
					</article>
						 <?php endwhile; ?>
				</section><!-- #other-revivalists -->
			</section><!-- #content -->
		</div><!-- #content-container -->
<?php get_footer(); ?>