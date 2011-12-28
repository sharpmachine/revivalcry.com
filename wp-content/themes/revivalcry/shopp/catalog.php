<ul class="cart-sum">
	<?php if( ! is_page('checkout')): ?>
	<li><img src="<?php bloginfo('template_directory'); ?>/images/cart.png" width="17" height="12" alt="Cart"></li>
	<li><a href="<?php bloginfo('url'); ?>/contact">Help</a></li>
	<li><a href="<?php bloginfo('url'); ?>/store/account" class="super-links">Account</a></li>
	<li class="items-in-cart"><a href="<?php shopp('cart','url'); ?>"><?php if (shopp('cart','hasitems')): ?><?php shopp('cart','totalitems'); ?> item(s)<?php else: ?>0 item(s)<?php endif; ?></a> in your cart</li>
	<li class="last"><a href="<?php bloginfo('url'); ?>/store/checkout" class="checkout">&nbsp;</a></li>
	<?php endif; ?>
	<?php shopp('catalog','searchform'); ?>
</ul>

<?php if(shopp('category','hasproducts','load=prices,images')): ?>
	<div class="category">

	<h3><?php shopp('category','name'); ?></h3>
	<?php shopp('catalog','views','label=Views: '); ?>
	
	<p><?php shopp('category','subcategory-list','hierarchy=true&showall=true&class=subcategories&dropdown=1'); ?></p>
	
	
	<?php shopp('catalog','category-list','dropdown=on'); ?><?php shopp('catalog','searchform'); ?>
	<div class="alignright"><?php shopp('category','pagination','show=10'); ?></div>
	

	<ul class="products">
		<li class="row"><ul>
		<?php while(shopp('category','products')): ?>
		<?php if(shopp('category','row')): ?></ul></li><li class="row"><ul><?php endif; ?>
			<li class="product">
				<div class="frame">
					<div class="image">
						<a href="<?php shopp('product','url'); ?>"><?php shopp('product','coverimage'); ?></a>
					</div>
				
					<div class="details">
					<h4 class="name"><a href="<?php shopp('product','url'); ?>"><?php shopp('product','name'); ?></a></h4>
					<p class="price"><?php shopp('product','saleprice','starting=from'); ?> </p>
					<?php if (shopp('product','has-savings')): ?>
						<p class="savings">Save <?php shopp('product','savings','show=percent'); ?></p>
					<?php endif; ?>
					
						<div class="listview">
						<p><?php shopp('product','summary'); ?></p>
						<form action="<?php shopp('cart','url'); ?>" method="post" class="shopp product">
						<?php shopp('product','addtocart'); ?>
						</form>
						</div>
					</div>
					
				</div>
			</li>
		<?php endwhile; ?>
		</ul></li>
	</ul>
	
	<div class="alignright"><?php shopp('category','pagination'); ?></div>
	
	</div>
<?php else: ?>
	<?php if (!shopp('catalog','is-landing')): ?>
	<?php shopp('catalog','breadcrumb'); ?>
	<h3><?php shopp('category','name'); ?></h3>
	<p>No products were found.</p>
	<?php endif; ?>
<?php endif; ?>
<div class="encourage-box">
Sign up for our <a href="#footer" class="scroll-to">newsletter</a> or subscribe to our <a href="<?php bloginfo('url'); ?>/store/category/catalog/feed/"> Store rss feed</a> to be notified about our latest products
</div>