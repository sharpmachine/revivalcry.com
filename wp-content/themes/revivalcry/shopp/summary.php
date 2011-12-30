<?php if (shopp('cart','hasitems')): ?>

<div id="cart" class="shopp">
	<table class="cart">
		<tr>
			<th scope="col" class="item tal">Items</th>
			<th scope="col">Item Price</th>
			<th scope="col">QTY</th>
			<th scope="col">Item Total</th>
		</tr>
	
		<?php while(shopp('cart','items')): ?>
			<tr class="cart-item">
				
				<td class="item">
					<a href="<?php shopp('cartitem','url'); ?>"><?php shopp('cartitem','name'); ?></a>
					<?php shopp('cartitem','options'); ?>
					<?php shopp('cartitem','addons-list'); ?>
					<?php shopp('cartitem','inputs-list'); ?>
				</td>
				<td class="money"><?php shopp('cartitem','unitprice'); ?></td>
				<td class="quantity"><?php shopp('cartitem','quantity'); ?></td>
				<td class="total"><?php shopp('cartitem','total'); ?></td>
			</tr>
	
		<?php endwhile; ?>
		
		<?php while(shopp('cart','promos')): ?>
			<tr><td colspan="4" class="money"><?php shopp('cart','promo-name'); ?><strong><?php shopp('cart','promo-discount',array('before' => '&nbsp;&mdash;&nbsp;')); ?></strong></td></tr>
		<?php endwhile; ?>
		
		<tr class="totals">
			<td colspan="2" rowspan="5">

				<?php if ((shopp('cart','has-shipping-methods'))): ?>
				<strong>Select a shipping method:</strong>
				
				<form action="<?php shopp('shipping','url') ?>" method="post">
				
				<ul id="shipping-methods">
				<?php while(shopp('shipping','methods')): ?>
					<li><span><label><?php shopp('shipping','method-selector'); ?>
					<?php shopp('shipping','method-name'); ?> &mdash;
					<strong><?php shopp('shipping','method-cost'); ?></strong><br />
					<small><?php shopp('shipping','method-delivery'); ?></small></label></span>
					</li>
				<?php endwhile; ?>
				</ul>
				</form>
				<?php else: ?>
					<h4 style="color:#CB3333;">Oops, we have a problem : (</h4>
					<p><strong>You need to return to your cart and enter your zip code before you can select a shipping method</strong></p>
					<a href="<?php bloginfo('url'); ?>/store/cart" class="button">Back to Cart</a>
				<?php endif; ?>
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
			<th scope="row" class="subtotal-cart"><?php shopp('cart','tax','label=Taxes'); ?></th>
			<td class="total"><?php shopp('cart','tax'); ?></td>
		</tr>
		<tr class="total">
			<th scope="row" class="subtotal-cart">Total</th>
			<td class="total"><?php shopp('cart','total'); ?></td>
		</tr>
	</table>
<?php if(shopp('checkout','hasdata')): ?>
	<ul>
	<?php while(shopp('checkout','orderdata')): ?>
		<li><strong><?php shopp('checkout','data','name'); ?>:</strong> <?php shopp('checkout','data'); ?></li>
	<?php endwhile; ?>
	</ul>
<?php endif; ?>

</div>
<?php else: ?>
	<p class="warning">There are currently no items in your shopping cart.</p>
<?php endif; ?>
