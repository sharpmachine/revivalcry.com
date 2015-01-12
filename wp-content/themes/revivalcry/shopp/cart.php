<div class="full-width-white">
<ul class="cart-sum">
	<?php if( ! is_page('checkout')): ?>
	<li><img src="<?php bloginfo('template_directory'); ?>/images/cart.png" width="17" height="12" alt="Cart"></li>
	<li><a href="<?php bloginfo('url'); ?>/contact">Help</a></li>
	<li><a href="<?php bloginfo('url'); ?>/store/account" class="super-links">Account</a></li>
	<li class="items-in-cart"><a href="<?php shopp('cart','url'); ?>"><?php if (shopp('cart','hasitems')): ?><?php shopp('cart','totalitems'); ?> item(s)<?php else: ?>0 item(s)<?php endif; ?></a> in your cart</li>
	<li class="last"><a href="<?php bloginfo('url'); ?>/store/checkout" class="checkout">&nbsp;</a></li>
	<?php endif; ?>
</ul>

<h1><?php the_title(); ?></h1>
<?php if (shopp('cart','hasitems')): ?>
	<div class="shopp-buttons top">
		<a href="<?php shopp('cart','referrer'); ?>" class="button">Continue Shopping</a>
		<a href="<?php shopp('checkout','url'); ?>" class="button">Checkout</a>
	</div>
<form id="cart" action="<?php shopp('cart','url'); ?>" method="post">
<?php shopp('cart','function'); ?>
<table class="cart">
	<tr>
		<th scope="col" class="item tal">Items</th>
		<th scope="col">&nbsp;</th>
		<th scope="col">Item Price</th>
		<th scope="col">QTY</th>
		<th scope="col">Item Total</th>
		
	</tr>

	<?php while(shopp('cart','items')): ?>
		<tr class="cart-item">
			
			<!--<td class="item-thumb">
				<?php shopp('cartitem','coverimage','size=60'); ?>
			</td>-->
			
			<td class="item">
				<!--<div class="cart-thumbnail"><?php shopp('cartitem','thumbnail'); ?></div>-->
				<a href="<?php shopp('cartitem','url'); ?>"><?php shopp('cartitem','name'); ?></a>
				<?php shopp('cartitem','options'); ?>
				<?php shopp('cartitem','addons-list'); ?>
				<?php shopp('cartitem','inputs-list'); ?>
			</td>
			<td><?php shopp('cartitem','remove','input=button'); ?></td>
			<td class="money"><?php shopp('cartitem','unitprice'); ?></td>
			<td class="quantity"><?php shopp('cartitem','quantity','input=text'); ?></td>
			
			<td class="total"><?php shopp('cartitem','total'); ?></td>
			
		</tr>

	<?php endwhile; ?>
	<?php while(shopp('cart','promos')): ?>
		<tr><td colspan="4" class="money"><?php shopp('cart','promo-name'); ?><strong><?php shopp('cart','promo-discount',array('before' => '&nbsp;&mdash;&nbsp;')); ?></strong></td></tr>
	<?php endwhile; ?>
	
	<tr class="totals">
		<td colspan="3" rowspan="5">
			<?php if (shopp('cart','needs-shipping-estimates')): ?>
			<div style="display:none;">
			<strong>Enter Your Zip Code:</strong>
			<?php shopp('cart','shipping-estimates'); ?>
			</div>
			<?php endif; ?>
			<?php shopp('cart','promo-code'); ?>
		</td>
		<th scope="row" class="subtotal-cart">Subtotal</th>
		<td class="total"><?php shopp('cart','subtotal'); ?></td>
	</tr>
	<?php if (shopp('cart','hasdiscount')): ?>
	<tr class="">
		<th scope="row" class="subtotal-cart">Discount</th>
		<td class="total">-<?php shopp('cart','discount'); ?></td>
	</tr>
	<?php endif; ?>
	<?php if (shopp('cart','needs-shipped')): ?>
	<tr class="">
		<th scope="row" class="subtotal-cart"><?php shopp('cart','shipping','label=Shipping'); ?></th>
		<td class="total"><?php shopp('cart','shipping'); ?></td>
	</tr>
	<?php endif; ?>
	<tr class="">
		<th scope="row" class="subtotal-cart"><?php shopp('cart','tax','label=Tax'); ?></th>
		<td class="total"><?php shopp('cart','tax'); ?></td>
	</tr>
	<tr class="total" class="subtotal-cart">
		<th scope="row" class="subtotal-cart">Total</th>
		<td class="total"><?php shopp('cart','total'); ?></td>
		<td></td>
	</tr>
				<tr class="buttons">
			<td colspan="6"><br \><?php shopp('cart','update-button'); ?></td>
		</tr>
</table>

	<div class="shopp-buttons">
		<a href="<?php shopp('cart','referrer'); ?>" class="button">Continue Shopping</a>
		<a href="<?php shopp('checkout','url'); ?>" class="button">Checkout</a>
	</div>

</form>

<?php else: ?>
<div class="clear">
	<p class="warning">There are currently no items in your shopping cart.</p>
	<p><a href="<?php shopp('cart','referrer'); ?>" class="button">Continue Shopping</a></p>
</div>
<?php endif; ?>
</div><!-- .full-width-white -->