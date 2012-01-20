<?php 
/*
	Template Name: Blog
*/
get_header(); ?>
		<div class="full-width-white">
			<div id="content-container" class="span-7 prepend-1">
				<section id="content" role="main">
	
				<?php get_template_part( 'loop', 'blog' ); ?>
				
				<?php rewind_posts(); ?>
				
				<?php
					$temp = $wp_query;
					$wp_query= null;
					$wp_query = new WP_Query();
					$wp_query->query('&paged='.$paged);
					while ($wp_query->have_posts()) : $wp_query->the_post();
				?>
				
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="span-2">
						<div class="post-date">
							<?php the_time('d') ?>
						</div>
						<a href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) {
							the_post_thumbnail( array (140, 140) );
							} else { ?>
							<img src="<?php bloginfo('template_directory'); ?>/images/default-post-thumb.jpg" alt="<?php the_title(); ?>" class="post-thumb" />
							<?php } ?>
						</a>
					</div>
					
					<div class="span-5 last">
						
					
						<h2 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title();  ?></a>
						</h2>
						<div class="post-author"><?php the_author_posts_link(); ?></div>
						<p class="posted-on-date">Posted on <?php the_time('F j, Y'); ?></p>
						
						<p><?php echo blog_landing_excerpt(); ?></p>
						
						<p><a href="<?php the_permalink(); ?>" class="button">Read More</a></p>
						
						<p class="comment-count"><a href="<?php comments_link(); ?> "><?php comments_number( ); ?></a></p>
					
						<div class="entry-utility">
						<?php if ( count( get_the_category() ) ) : ?>
							<span class="cat-links">
								<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
							</span>
						<?php endif; ?>
						<?php
							$tags_list = get_the_tag_list( '', ', ' );
							if ( $tags_list ):
						?>
						<span class="meta-sep">|</span>
							<span class="tag-links">
								<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
							</span>
						<?php endif; ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
						</div><!-- .entry-utility -->
					</div>
					<div class="clear"></div>
				</div>
				<?php endwhile; ?>
		
				<?php if (  $wp_query->max_num_pages > 1 ) : ?>
								<?php if(function_exists('wp_paginate')) {
				    wp_paginate();
				} ?>
				
				<?php endif; ?>
	
				<?php $wp_query = null; $wp_query = $temp;?>
				
				</section><!-- #content -->
				
			</div><!-- #content-container -->

<?php get_sidebar(); ?>
			<div class="clear"></div>
		</div><!-- .full-width-white -->
<?php get_footer(); ?>
