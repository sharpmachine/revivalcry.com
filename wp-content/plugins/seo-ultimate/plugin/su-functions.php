<?php
/**
 * Non-class functions.
 */

/********** INDEPENDENTLY-OPERABLE FUNCTIONS **********/

/**
 * Returns the plugin's User-Agent value.
 * Can be used as a WordPress filter.
 * 
 * @since 0.1
 * @uses SU_USER_AGENT
 * 
 * @return string The user agent.
 */
function su_get_user_agent() {
	return SU_USER_AGENT;
}

/**
 * Records an event in the debug log file.
 * Usage: su_debug_log(__FILE__, __CLASS__, __FUNCTION__, __LINE__, "Message");
 * 
 * @since 0.1
 * @uses SU_VERSION
 * 
 * @param string $file The value of __FILE__
 * @param string $class The value of __CLASS__
 * @param string $function The value of __FUNCTION__
 * @param string $line The value of __LINE__
 * @param string $message The message to log.
 */
function su_debug_log($file, $class, $function, $line, $message) {
	global $seo_ultimate;
	if (isset($seo_ultimate->modules['settings']) && $seo_ultimate->modules['settings']->get_setting('debug_mode') === true) {
	
		$date = date("Y-m-d H:i:s");
		$version = SU_VERSION;
		$message = str_replace("\r\n", "\n", $message);
		$message = str_replace("\n", "\r\n", $message);
		
		$log = "Date: $date\r\nVersion: $version\r\nFile: $file\r\nClass: $class\r\nFunction: $function\r\nLine: $line\r\nMessage: $message\r\n\r\n";
		$logfile = trailingslashit(dirname(__FILE__))."seo-ultimate.log";
		
		@error_log($log, 3, $logfile);
	}
}

/**
 * Joins strings into a natural-language list.
 * Can be internationalized with gettext or the su_lang_implode filter.
 * 
 * @since 1.1
 * 
 * @param array $items The strings (or objects with $var child strings) to join.
 * @param string|false $var The name of the items' object variables whose values should be imploded into a list.
	If false, the items themselves will be used.
 * @param bool $ucwords Whether or not to capitalize the first letter of every word in the list.
 * @return string|array The items in a natural-language list.
 */
function su_lang_implode($items, $var=false, $ucwords=false) {
	
	if (is_array($items) ) {
		
		if (strlen($var)) {
			$_items = array();
			foreach ($items as $item) $_items[] = $item->$var;
			$items = $_items;
		}
		
		if ($ucwords) $items = array_map('ucwords', $items);
		
		switch (count($items)) {
			case 0: $list = ''; break;
			case 1: $list = $items[0]; break;
			case 2: $list = sprintf(__('%s and %s', 'seo-ultimate'), $items[0], $items[1]); break;
			default:
				$last = array_pop($items);
				$list = implode(__(', ', 'seo-ultimate'), $items);
				$list = sprintf(__('%s, and %s', 'seo-ultimate'), $list, $last);
				break;
		}
		
		return apply_filters('su_lang_implode', $list, $items);
	}

	return $items;
}

/**
 * Escapes an attribute value and removes unwanted characters.
 * 
 * @since 0.8
 * 
 * @param string $str The attribute value.
 * @return string The filtered attribute value.
 */
function su_esc_attr($str) {
	if (!is_string($str)) return $str;
	$str = str_replace(array("\t", "\r\n", "\n"), ' ', $str);
	$str = esc_attr($str);
	return $str;
}

/**
 * Escapes HTML.
 * 
 * @since 2.1
 */
function su_esc_html($str) {
	return esc_html($str);
}

/**
 * Escapes HTML. Double-encodes existing entities (ideal for editable HTML).
 * 
 * @since 1.5
 * 
 * @param string $str The string that potentially contains HTML.
 * @return string The filtered string.
 */
function su_esc_editable_html($str) {
	return _wp_specialchars($str, ENT_QUOTES, false, true);
}

// Add a parent shortcut link for admin toolbar
function seo_ultimate_admin_bar_menu( $meta = true ) {
	global $wp_admin_bar, $seo_ultimate;
		if ( !is_user_logged_in() ) { return; }
		if ( !is_super_admin() || !is_admin_bar_showing() ) { return; }
		if (isset($seo_ultimate->modules['settings']) && $seo_ultimate->modules['settings']->get_setting('seo_toolbar_menu') === false) { return; }

		// Add the parent link for admin toolbar
		$args = array(
			'id' => 'seo-ultimate',
			'title' => 'SEO', 
			'href' => self_admin_url( 'admin.php?page=seo' ), 
			'meta' => array(
				'class' => 'seo-ultimate', 
				'title' => 'SEO'
				)
		);
		$wp_admin_bar->add_node($args);
	
		// Add the child link for admin toolbar
		$args = array(
			'id' => 'su-moduels',
			'title' => 'Modules', 
			'href' => self_admin_url( 'admin.php?page=seo' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-moduels', 
				'title' => 'Modules'
				)
		);
		$wp_admin_bar->add_node($args);	

		$args = array(
			'id' => 'su-fofs',
			'title' => '404 Monitor', 
			'href' => self_admin_url( 'admin.php?page=su-fofs' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-fofs', 
				'title' => '404 Monitor'
				)
		);
		$wp_admin_bar->add_node($args);

		$args = array(
			'id' => 'su-user-code',
			'title' => 'Code Inserter', 
			'href' => self_admin_url( 'admin.php?page=su-user-code' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-user-code', 
				'title' => 'Code Inserter'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-autolinks',
			'title' => 'Deeplink Juggernaut', 
			'href' => self_admin_url( 'admin.php?page=su-autolinks' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-autolinks', 
				'title' => 'Deeplink Juggernaut'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-files',
			'title' => 'File Editor', 
			'href' => self_admin_url( 'admin.php?page=su-files' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-files', 
				'title' => 'File Editor'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-internal-link-aliases',
			'title' => 'Link Mask Generator', 
			'href' => self_admin_url( 'admin.php?page=su-internal-link-aliases' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-internal-link-aliases', 
				'title' => 'Link Mask Generator'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-meta-descriptions',
			'title' => 'Meta Description', 
			'href' => self_admin_url( 'admin.php?page=su-meta-descriptions' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-meta-descriptions', 
				'title' => 'Meta Description'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-meta-keywords',
			'title' => 'Meta Keywords', 
			'href' => self_admin_url( 'admin.php?page=su-meta-keywords' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-meta-keywords', 
				'title' => 'Meta Keywords'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-meta-robots',
			'title' => 'Meta Robot Tags', 
			'href' => self_admin_url( 'admin.php?page=su-meta-robots' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-meta-robots', 
				'title' => 'Meta Robot Tags'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-opengraph',
			'title' => 'Open Graph', 
			'href' => self_admin_url( 'admin.php?page=su-opengraph' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-opengraph', 
				'title' => 'Open Graph'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-wp-settings',
			'title' => 'Settings Monitor', 
			'href' => self_admin_url( 'admin.php?page=su-wp-settings' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-wp-settings', 
				'title' => 'Settings Monitor'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-titles',
			'title' => 'Title Tag Rewriter', 
			'href' => self_admin_url( 'admin.php?page=su-titles' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-titles', 
				'title' => 'Title Tag Rewriter'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-sds-blog',
			'title' => 'Whitepapers', 
			'href' => self_admin_url( 'admin.php?page=su-sds-blog' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-sds-blog', 
				'title' => 'Whitepapers'
				)
		);
		$wp_admin_bar->add_node($args);
		
		$args = array(
			'id' => 'su-misc',
			'title' => 'Miscellaneous', 
			'href' => self_admin_url( 'admin.php?page=su-misc' ),
			'parent' => 'seo-ultimate', 
			'meta' => array(
				'class' => 'su-misc', 
				'title' => 'Miscellaneous'
				)
		);
		$wp_admin_bar->add_node($args);
		
}
add_action('admin_bar_menu', 'seo_ultimate_admin_bar_menu', 95);
?>