<ul class="cart-sum">
	<?php if( ! is_page('checkout')): ?>
	<li><img src="<?php bloginfo('template_directory'); ?>/images/cart.png" width="17" height="12" alt="Cart"></li>
	<li><a href="<?php bloginfo('url'); ?>/contact">Help</a></li>
	<li><a href="<?php bloginfo('url'); ?>/store/account" class="super-links">Account</a></li>
	<li class="items-in-cart"><a href="<?php shopp('cart','url'); ?>"><?php if (shopp('cart','hasitems')): ?><?php shopp('cart','totalitems'); ?> item(s)<?php else: ?>0 item(s)<?php endif; ?></a> in your cart</li>
	<li class="last"><a href="<?php bloginfo('url'); ?>/store/checkout" class="checkout">&nbsp;</a></li>
	<?php endif; ?>
</ul>
<?php if (shopp('product','found')): ?>

	
	<div class="span-8 prepend-2 last">

		<div class="span-5 last">
		<h1><?php shopp('product','name'); ?></h1>
	
		<?php if (shopp('product','onsale')): ?>
			<h3 class="original price"><?php shopp('product','price'); ?></h3>
			<h3 class="sale price"><?php shopp('product','saleprice'); ?></h3>
			<?php if (shopp('product','has-savings')): ?>
				<p class="savings">You save <?php shopp('product','savings'); ?> (<?php shopp('product','savings','show=%'); ?>)!</p>
			<?php endif; ?>
		<?php else: ?>
			<h3 class="price"><?php shopp('product','price'); ?></h3>
		<?php endif; ?>
		
		<?php if (shopp('product','freeshipping')): ?>
		<p class="freeshipping">Free Shipping!</p>
		<?php endif; ?>
		
		<?php if(shopp('product','has-specs')): ?>
		<dl class="details">
			<?php while(shopp('product','specs')): ?>
			<dt><?php shopp('product','spec','name'); ?>:</dt><dd><?php shopp('product','spec','content'); ?></dd>
			<?php endwhile; ?>
		</dl>
		<form action="<?php shopp('cart','url'); ?>" method="post" class="shopp product validate">
			<?php if(shopp('product','has-variations')): ?>
			<ul class="variations">
				<?php shopp('product','variations','mode=multiple&label=true&defaults=Select an option&before_menu=<li>&after_menu=</li>'); ?>
			</ul>
			<?php endif; ?>
			<?php if(shopp('product','has-addons')): ?>
				<ul class="addons">
					<?php shopp('product','addons','mode=menu&label=true&defaults=Select an add-on&before_menu=<li>&after_menu=</li>'); ?>
				</ul>
			<?php endif; ?>
	
		<?php shopp('product','description'); ?>
	
		
					
			<p><?php shopp('product','quantity','class=selectall&input=menu'); ?>
			<?php shopp('product','addtocart'); ?></p>
	
		</form>
		</div>
				<div class="span-3 last">
			<?php shopp('product','gallery'); ?>
		</div>
	</div>
	<?php endif; ?>

<?php else: ?>
<h3>Product Not Found</h3>
<p>Sorry! The product you requested is not found in our catalog!</p>
<?php endif; ?>