<?php
/*
	*	Template Name: Videos
*/
 get_header(); ?>
 <h1>Videos</h1>
		<div id="content-container">
			<section id="content" role="main">

			<?php query_posts("post_type=videos&posts_per_page=100"); ?>
			
			<?php if (have_posts()) : ?>
			
	<?php while (have_posts()) : the_post(); ?>
		
		<div class="videos-container">
			<a href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>&width=640&height=390" rel="lightbox[video]" class="video-gallery">
				<img src="http://img.youtube.com/vi/<?php the_field('youtube_video_id'); ?>/0.jpg" alt="Hello" width="220" height="150">
				<span class="video-icon"><img src="<?php bloginfo('template_directory'); ?>/images/video-icon.png" width="50" height="50" alt="Video"></span>
			</a>
			<span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
		</div>
	<?php endwhile; ?>
			
		<?php // Navigation ?>
			
	<?php else : ?>
			
		<?php // No Posts Found ?>
			
<?php endif; ?>
			
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
