<?php
class EM_Pro_Admin{
	static function em_options_page_panel_admin_tools(){
	    ?>
	    <h4 style="font-size:1.1em;"><?php _e ( 'Logging', 'dbem' ); ?></h4>
		<table class="form-table">
			<?php em_options_radio_binary ( __( 'Enable Logging?', 'em-pro' ), 'dbem_enable_logging', sprintf(__('If enabled, a folder called %s will be created. Please ensure that your wp-contents folder is writable by the server.'), '<code>'.WP_PLUGIN_DIR.'events-manager-logs'.'</code>')); ?>
		</table>
		<?php
	}
	
	static function updated_option_dbem_enable_logging($old_val, $new_val){
		global $EM_Notices;
		if( $new_val && $new_val != $old_val ){
			if( !EM_Pro::log('Logging Enabled','general', true) ){
				$EM_Notices->add_error(__('Could not create a log directory, please make sure your wp-content is writeable.'.'em-pro'));
			}
		}
	}
}
add_action('em_options_page_panel_admin_tools', 'EM_Pro_Admin::em_options_page_panel_admin_tools');
add_action('add_option_dbem_enable_logging', 'EM_Pro_Admin::updated_option_dbem_enable_logging', 10, 2);
add_action('update_option_dbem_enable_logging', 'EM_Pro_Admin::updated_option_dbem_enable_logging', 10, 2);