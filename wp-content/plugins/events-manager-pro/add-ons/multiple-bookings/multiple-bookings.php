<?php
class EM_Multiple_Bookings{
    
    public static $booking_data;
    public static $session_started = false;
    
    public static function init(){
		include('multiple-booking.php');
		include('multiple-bookings-widget.php');
		//admin stuff
		if( is_admin() ){
		    include('multiple-bookings-admin.php');
		}
		add_filter('em_get_booking','EM_Multiple_Bookings::em_get_booking');
        //hooking into the booking process
        add_action('em_booking_add','EM_Multiple_Bookings::em_booking_add', 1, 3); //prevent booking being made and add to cart
		//modify traditional booking forms behaviour
		add_action('em_booking_form_custom','EM_Multiple_Bookings::prevent_user_fields', 1); //prevent user fields from showing
		add_filter('em_booking_validate', 'EM_Multiple_Bookings::prevent_user_validation', 1); //prevent user fields validation
		//cart/checkout pages
		add_filter('the_content', 'EM_Multiple_Bookings::pages');
		//ajax calls for cart checkout
		add_action('wp_ajax_emp_checkout_remove_item','EM_Multiple_Bookings::remove_booking');
		add_action('wp_ajax_nopriv_emp_checkout_remove_item','EM_Multiple_Bookings::remove_booking');
		//ajax calls for cart checkout
		add_action('wp_ajax_emp_checkout','EM_Multiple_Bookings::checkout');
		add_action('wp_ajax_nopriv_emp_checkout','EM_Multiple_Bookings::checkout');
		//ajax calls for cart contents		
		add_action('wp_ajax_em_cart_page_contents','EM_Multiple_Bookings::cart_page_contents_ajax');
		add_action('wp_ajax_nopriv_em_cart_page_contents','EM_Multiple_Bookings::cart_page_contents_ajax');
		add_action('wp_ajax_em_checkout_page_contents','EM_Multiple_Bookings::checkout_page_contents_ajax');
		add_action('wp_ajax_nopriv_em_checkout_page_contents','EM_Multiple_Bookings::checkout_page_contents_ajax');
		add_action('wp_ajax_em_cart_contents','EM_Multiple_Bookings::cart_contents_ajax');
		add_action('wp_ajax_nopriv_em_cart_contents','EM_Multiple_Bookings::cart_contents_ajax');
		//cart content widget and shortcode
		add_action('wp_ajax_em_cart_widget_contents','EM_Multiple_Bookings::cart_widget_contents_ajax');
		add_action('wp_ajax_nopriv_em_cart_widget_contents','EM_Multiple_Bookings::cart_widget_contents_ajax');
		add_shortcode('em_cart_contents', 'EM_Multiple_Bookings::cart_contents');
		add_action('em_booking_js_footer', 'EM_Multiple_Bookings::em_booking_js_footer');
		//booking admin pages
		add_action('em_bookings_admin_page', 'EM_Multiple_Bookings::bookings_admin_notices');
		add_action('em_bookings_multiple_booking', 'EM_Multiple_Bookings::booking_admin',1,1);
    }
    
    public static function em_get_booking($EM_Booking){
        if( !empty($EM_Booking->booking_id) && $EM_Booking->event_id == 0 ){
            return new EM_Multiple_Booking($EM_Booking);
        }
        return $EM_Booking;
    }
    
    /**
     * Starts a session, and returns whether session successfully started or not.
     * We can start a session after the headers are sent in this case, it's ok if a session was started earlier, since we're only grabbing server-side data
     */
    public static function session_start(){
        global $EM_Notices;
        if( !self::$session_started ){
            self::$session_started = @session_start();
        }
        return self::$session_started;
    }
    
    /**
     * Grabs multiple booking from session, or creates a new multiple booking object
     * @return EM_Multiple_Booking
     */
    public static function get_multiple_booking(){
        if( empty(self::$booking_data) ){
	        self::session_start();
	        if( !empty($_SESSION['em_multiple_bookings']) && get_class($_SESSION['em_multiple_bookings']) == 'EM_Multiple_Booking' ){
	            self::$booking_data = $_SESSION['em_multiple_bookings'];
	        }else{
	            self::$booking_data = $_SESSION['em_multiple_bookings'] = new EM_Multiple_Booking();
	        }
        }
        return self::$booking_data; 
    }
    
    public static function save_multiple_booking(){
        //probably won't be used due to object referencing in PHP5
        $_SESSION['em_multiple_bookings'] = self::get_multiple_booking();
    }
    
    public static function prevent_user_fields(){
		add_filter('emp_form_show_reg_fields', create_function('','return false;'));
    }
    
    public static function prevent_user_validation($result){
        self::prevent_user_fields();
        return $result;        
    }
    
    /**
     * Hooks into em_booking_add ajax action early and prevents booking from being saved to the database, instead it adds the booking to the bookings cart.
     * If this is not an AJAX request (due to JS issues) then a redirect is made after processing the booking.
     * @param EM_Event $EM_Event
     * @param EM_Booking $EM_Booking
     * @param boolean $post_validation
     */
    public static function em_booking_add( $EM_Event, $EM_Booking, $post_validation ){
        global $EM_Notices;
        if( self::session_start() ){
	        if ( $post_validation ) {
	            //booking can be added to cart
	            if( self::get_multiple_booking()->add_booking($EM_Booking) ){
	                $result = true;
		            $feedback = get_option('dbem_multiple_bookings_feedback_added');
		            $EM_Notices->add_confirm( $feedback, !defined('DOING_AJAX') ); //if not ajax, make this notice static for redirect
	            }else{
	                $result = false;
	                $feedback = '';
	                $EM_Notices->add_error( $EM_Booking->get_errors(), !defined('DOING_AJAX') ); //if not ajax, make this notice static for redirect
	            }
	        }else{
				$result = false;
				$EM_Notices->add_error( $EM_Booking->get_errors() );
			}
        }else{
			$EM_Notices->add_error(__('Sorry for the inconvenience, but we are having technical issues adding your bookings, please contact an administrator about this issue.','em-pro'), !defined('DOING_AJAX'));
        }
		ob_clean(); //em_booking_add uses ob_start(), so flush it here
		if( defined('DOING_AJAX') ){
			$return = array('result'=>$result, 'message'=>$feedback, 'errors'=> $EM_Notices->get_errors());
            echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Booking));
		}else{
			wp_redirect(wp_get_referer());
		}
	    die();
    }
    
    public static function remove_booking(){
        $EM_Multiple_Booking = self::get_multiple_booking();
		if( !empty($_REQUEST['event_id']) && !empty($EM_Multiple_Booking->bookings[$_REQUEST['event_id']]) ){
		    unset($EM_Multiple_Booking->bookings[$_REQUEST['event_id']]);
		    $result = true;
		}else{
		    $result = false;
		    $feedback = __('Could not remove booking due to an unexpected error.', 'em-pro');
		}
        if( defined('DOING_AJAX') ){
        	$return = array('result'=>$result, 'message'=>$feedback);
        	echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Booking));
        }else{
        	wp_redirect(wp_get_referer());
        }
        die();
    }
    
    public static function checkout(){
        global $EM_Notices;
		check_ajax_referer('emp_checkout');
		$EM_Multiple_Booking = self::get_multiple_booking();
        //remove filters so that our master booking validates user fields
		remove_action('em_booking_form_custom','EM_Multiple_Bookings::prevent_user_fields', 1); //prevent user fields from showing
		remove_filter('em_booking_validate', 'EM_Multiple_Bookings::prevent_user_validation', 1); //prevent user fields validation
		//now validate the master booking
        $EM_Multiple_Booking->get_post();
        $post_validation = $EM_Multiple_Booking->validate();
		//re-add filters to prevent individual booking problems
		add_action('em_booking_form_custom','EM_Multiple_Bookings::prevent_user_fields', 1); //prevent user fields from showing
		add_filter('em_booking_validate', 'EM_Multiple_Bookings::prevent_user_validation', 1); //prevent user fields validation
		$bookings_validation = $EM_Multiple_Booking->validate_bookings();
		//fire the equivalent of the em_booking_add action, but multiple variation 
		do_action('em_multiple_booking_add', $EM_Multiple_Booking->get_event(), $EM_Multiple_Booking, $post_validation && $bookings_validation); //get_event returns blank, just for backwards-compatabaility
		//proceed with saving bookings if all is well
        if( $bookings_validation && $post_validation ){
			//save user registration
       	    $registration = em_booking_add_registration($EM_Multiple_Booking);

        	//save master booking, which in turn saves the other bookings too
        	if( $registration && $EM_Multiple_Booking->save_bookings() ){
        	    $result = true;
        		$EM_Notices->add_error( $EM_Multiple_Booking->feedback_message );
        		$feedback = $EM_Multiple_Booking->feedback_message;
        		unset($_SESSION['em_multiple_bookings']); //we're done with this checkout!
        	}else{
        		$result = false;
        		$EM_Notices->add_error( $EM_Multiple_Booking->get_errors() );
        		$feedback = $EM_Multiple_Booking->feedback_message;
        	}
        	global $em_temp_user_data; $em_temp_user_data = false; //delete registered user temp info (if exists)
        }else{
            $EM_Notices->add_error( $EM_Multiple_Booking->get_errors() );
        }
		if( defined('DOING_AJAX') ){
		    if( $result ){
				$return = array('result'=>true, 'message'=>$feedback);
				echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Multiple_Booking));
			}elseif( !$result ){
				$return = array('result'=>false, 'message'=>$feedback, 'errors'=>$EM_Notices->get_errors());
				echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Multiple_Booking));
			}
			die();
		}
    }
    
    /**
     * Hooks into the_content and checks if this is a checkout or cart page, and if so overwrites the page content with the relevant content. Uses same concept as em_content.
     * @param string $page_content
     * @return string
     */
    public static function pages($page_content) {
    	global $post, $wpdb, $wp_query, $EM_Event, $EM_Location, $EM_Category;
    	if( empty($post) ) return $page_content; //fix for any other plugins calling the_content outside the loop
    	$cart_page_id = get_option ( 'dbem_multiple_bookings_cart_page' );
    	$checkout_page_id = get_option( 'dbem_multiple_bookings_checkout_page' );
    	if( in_array($post->ID, array($cart_page_id, $checkout_page_id)) ){
    		ob_start();
    		if( $post->ID == $cart_page_id && $cart_page_id != 0 ){
    			self::cart_page();
    		}elseif( $post->ID == $checkout_page_id && $checkout_page_id != 0 ){
    			self::checkout_page();
    		}
    		$content = ob_get_clean();
    		//Now, we either replace CONTENTS or just replace the whole page
    		if( preg_match('/CONTENTS/', $page_content) ){
    			$content = str_replace('CONTENTS',$content,$page_content);
    		}
    		return $content;
    	}
    	return $page_content;
    }
    
    public static function cart_contents_ajax(){
    	emp_locate_template('multiple-bookings/cart-table.php', true);
    	die();
    }
    
    /* Checkout Page Code */
    
    public static function em_booking_js_footer(){
        if( !defined('EM_CART_JS_LOADED') ){
	        include('multiple-bookings.js');
			define('EM_CART_JS_LOADED',true);
        }
    }
	
	public static function checkout_page_contents_ajax(){
		emp_locate_template('multiple-bookings/page-checkout.php',true);
		die();
	}

	public static function checkout_page(){
	    if( !EM_Multiple_Bookings::get_multiple_booking()->validate_bookings_spaces() ){
	        global $EM_Notices;
	        $EM_Notices->add_error(EM_Multiple_Bookings::get_multiple_booking()->get_errors());
	    }
		//load contents if not using caching, do not alter this conditional structure as it allows the cart to work with caching plugins
		echo '<div class="em-checkout-page-contents" style="position:relative;">';
		if( !defined('WP_CACHE') || !WP_CACHE ){
			emp_locate_template('multiple-bookings/page-checkout.php',true);
		}else{
			echo '<p>'.get_option('dbem_multiple_bookings_feedback_loading_cart').'</p>';
		}
		echo '</div>';
		EM_Bookings::enqueue_js();
    }
    
    /* Shopping Cart Page */
	
	public static function cart_page_contents_ajax(){
		emp_locate_template('multiple-bookings/page-cart.php',true);
		die();
	}
        
    public static function cart_page(){
		if( !EM_Multiple_Bookings::get_multiple_booking()->validate_bookings_spaces() ){
			global $EM_Notices;
			$EM_Notices->add_error(EM_Multiple_Bookings::get_multiple_booking()->get_errors());
		}
		//load contents if not using caching, do not alter this conditional structure as it allows the cart to work with caching plugins
		echo '<div class="em-cart-page-contents" style="position:relative;">';
		if( !defined('WP_CACHE') || !WP_CACHE ){
			emp_locate_template('multiple-bookings/page-cart.php',true);
		}else{
			echo '<p>'.get_option('dbem_multiple_bookings_feedback_loading_cart').'</p>';
		}
		echo '</div>';
		if( !defined('EM_CART_JS_LOADED') ){
			//load 
			function em_cart_js_footer(){
				?>
				<script type="text/javascript">
					<?php include('multiple-bookings.js'); ?>
				</script>
				<?php
			}
			add_action('wp_footer','em_cart_js_footer');
			add_action('admin_footer','em_cart_js_footer');
			define('EM_CART_JS_LOADED',true);
		}
	}
    
    /* Shopping Cart Widget */
    
    public static function cart_widget_contents_ajax(){
        emp_locate_template('multiple-bookings/widget.php', true, array('instance'=>$_REQUEST));
        die();
    }
    
    public static function cart_contents( $instance ){
		$defaults = array(
				'title' => __('Event Bookings Cart','em-pro'),
				'format' => '#_EVENTLINK - #_EVENTDATES<ul><li>#_BOOKINGSPACES Spaces - #_BOOKINGPRICE</li></ul>',
				'loading_text' =>  __('Loading...','em-pro'),
				'checkout_text' => __('Checkout','em-pro'),
				'cart_text' => __('View Cart','em-pro'),
				'no_bookings_text' => __('No events booked yet','em-pro')
		);
		$instance = array_merge($defaults, (array) $instance);
		ob_start();
		?>
		<div class="em-cart-widget">
			<form>
				<input type="hidden" name="action" value="em_cart_widget_contents" />
				<input type="hidden" name="format" value="<?php echo $instance['format'] ?>" />
				<input type="hidden" name="cart_text" value="<?php echo $instance['cart_text'] ?>" />
				<input type="hidden" name="checkout_text" value="<?php echo $instance['checkout_text'] ?>" />
				<input type="hidden" name="no_bookings_text" value="<?php echo $instance['no_bookings_text'] ?>" />
				<input type="hidden" name="loading_text" value="<?php echo $instance['loading_text'] ?>" />
			</form>
			<div class="em-cart-widget-contents">
				<?php if( !defined('WP_CACHE') || !WP_CACHE ) emp_locate_template('multiple-bookings/widget.php', true, array('instance'=>$instance)); ?>
			</div>
		</div>
		<?php		
		if( !defined('EM_CART_WIDGET_JS_LOADED') ){ //load cart widget JS once per page
			function em_cart_widget_js_footer(){
				?>
				<script type="text/javascript">
					<?php include('cart-widget.js'); ?>
				</script>
				<?php
			}
			add_action('wp_footer','em_cart_widget_js_footer', 1000);
			define('EM_CART_WIDGET_JS_LOADED',true);
		}
		return ob_get_clean();
	}
    
    /* Admin Stuff */
    public static function bookings_admin_notices(){
		global $EM_Booking, $EM_Notices;
		if( current_user_can('manage_others_bookings') ){
	    	if( !empty($EM_Booking) && get_class($EM_Booking) != 'EM_Multiple_Booking' ){
				//is this part of a multiple booking?
				$EM_Multiple_Booking = self::get_main_booking( $EM_Booking );
				if( $EM_Multiple_Booking !== false ){
					$EM_Notices->add_info(sprintf(__('This single booking is part of a larger booking made by this person at once. <a href="%s">View Main Booking</a>.','em-pro'), $EM_Multiple_Booking->get_admin_url()));
					echo $EM_Notices;
				}
			}elseif( !empty($EM_Booking) && get_class($EM_Booking) == 'EM_Multiple_Booking' ){
				$EM_Notices->add_info(__('This booking contains a set of bookings made by this person. To edit particular bookings click on the relevant links below.','em-pro'));
				echo $EM_Notices;
			}
		}
    }
    
    public static function get_main_booking( $EM_Booking ){
		global $wpdb;
		$main_booking_id = $wpdb->get_var($wpdb->prepare('SELECT booking_main_id FROM '.EM_BOOKINGS_RELATIONSHIPS_TABLE.' WHERE booking_id=%d', $EM_Booking->booking_id));
		if( !empty($main_booking_id) ){
			return new EM_Multiple_Booking($main_booking_id);
		}
		return false;
	}
    
    public static function booking_admin(){
		emp_locate_template('multiple-bookings/admin.php',true);
	}
}
EM_Multiple_Bookings::init();