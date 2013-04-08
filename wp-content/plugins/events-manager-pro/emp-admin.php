<?php
/**
 * Used for hooking into the admin page and adding extra settings specific to Events Manager Pro, meaning classes do not need to be loaded until enabled here. 
 */
class EM_Pro_Admin{
    public static function init(){
        //Multiple Bookings
        add_action('em_options_page_footer_bookings', 'EM_Pro_Admin::multiple_bookings_settings');
        //Logging
        add_action('em_options_page_panel_admin_tools', 'EM_Pro_Admin::logging_settings');
        add_action('add_option_dbem_enable_logging', 'EM_Pro_Admin::logging_enable', 10, 2);
        add_action('update_option_dbem_enable_logging', 'EM_Pro_Admin::logging_enable', 10, 2);
    }
    
    /**
     * Settings for Multiple Bookings Mode 
     */
    public static function multiple_bookings_settings(){
        global $save_button;
        ?>
        <div  class="postbox " id="em-opt-multiple-bookings" >
        	<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e('Multiple Bookings Mode','em-pro'); ?> <em>(Beta)</em></span></h3>
        	<div class="inside">
        		<table class='form-table'>
        			<tr><td colspan='2'>
        				<p>
        					<?php _e('Multiple Bookings Mode enables your visitors to make bookings follow a flow similar to that of a shopping cart, meaning users can book multiple events and pay for them in one go.','em-pro'); ?>
        					<a href="http://wp-events-plugin.com/documentation/multiple-booking-mode/"><?php echo sprintf(__('More about %s.','em-pro'), __('Multiple Bookings Mode','em-pro')); ?></a>
        				</p>
        			</td></tr>
        			<?php
        			em_options_radio_binary ( __( 'Enable Muliple Bookings Mode?', 'em-pro' ), 'dbem_multiple_bookings' );
        			?>
        			<tbody id="dbem-js-multiple-bookings">
        				<tr>
							<td><?php echo __( 'Checkout Page', 'em-pro'); ?></td>
							<td>
								<?php wp_dropdown_pages(array('name'=>'dbem_multiple_bookings_checkout_page', 'selected'=>get_option('dbem_multiple_bookings_checkout_page'), 'show_option_none'=>sprintf(__('Select ...', 'dbem')) )); ?>
								<br />
								<em>
									<?php
									echo __('This page will be where the user reviews their bookings, enters additional information (such as user registration info) and proceeds with payment.','em-pro');
									echo ' '. sprintf(__( 'Please <a href="%s">add a new page</a> and assign it here. This is required for Multiple Bookings Mode to work.','em-pro'), 'post-new.php?post_type=page'); 
									?>
								</em>
							</td>
						</tr>
	        			<?php
	        				em_options_select( __('Checkout Page Booking Form','em-pro'), 'dbem_muliple_bookings_form', EM_Booking_Form::get_forms_names(), __('This form will be shown on the checkout page, which should include user fields you may want when registering new users. Any non-user fields will be added as supplementary information to every booking, if you have identical Field IDs on the individual event booking form, that field value will be saved to the individual booking instead.','em-pro'));
	        			?>
        				<tr>
							<td><?php echo __( 'Cart Page', 'em-pro'); ?></td>
							<td>
								<?php wp_dropdown_pages(array('name'=>'dbem_multiple_bookings_cart_page', 'selected'=>get_option('dbem_multiple_bookings_cart_page'), 'show_option_none'=>sprintf(__('Select ...', 'dbem')) )); ?>
								<br />
								<em><?php 
									echo __('This page will display the events the user has chosen to book and allow them to edit their bookings before checkout.','em-pro');
									echo ' '.sprintf(__( 'Please <a href="%s">add a new page</a> and assign it here. This is required for Multiple Bookings Mode to work.','em-pro'), 'post-new.php?post_type=page'); ?>
								</em>
							</td>
						</tr>
	        			<?php
	        				em_options_input_text( __('Successfully Added Message'), 'dbem_multiple_bookings_feedback_added', __('A booking was successfull added to the bookings cart.','em-pro'));
	        				em_options_input_text( __('Loading Cart Contents'), 'dbem_multiple_bookings_feedback_loading_cart', __('If caching plugins are used, cart contents are loaded after a page load and this text is shown whilst loading.','em-pro'));
	        				em_options_input_text( __('Event Already Booked'), 'dbem_multiple_bookings_feedback_already_added', __('This event has already been added to the cart and cannot be added twice.','em-pro'));
	        				em_options_input_text( __('No Bookings'), 'dbem_multiple_bookings_feedback_no_bookings', __('User has not booked any events yet, cart is empty.','em-pro'));
	        				em_options_input_text( __('Empty Cart Warning'), 'dbem_multiple_bookings_feedback_empty_cart', __('Warning after the "empty cart" button is clicked.','em-pro'));
	        			?>
        			</tbody>
        			<?php echo $save_button; ?>
        		</table>
        	</div> <!-- . inside -->
        </div> <!-- .postbox -->
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('input:radio[name="dbem_multiple_bookings"]').change(function(){
					if( $('input:radio[name="dbem_multiple_bookings"]:checked').val() == 1 ){
						$('tbody#dbem-js-multiple-bookings').show();
					}else{
						$('tbody#dbem-js-multiple-bookings').hide();					
					}
				}).first().trigger('change');
			});
		</script>
		<?php
    }
    
    /* START Logging */
	public static function logging_settings(){
	    ?>
	    <h4 style="font-size:1.1em;"><?php _e ( 'Logging', 'dbem' ); ?></h4>
		<table class="form-table">
			<?php em_options_radio_binary ( __( 'Enable Logging?', 'em-pro' ), 'dbem_enable_logging', sprintf(__('If enabled, a folder called %s will be created. Please ensure that your wp-contents folder is writable by the server.'), '<code>'.WP_PLUGIN_DIR.'events-manager-logs'.'</code>')); ?>
		</table>
		<?php
	}
	
	public static function logging_enable($old_val, $new_val){
		global $EM_Notices;
		if( $new_val && $new_val != $old_val ){
			if( !EM_Pro::log('Logging Enabled','general', true) ){
				$EM_Notices->add_error(__('Could not create a log directory, please make sure your wp-content is writeable.'.'em-pro'));
			}
		}
	}
	/* END Logging */
}
EM_Pro_Admin::init();