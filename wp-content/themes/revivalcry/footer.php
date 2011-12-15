
	</section><!-- #page -->
</div><!-- .container -->

<div id="page-gradient">
	&nbsp;
</div><!-- #page-gradient -->

	<footer role="contentinfo">
		<div id="footer">
			
		</div><!-- #footer -->


			<div id="site-info">
				&copy;<?php echo date ('Y'); ?><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?> Ministries
				</a> | <a href="<?php bloginfo('url'); ?>/privacy-policy">Privacy Policy</a> | <a href="<?php bloginfo('url'); ?>/terms-conditions">Terms &amp; Conditions</a>
				<?php if (page_is("store")): ?>
					| <a href="<?php bloginfo('url'); ?>/shipping-returns-policy">Shipping &amp; Returns Policy</a>
				<?php endif; ?>
			</div><!-- #site-info -->

	</footer>


  
<?php wp_footer(); ?>

  <!-- scripts concatenated and minified via ant build script-->
  <script src="<?php bloginfo ('template_directory'); ?>/js/plugins.js"></script>
  <script src="<?php bloginfo ('template_directory'); ?>/js/script.js"></script>

	<!-- Remove these before deploying to production -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="<?php bloginfo ('template_directory'); ?>/js/hashgrid.js" type="text/javascript"></script>
</body>
</html>
