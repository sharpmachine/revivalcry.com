<?php

if(!class_exists('EM_Gateway')) {

	class EM_Gateway {
		
		// Class Identification
		var $gateway = 'Not Set';
		var $title = 'Not Set';

		// Tables
		var $transactions_table;

		function EM_Gateway() {
			global $wpdb;
			$this->db =& $wpdb;
			$this->transactions_table = EM_TRANSACTIONS_TABLE;
			// Actions and Filters
			add_filter('EM_gateways_list', array(&$this, 'gateways_list'));
			add_filter('EM_active_gateways', array(&$this, 'active_gateways'));
			add_filter('em_booking_form_js', array(&$this,'booking_form_js'),1,2); //JS Replacement, so we can handle the ajax return differently
		}
		
		function init(){
			//WP_Query/Rewrite
			add_filter('rewrite_rules_array',array('EM_Gateway','rewrite_rules_array'));
			add_filter('query_vars',array('EM_Gateway','query_vars'));			
			//Menus
			add_action('em_create_events_submenu',array('EM_Gateway', 'admin_menu'),1,1);
			add_action('admin_init', array('EM_Gateway', 'handle_payment_gateways'),1,1);
			add_action('admin_init', array('EM_Gateway', 'handle_gateways_panel_updates'),1,1);
			//Booking interception
			add_filter('em_booking_add', array('EM_Gateway', 'em_booking_add'), 1, 2);
			// Payment return
			add_action('parse_query', array('EM_Gateway', 'handle_payment_gateways'), 1 ); //just in case
			add_action('wp_ajax_em_payment', array('EM_Gateway', 'handle_payment_gateways'), 1 );
			add_filter('em_booking_form_buttons', array('EM_Gateway','booking_form_buttons'),1,2); //Replace button with PP image
		}	
		
		/**
		 * Adding a new rule, shouldn't be necessary anymore, but for backwards compatability
		 * @param array $rules
		 * @return array
		 */
		function rewrite_rules_array($rules){
			//get the slug of the event page
			$events_page_id = get_option ( 'dbem_events_page' );
			$events_page = get_post($events_page_id);
			$em_rules = array();
			if( is_object($events_page) ){
				$events_slug = preg_replace('/\/$/', '', str_replace( trailingslashit(home_url()), '', get_permalink($events_page_id)) );
				$events_slug = ( !empty($events_slug) ) ? trailingslashit($events_slug) : $events_slug;		
				$em_rules[$events_slug.'payments/(.+)$'] = 'index.php?pagename='.$events_slug.'&em_payment_gateway=$matches[1]'; //single event booking form with slug
			}else{
				$events_slug = EM_POST_TYPE_EVENT_SLUG;
				$em_rules[$events_slug.'/payments/(.+)$'] = 'index.php?post_type='.EM_POST_TYPE_EVENT.'&em_payment_gateway=$matches[1]'; //single event booking form with slug
			}
			return $em_rules + $rules;
		}
		
		/**
		 * Add the queryvars to WP_Query
		 * @param array $vars
		 * @return array
		 */
		function query_vars($vars){
			array_push($vars, 'em_payment_gateway');
		    return $vars;
		}
		
		function em_booking_add($EM_Event,$EM_Booking){
			global $EM_Gateways;
			if( array_key_exists( $_REQUEST['gateway'], get_option('em_payment_gateways',array()))){
				do_action('em_booking_add_'.$_REQUEST['gateway'], $EM_Event, $EM_Booking);
			}
		}
	
		/**
		 * This gets called when a booking form is added. 
		 * @param unknown_type $button
		 * @param EM_Event $EM_Event
		 * @return string
		 */
		function booking_form_buttons($button, $EM_Event){
			global $EM_Gateways;
			$gateway_buttons = array();
			if(!$EM_Event->is_free()){
				$active_gateways = get_option('em_payment_gateways');
				if( is_array($active_gateways) ){
					foreach($active_gateways as $gateway => $active_val){
						if(array_key_exists($gateway, $EM_Gateways)) {
							$gateway_button = $EM_Gateways[$gateway]->booking_form_button();
							if(!empty($gateway_button)){
								$gateway_buttons[$gateway] = $gateway_button;
							}
						}
					}
					if( count($gateway_buttons) > 0 ){
						$button = '<div class="em-gateway-buttons"><div class="em-gateway-button first">'. implode('</div><div class="em-gateway-button">', $gateway_buttons).'</div></div>';			
					}
				}
			}
			return apply_filters('em_gateway_booking_form_buttons', $button, $gateway_buttons);
		}
		
		/**
		 * Override this
		 * @param string $button
		 * @return string
		 */
		function booking_form_button($button){
			return $button;
		}

		function handle_payment_gateways($wp_query) {
			if( !empty($_REQUEST['em_payment_gateway']) || get_query_var('em_payment_gateway') != '' ) {
				$action = !empty($_REQUEST['em_payment_gateway']) ? $_REQUEST['em_payment_gateway'] : get_query_var('em_payment_gateway');
				do_action( 'em_handle_payment_return_' . $action);
				exit();
			}
		}
		
		function admin_menu($plugin_pages){
			$plugin_pages[] = add_submenu_page('edit.php?post_type='.EM_POST_TYPE_EVENT, __('Payment Gateways'),__('Payment Gateways'),'activate_plugins','events-manager-gateways',array('EM_Gateway','handle_gateways_panel'));
			return $plugin_pages;
		}

		function gateways_list($gateways) {
			$gateways[$this->gateway] = $this->title;
			return $gateways;
		}
		
		function active_gateways($gateways) {
			if($this->is_active()){
				$gateways[$this->gateway] = $this->title;
			}
			return $gateways;
		}

		function toggleactivation() {
			global $EM_Pro;
			$active = get_option('em_payment_gateways');

			if(array_key_exists($this->gateway, $active)) {
				unset($active[$this->gateway]);
				update_option('em_payment_gateways',$active);
				return true;
			} else {
				$active[$this->gateway] = true;
				update_option('em_payment_gateways',$active);
				return true;
			}
		}

		function activate() {
			global $EM_Pro;
			$active = get_option('em_payment_gateways', array());
			if(array_key_exists($this->gateway, $active)) {
				return true;
			} else {
				$active[$this->gateway] = true;
				update_option('em_payment_gateways', $active);
				return true;
			}
		}

		function deactivate() {
			global $EM_Pro;
			$active = get_option('em_payment_gateways');
			if(array_key_exists($this->gateway, $active)) {
				unset($active[$this->gateway]);
				update_option('em_payment_gateways', $active);
				return true;
			} else {
				return true;
			}
		}

		function is_active() {
			global $EM_Pro;
			$active = get_option('em_payment_gateways', array());
			if( array_key_exists($this->gateway, $active)) {
				return true;
			} else {
				return false;
			}
		}

		function settings() {
			global $page, $action;
			?>
			<div class='wrap nosubsub'>
				<div class="icon32" id="icon-plugins"><br></div>
				<h2><?php echo sprintf(__('Edit &quot;%s&quot; settings','em-pro'), esc_html($this->title) ); ?></h2>
				<form action='' method='post' name='gatewaysettingsform'>
					<input type='hidden' name='action' id='action' value='updated' />
					<input type='hidden' name='gateway' id='gateway' value='<?php echo $this->gateway; ?>' />
					<?php
					wp_nonce_field('updated-' . $this->gateway);
					do_action('EM_gateways_settings_' . $this->gateway);
					?>
					<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
					</p>
				</form>
			</div> <!-- wrap -->
			<?php
		}

		function update() {
			// default action is to return true
			return true;
		}

		function record_transaction($EM_Booking, $amount, $currency, $timestamp, $paypal_id, $status, $note) {
			global $wpdb;
			$data = array();
			$data['booking_id'] = $EM_Booking->booking_id;
			$data['transaction_gateway_id'] = $paypal_id;
			$data['transaction_timestamp'] = $timestamp;
			$data['transaction_currency'] = $currency;
			$data['transaction_status'] = $status;
			$data['transaction_total_amount'] = $amount;
			$data['transaction_note'] = $note;
			$data['transaction_gateway'] = $this->gateway;

			if( !empty($paypal_id) ){
				$existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT transaction_id FROM {$this->transactions_table} WHERE transaction_gateway_id = %s", $paypal_id ) );
			}

			if( !empty($existing_id) ) {
				// Update
				$wpdb->update( $this->transactions_table, $data, array('transaction_id' => $existing_id) );
			} else {
				// Insert
				$wpdb->insert( $this->transactions_table, $data );
			}
		}

		function get_total() {
			global $wpdb;
			return $wpdb->get_var( "SELECT FOUND_ROWS();" );
		}

		function handle_gateways_panel() {
			global $action, $page, $EM_Gateways, $EM_Pro;
			wp_reset_vars( array('action', 'page') );
			switch(addslashes($action)) {
				case 'edit':	
					if(isset($EM_Gateways[addslashes($_GET['gateway'])])) {
						$EM_Gateways[addslashes($_GET['gateway'])]->settings();
					}
					return; // so we don't show the list below
					break;
				case 'transactions':
					if(isset($EM_Gateways[addslashes($_GET['gateway'])])) {
						global $EM_Gateways_Table;
						$EM_Gateways_Table->output();
					}
					return; // so we don't show the list below
					break;
			}
			$messages = array();
			$messages[1] = __('Gateway updated.');
			$messages[2] = __('Gateway not updated.');
			$messages[3] = __('Gateway activated.');
			$messages[4] = __('Gateway not activated.');
			$messages[5] = __('Gateway deactivated.');
			$messages[6] = __('Gateway not deactivated.');
			$messages[7] = __('Gateway activation toggled.');
			?>
			<div class='wrap'>
				<div class="icon32" id="icon-plugins"><br></div>
				<h2><?php _e('Edit Gateways','em-pro'); ?></h2>
				<?php
				if ( isset($_GET['msg']) ) {
					echo '<div id="message" class="updated fade"><p>' . $messages[(int) $_GET['msg']] . '</p></div>';
					$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
				}
				?>
				<form method="get" action="" id="posts-filter">
					<input type='hidden' name='page' value='<?php echo esc_attr($page); ?>' />
					<div class="tablenav">
						<div class="alignleft actions">
							<select name="action">
								<option selected="selected" value=""><?php _e('Bulk Actions'); ?></option>
								<option value="toggle"><?php _e('Toggle activation'); ?></option>
							</select>
							<input type="submit" class="button-secondary action" id="doaction" name="doaction" value="<?php _e('Apply'); ?>">		
						</div>		
						<div class="alignright actions"></div>		
						<br class="clear">
					</div>	
					<div class="clear"></div>	
					<?php
						wp_original_referer_field(true, 'previous'); wp_nonce_field('bulk-gateways');	
						$columns = array(	
							"name" => __('Gateway Name','em-pro'),
							"active" =>	__('Active','em-pro'),
							"transactions" => __('Transactions','em-pro')
						);
						$columns = apply_filters('EM_gateways_columns', $columns);	
						$gateways = apply_filters('EM_gateways_list', array());	
						$active = get_option('em_payment_gateways', array());
					?>	
					<table cellspacing="0" class="widefat fixed">
						<thead>
						<tr>
						<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
							<?php
							foreach($columns as $key => $col) {
								?>
								<th style="" class="manage-column column-<?php echo $key; ?>" id="<?php echo $key; ?>" scope="col"><?php echo $col; ?></th>
								<?php
							}
							?>
						</tr>
						</thead>	
						<tfoot>
						<tr>
						<th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
							<?php
							reset($columns);
							foreach($columns as $key => $col) {
								?>
								<th style="" class="manage-column column-<?php echo $key; ?>" id="<?php echo $key; ?>" scope="col"><?php echo $col; ?></th>
								<?php
							}
							?>
						</tr>
						</tfoot>
						<tbody>
							<?php
							if($gateways) {
								foreach($gateways as $key => $gateway) {
									if(!isset($EM_Gateways[$key])) {
										continue;
									}
									?>
									<tr valign="middle" class="alternate">
										<th class="check-column" scope="row"><input type="checkbox" value="<?php echo esc_attr($key); ?>" name="gatewaycheck[]"></th>
										<td class="column-name">
											<strong><a title="Edit <?php echo esc_attr($gateway); ?>" href="<?php echo EM_ADMIN_URL; ?>&amp;page=<?php echo $page; ?>&amp;action=edit&amp;gateway=<?php echo $key; ?>" class="row-title"><?php echo esc_html($gateway); ?></a></strong>
											<?php
												$actions = array();
												$actions['edit'] = "<span class='edit'><a href='".EM_ADMIN_URL."&amp;page=" . $page . "&amp;action=edit&amp;gateway=" . $key . "'>" . __('Settings') . "</a></span>";
	
												if(array_key_exists($key, $active)) {
													$actions['toggle'] = "<span class='edit activate'><a href='" . wp_nonce_url(EM_ADMIN_URL."&amp;page=" . $page. "&amp;action=deactivate&amp;gateway=" . $key . "", 'toggle-gateway_' . $key) . "'>" . __('Deactivate') . "</a></span>";
												} else {
													$actions['toggle'] = "<span class='edit deactivate'><a href='" . wp_nonce_url(EM_ADMIN_URL."&amp;page=" . $page. "&amp;action=activate&amp;gateway=" . $key . "", 'toggle-gateway_' . $key) . "'>" . __('Activate') . "</a></span>";
												}
											?>
											<br><div class="row-actions"><?php echo implode(" | ", $actions); ?></div>
											</td>
										<td class="column-active">
											<?php
												if(array_key_exists($key, $active)) {
													echo "<strong>" . __('Active', 'em-pro') . "</strong>";
												} else {
													echo __('Inactive', 'em-pro');
												}
											?>
										</td>
										<td class="column-transactions">
											<a href='<?php echo EM_ADMIN_URL; ?>&amp;page=<?php echo $page; ?>&amp;action=transactions&amp;gateway=<?php echo $key; ?>'><?php _e('View transactions','em-pro'); ?></a>
										</td>
								    </tr>
									<?php
								}
							} else {
								$columncount = count($columns) + 1;
								?>
								<tr valign="middle" class="alternate" >
									<td colspan="<?php echo $columncount; ?>" scope="row"><?php _e('No Payment gateways where found for this install.','em-pro'); ?></td>
							    </tr>
								<?php
							}
							?>
						</tbody>
					</table>	
					<div class="tablenav">	
						<div class="alignleft actions">
							<select name="action2">
								<option selected="selected" value=""><?php _e('Bulk Actions'); ?></option>
								<option value="toggle"><?php _e('Toggle activation'); ?></option>
							</select>
							<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Apply">
						</div>
						<div class="alignright actions"></div>
						<br class="clear">
					</div>
				</form>
	
			</div> <!-- wrap -->
			<?php
		}
				
		function handle_gateways_panel_updates() {	
			global $action, $page, $EM_Gateways;	
			wp_reset_vars ( array ('action', 'page' ) );
			$request = $_REQUEST;
			if (isset ( $_REQUEST ['doaction'] ) || isset ( $_REQUEST ['doaction2'] )) {
				if ( (!empty($_GET ['action']) && addslashes ( $_GET ['action'] ) == 'toggle') || (!empty( $_GET ['action2']) && addslashes ( $_GET ['action2'] ) == 'toggle') ) {
					$action = 'bulk-toggle';
				}
			}	
			if( !empty($_REQUEST['gateway']) || !empty($_REQUEST['bulk-gateways']) ){
				switch (addslashes ( $action )) {		
					case 'deactivate' :
						$key = addslashes ( $_REQUEST ['gateway'] );
						if (isset ( $EM_Gateways [$key] )) {
							if ($EM_Gateways [$key]->deactivate ()) {
								wp_safe_redirect ( add_query_arg ( 'msg', 5, wp_get_referer () ) );
							} else {
								wp_safe_redirect ( add_query_arg ( 'msg', 6, wp_get_referer () ) );
							}
						}
						break;		
					case 'activate' :
						$key = addslashes ( $_REQUEST ['gateway'] );
						if (isset ( $EM_Gateways[$key] )) {
							if ($EM_Gateways[$key]->activate ()) {
								wp_safe_redirect ( add_query_arg ( 'msg', 3, wp_get_referer () ) );
							} else {
								wp_safe_redirect ( add_query_arg ( 'msg', 4, wp_get_referer () ) );
							}
						}
						break;		
					case 'bulk-toggle' :
						check_admin_referer ( 'bulk-gateways' );
						foreach ( $_REQUEST ['gatewaycheck'] as $key ) {
							if (isset ( $EM_Gateways [$key] )) {					
								$EM_Gateways [$key]->toggleactivation ();				
							}
						}
						wp_safe_redirect ( add_query_arg ( 'msg', 7, wp_get_referer () ) );
						break;		
					case 'updated' :
						$gateway = addslashes ( $_REQUEST ['gateway'] );		
						check_admin_referer ( 'updated-'.$EM_Gateways[$gateway]->gateway );
						if ($EM_Gateways[$gateway]->update ()) {
							wp_safe_redirect ( add_query_arg ( 'msg', 1, EM_ADMIN_URL.'&page=' . $page ) );
						} else {
							wp_safe_redirect ( add_query_arg ( 'msg', 2, EM_ADMIN_URL.'&page=' . $page ) );
						}			
						break;
				}
			}
		}
	}
}
EM_Gateway::init();


class EM_Gateways_Table{
	var $limit = 20;
	var $total_transactions = 0;
	
	function __construct(){
		$this->order = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'ASC';
		$this->orderby = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'booking_name';
		$this->limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
		$this->page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
		$this->gateway = !empty($_REQUEST['gateway']) ? $_REQUEST['gateway']:false;
		//Add options and tables to EM admin pages
		if( current_user_can('manage_others_bookings') ){
			add_action('em_bookings_dashboard', array(&$this, 'output'),10,1);
			add_action('em_bookings_ticket_footer', array(&$this, 'output'),10,1);
			add_action('em_bookings_single_footer', array(&$this, 'output'),10,1);
			add_action('em_bookings_person_footer', array(&$this, 'output'),10,1);
			add_action('em_bookings_event_footer', array(&$this, 'output'),10,1);
		}
		add_action('wp_ajax_em_transactions_table', array(&$this, 'ajax'),10,1);
	}
	
	function ajax(){
		if( wp_verify_nonce($_REQUEST['_wpnonce'],'em_transactions_table') ){
			//Get the context
			global $EM_Event, $EM_Booking, $EM_Ticket, $EM_Person;
			em_load_event();
			$context = false;
			if( !empty($_REQUEST['booking_id']) && is_object($EM_Booking) && $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ){
				$context = $EM_Booking;
			}elseif( !empty($_REQUEST['event_id']) && is_object($EM_Event) && $EM_Event->can_manage('manage_bookings','manage_others_bookings') ){
				$context = $EM_Event;
			}elseif( !empty($_REQUEST['person_id']) && is_object($EM_Person) && current_user_can('manage_bookings') ){
				$context = $EM_Person;
			}elseif( !empty($_REQUEST['ticket_id']) && is_object($EM_Ticket) && $EM_Ticket->can_manage('manage_bookings','manage_others_bookings') ){
				$context = $EM_Ticket;
			}			
			echo $this->mytransactions($context);
			exit;
		}
	}
	
	function output( $context = false ) {
		global $page, $action, $wp_query;
		?>
		<div class="wrap">
		<div class="icon32" id="icon-bookings"><br></div>
		<h2><?php echo __('Transactions','dbem'); ?></h2>
		<?php $this->mytransactions($context); ?>
		<script type="text/javascript">
			jQuery(document).ready( function($){
				//Pagination link clicks
				$('#em-transactions-table .tablenav-pages a').live('click', function(){
					var el = $(this);
					var form = el.parents('#em-transactions-table form.transactions-filter');
					//get page no from url, change page, submit form
					var match = el.attr('href').match(/#[0-9]+/);
					if( match != null && match.length > 0){
						var pno = match[0].replace('#','');
						form.find('input[name=pno]').val(pno);
					}else{
						form.find('input[name=pno]').val(1);
					}
					form.trigger('submit');
					return false;
				});
				//Widgets and filter submissions
				$('#em-transactions-table form.transactions-filter').live('submit', function(e){
					var el = $(this);			
					el.parents('#em-transactions-table').find('.table-wrap').first().append('<div id="em-loading" />');
					$.get( EM.ajaxurl, el.serializeArray(), function(data){
						el.parents('#em-transactions-table').first().replaceWith(data);
					});
					return false;
				});
			});
		</script>
		</div>
		<?php
	}

	function mytransactions($context=false) {
		global $EM_Person;
		$transactions = $this->get_transactions($context);
		$total = $this->total_transactions;

		$columns = array();

		$columns['event'] = __('Event','em-pro');
		$columns['user'] = __('User','em-pro');
		$columns['date'] = __('Date','em-pro');
		$columns['amount'] = __('Amount','em-pro');
		$columns['transid'] = __('Transaction id','em-pro');
		$columns['gateway'] = __('Gateway','em-pro');
		$columns['status'] = __('Status','em-pro');
		$columns['note'] = __('Notes','em-pro');

		$trans_navigation = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'total' => ceil($total / 20),
			'current' => $this->page
		));
		?>
		<div id="em-transactions-table" class="em_obj">
		<form id="em-transactions-table-form" class="transactions-filter" action="" method="post">
			<?php if( is_object($context) && get_class($context)=="EM_Event" ): ?>
			<input type="hidden" name="event_id" value='<?php echo $context->event_id ?>' />
			<?php elseif( is_object($context) && get_class($context)=="EM_Person" ): ?>
			<input type="hidden" name="person_id" value='<?php echo $context->person_id ?>' />
			<?php endif; ?>
			<input type="hidden" name="pno" value='<?php echo $this->page ?>' />
			<input type="hidden" name="order" value='<?php echo $this->order ?>' />
			<input type="hidden" name="orderby" value='<?php echo $this->orderby ?>' />
			<input type="hidden" name="_wpnonce" value="<?php echo ( !empty($_REQUEST['_wpnonce']) ) ? $_REQUEST['_wpnonce']:wp_create_nonce('em_transactions_table'); ?>" />
			<input type="hidden" name="action" value="em_transactions_table" />
			
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="limit">
						<option value="<?php echo $this->limit ?>"><?php echo sprintf(__('%s Rows','dbem'),$this->limit); ?></option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
					<select name="gateway">
						<option value="">All</option>
						<?php
						global $EM_Gateways;
						foreach ( $EM_Gateways as $EM_Gateway ) {
							?><option value='<?php echo $EM_Gateway->gateway ?>' <?php if($EM_Gateway->gateway == $this->gateway) echo "selected='selected'"; ?>><?php echo $EM_Gateway->title ?></option><?php
						}
						?>
					</select>
					<input id="post-query-submit" class="button-secondary" type="submit" value="<?php _e ( 'Filter' )?>" />
					<?php if( is_object($context) && get_class($context)=="EM_Event" ): ?>
					<?php _e('Displaying Event','dbem'); ?> : <?php echo $context->event_name; ?>
					<?php elseif( is_object($context) && get_class($context)=="EM_Person" ): ?>
					<?php _e('Displaying User','dbem'); echo ' : '.$context->get_name(); ?>
					<?php endif; ?>
				</div>
				<?php 
				if ( $this->total_transactions >= $this->limit ) {
					echo em_admin_paginate( $this->total_transactions, $this->limit, $this->page, array(),'#%#%','#');
				}
				?>
			</div>

			<div class="table-wrap">
			<table cellspacing="0" class="widefat">
				<thead>
				<tr>
				<?php
					foreach($columns as $key => $col) {
						?>
						<th style="" class="manage-column column-<?php echo $key; ?>" id="<?php echo $key; ?>" scope="col"><?php echo $col; ?></th>
						<?php
					}
				?>
				</tr>
				</thead>

				<tfoot>
				<tr>
					<?php
						reset($columns);
						foreach($columns as $key => $col) {
							?>
							<th style="" class="manage-column column-<?php echo $key; ?>" id="<?php echo $key; ?>" scope="col"><?php echo $col; ?></th>
							<?php
						}
					?>
				</tr>
				</tfoot>

				<tbody>
					<?php
						echo $this->print_transactions($transactions);
					?>

				</tbody>
			</table>
			</div>
		</form>
		</div>
		<?php
	}
	
	function print_transactions($transactions, $columns=7){
		ob_start();
		if($transactions) {
			foreach($transactions as $key => $transaction) {
				?>
				<tr valign="middle" class="alternate">
					<td>
						<?php
							$EM_Booking = new EM_Booking($transaction->booking_id);
							echo '<a href="'.EM_ADMIN_URL.'&amp;page=events-manager-bookings&amp;event_id='.$EM_Booking->get_event()->event_id.'">'.$EM_Booking->get_event()->event_name.'</a>';
						?>
					</td>
					<td>
						<?php
							echo '<a href="'.EM_ADMIN_URL.'&amp;page=events-manager-bookings&amp;person_id='.$EM_Booking->get_person()->ID.'">'.$EM_Booking->get_person()->get_name().'</a>';
						?>
					</td>
					<td class="column-date">
						<?php
							echo mysql2date("d-m-Y", $transaction->transaction_timestamp);
						?>
					</td>
					<td class="column-amount">
						<?php
							$amount = $transaction->transaction_total_amount;
							echo $transaction->transaction_currency;
							echo "&nbsp;" . number_format($amount, 2, '.', ',');
						?>
					</td>
					<td class="column-transid">
						<?php
							if(!empty($transaction->transaction_gateway_id)) {
								echo $transaction->transaction_gateway_id;
							} else {
								echo __('None yet','em-pro');
							}
						?>
					</td>
					<td class="column-transid">
						<?php
							if(!empty($transaction->transaction_gateway)) {
								echo $transaction->transaction_gateway;
							} else {
								echo __('None yet','em-pro');
							}
						?>
					</td>
					<td class="column-transid">
						<?php
							if(!empty($transaction->transaction_status)) {
								echo $transaction->transaction_status;
							} else {
								echo __('None yet','em-pro');
							}
						?>
					</td>
					<td class="column-transid">
						<?php
							if(!empty($transaction->transaction_note)) {
								echo esc_html($transaction->transaction_note);
							} else {
								echo __('None','em-pro');
							}
						?>
					</td>
			    </tr>
				<?php
			}
		} else {
			$columncount = count($columns);
			?>
			<tr valign="middle" class="alternate" >
				<td colspan="<?php echo $columncount; ?>" scope="row"><?php _e('No Transactions','em-pro'); ?></td>
		    </tr>
			<?php
		}
		return ob_get_clean();
	}
	
	function get_transactions($context=false) {
		global $wpdb;
		$join = '';
		$conditions = array();
		$table = EM_BOOKINGS_TABLE;
		//we can determine what to search for, based on if certain variables are set.
		if( is_object($context) && get_class($context)=="EM_Booking" && $context->can_manage('manage_bookings','manage_others_bookings') ){
			$conditions[] = "booking_id = ".$context->booking_id;
		}elseif( is_object($context) && get_class($context)=="EM_Event" && $context->can_manage('manage_bookings','manage_others_bookings') ){
			$join = "tx JOIN $table ON $table.booking_id=tx.booking_id";	
			$conditions[] = "event_id = ".$context->event_id;		
		}elseif( is_object($context) && get_class($context)=="EM_Person" ){
			//FIXME peole could potentially view other's txns like this
			$join = "tx JOIN $table ON $table.booking_id=tx.booking_id";
			$conditions[] = "person_id = ".$context->ID;			
		}elseif( is_object($context) && get_class($context)=="EM_Ticket" && $context->can_manage('manage_bookings','manage_others_bookings') ){
			$booking_ids = array();
			foreach($context->get_bookings()->bookings as $EM_Booking){
				$booking_ids[] = $EM_Booking->booking_id;
			}
			if( count($booking_ids) > 0 ){
				$conditions[] = "booking_id IN (".implode(',', $booking_ids).")";
			}else{
				return new stdClass();
			}			
		}
		if( is_multisite() && !is_main_blog() ){ //if not main blog, we show only blog specific booking info
			global $blog_id;
			$join = "tx JOIN $table ON $table.booking_id=tx.booking_id";
			$conditions[] = "booking_id IN (SELECT booking_id FROM $table, ".EM_EVENTS_TABLE." e WHERE e.blog_id=".$blog_id.")";
		}
		//filter by gateway
		if( !empty($this->gateway) ){
			$conditions[] = $wpdb->prepare('transaction_gateway = %s',$this->gateway);
		}
		//build conditions string
		$condition = (!empty($conditions)) ? "WHERE ".implode(' AND ', $conditions):'';
		$offset = ( $this->page > 1 ) ? ($this->page-1)*$this->limit : 0;		
		$sql = $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS * FROM ".EM_TRANSACTIONS_TABLE." $join $condition ORDER BY transaction_id DESC  LIMIT %d, %d", $offset, $this->limit );
		$return = $wpdb->get_results( $sql );
		$this->total_transactions = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		return $return;
	}	
}
global $EM_Gateways_Table;
$EM_Gateways_Table = new EM_Gateways_Table();

function emp_register_gateway($gateway, $class) {
	global $EM_Gateways;
	if(!is_array($EM_Gateways)) {
		$EM_Gateways = array();
	}
	$EM_Gateways[$gateway] = new $class;
}

?>