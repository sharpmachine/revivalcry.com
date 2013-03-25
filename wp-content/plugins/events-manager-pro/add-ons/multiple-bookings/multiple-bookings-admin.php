<?php
class EM_Multiple_Bookings_Admin {

    public static function init(){
        add_action( 'admin_notices', 'EM_Multiple_Bookings_Admin::page_warning', 100 );
		add_action( 'em_options_page_footer_emails', 'EM_Multiple_Bookings_Admin::emails', 1);
    }
    
    public static function page_warning(){
        //Warn about EM page edit
        if ( preg_match( '/(post|page).php/', $_SERVER ['SCRIPT_NAME']) && isset ( $_GET ['action'] ) && $_GET ['action'] == 'edit' && isset($_GET['post']) ){
            $cart_page_id = get_option ( 'dbem_multiple_bookings_cart_page' );
            $checkout_page_id = get_option( 'dbem_multiple_bookings_checkout_page' );
            if( in_array($_GET['post'], array($cart_page_id, $checkout_page_id)) ){
                $page_name = $_GET['post'] == $cart_page_id ? __('Cart','em-pro'):__('Checkout','em-pro');
	        	$message = sprintf ( __ ( "This page corresponds to the <strong>Events Manager</strong> %s page. Its content will be overriden by Events Manager, although if you include the word CONTENTS (exactly in capitals) and surround it with other text, only CONTENTS will be overwritten.", 'em-pro' ), $page_name );
	        	$notice = "<div class='error'><p>$message</p></div>";
	        	echo $notice;
            }
        }
    }
    
	public static function emails(){
	    global $save_button;
		?>
		<div  class="postbox " id="em-opt-multiple-booking-emails" >
		<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Multiple Booking Email Templates', 'em-pro' ); ?> </span></h3>
		<div class="inside">
			<table class='form-table'>
				<?php
				$email_subject_tip = __('You can disable this email by leaving the subject blank.','dbem');
				em_options_radio_binary ( __( 'Email event owners?', 'dbem' ), 'dbem_multiple_bookings_contact_email', sprintf(__( 'If enabled, additional emails will be sent to administrators and event owners for EVERY event booked based on the above %s settings.', 'dbem' ), '<code>'.__( 'Booking Email Templates', 'dbem' ).'</code>') );
				?>
				<tr><td colspan='2'><strong style="font-size:1.2em"><?php _e('Event Admin/Owner Emails', 'dbem'); ?></strong></td></tr>
				<tr><td colspan='2'>
					<p><strong><?php _e('Contact person booking confirmed','dbem'); ?></strong></p>
					<em><?php echo __('An email will be sent to the event contact when a booking is first made.','dbem').$bookings_placeholder_tip ?></em>
				</td></tr>
				<?php
				em_options_input_text ( __( 'Contact person email subject', 'dbem' ), 'dbem_multiple_bookings_contact_email_subject', $email_subject_tip );
				em_options_textarea ( __( 'Contact person email', 'dbem' ), 'dbem_multiple_bookings_contact_email_body', '' );
				?>
				<tr><td colspan='2'>
					<p><strong><?php _e('Contact person booking cancelled','dbem') ?></strong></p>
					<em><?php echo __('An email will be sent to the event contact if someone cancels their booking.','dbem').$bookings_placeholder_tip ?></em>
				</td></tr>
				<?php
				em_options_input_text ( __( 'Contact person cancellation subject', 'dbem' ), 'dbem_multiple_bookings_contact__email_cancelled_subject', $email_subject_tip );
				em_options_textarea ( __( 'Contact person cancellation email', 'dbem' ), 'dbem_multiple_bookings_contact__email_cancelled_body', '' );
				?>
				<tr><td colspan='2'><strong style="font-size:1.2em"><?php _e('Booked User Emails', 'dbem'); ?></strong></td></tr>
				<tr><td colspan='2'>
					<p><strong><?php _e('Confirmed booking email','dbem') ?></strong></p>
					<em><?php echo __('This is sent when a person\'s booking is confirmed. This will be sent automatically if approvals are required and the booking is approved. If approvals are disabled, this is sent out when a user first submits their booking.','dbem').$bookings_placeholder_tip ?></em>
				</td></tr>
				<?php
				em_options_input_text ( __( 'Booking confirmed email subject', 'dbem' ), 'dbem_multiple_bookings_email_confirmed_subject', $email_subject_tip );
				em_options_textarea ( __( 'Booking confirmed email', 'dbem' ), 'dbem_multiple_bookings_email_confirmed_body', '' );
				?>
				<tr><td colspan='2'>
					<p><strong><?php _e('Pending booking email','dbem') ?></strong></p>
					<em><?php echo __( 'This will be sent to the person when they first submit their booking. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip ?></em>
				</td></tr>
				<?php
				em_options_input_text ( __( 'Booking pending email subject', 'dbem' ), 'dbem_multiple_bookings_email_pending_subject', $email_subject_tip);
				em_options_textarea ( __( 'Booking pending email', 'dbem' ), 'dbem_multiple_bookings_email_pending_body','') ;
				?>
				<tr><td colspan='2'>
					<p><strong><?php _e('Rejected booking email','dbem') ?></strong></p>
					<em><?php echo __( 'This will be sent automatically when a booking is rejected. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip ?></em>
				</td></tr>
				<?php
				em_options_input_text ( __( 'Booking rejected email subject', 'dbem' ), 'dbem_multiple_bookings_email_rejected_subject', $email_subject_tip );
				em_options_textarea ( __( 'Booking rejected email', 'dbem' ), 'dbem_multiple_bookings_email_rejected_body', '' );
				?>
				<tr><td colspan='2'>
					<p><strong><?php _e('Booking cancelled','dbem') ?></strong></p>
					<em><?php echo __('This will be sent when a user cancels their booking.','dbem').$bookings_placeholder_tip ?></em>
				</td></tr>
				<?php
				em_options_input_text ( __( 'Booking cancelled email subject', 'dbem' ), 'dbem_multiple_bookings_email_cancelled_subject', $email_subject_tip );
				em_options_textarea ( __( 'Booking cancelled email', 'dbem' ), 'dbem_multiple_bookings_email_cancelled_body', '' );
				?>
				<?php echo $save_button; ?>
			</table>
		</div> <!-- . inside -->
		</div> <!-- .postbox -->
		<?php
}

}
EM_Multiple_Bookings_Admin::init();