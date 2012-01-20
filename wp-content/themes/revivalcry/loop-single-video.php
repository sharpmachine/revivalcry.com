<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<p class="posted-on-date">Posted on <?php the_time('F j, Y'); ?></p>
					<div class="entry-utility">
						<?php echo get_the_term_list( $post->ID, 'video_categories', 'This entry was posted in ', ', ', '' ); ?>
						<?php echo get_the_term_list( $post->ID, 'video_tags', 'and tagged ', ', ', '.' ); ?>
					</div>
					<br \>
					<div class="entry-content">
						<?php the_content(); ?>
						
						<iframe width="940" height="667" src="http://www.youtube.com/embed/<?php the_field('youtube_video_id'); ?>" frameborder="0" allowfullscreen></iframe>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

					<div class="entry-utility">
						<?php twentyten_posted_in(); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-## -->

					<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>