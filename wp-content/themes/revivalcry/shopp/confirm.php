<div class="full-width-white">
	<h1>Final Confirmation</h1>
	<?php shopp('checkout','cart-summary'); ?>
	
	<form action="<?php shopp('checkout','url'); ?>" method="post" class="shopp" id="checkout">
		<?php shopp('checkout','function','value=confirmed'); ?>
		<p class="note-box tal"><img title="Cards" src="<?php bloginfo('url'); ?>/wp-content/uploads/2011/06/cards.png" alt="Cards" width="144" height="21" /> All payments are process by <img title="Paypal" src="<?php bloginfo('url'); ?>/wp-content/uploads/2011/06/paypal.png" alt="Paypal" width="74" height="21" />.</p>
		<br \>
		<p class="submit"><?php shopp('checkout','confirm-button','value=Confirm Order'); ?></p>
	</form>
</div>