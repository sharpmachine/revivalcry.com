<?php
/*
Plugin Name: Popular Posts
Plugin URI: http://plugins.jrseoservices.com/popular-posts-plugin
Description: Allows you to show your most popular posts as a widget on your blog.
Version: 1.0.3
Author: JR SEO
Author URI: http://www.jrseoservices.com
*/

/*  Copyright 2011 JR SEO - support@jrseoservices.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
add_action('admin_menu', 'popular_posts_add_pages');
register_activation_hook(__FILE__,'PopularPosts_install');

// action function for above hook
function popular_posts_add_pages() {
    add_options_page('Popular Posts', 'Popular Posts', 'administrator', 'popular_posts', 'popular_posts_options_page');
}

$PopularPosts_db_version = "1.0.0";

function PopularPosts_install () {
   global $wpdb;
   global $PopularPosts_db_version;

   $table_name = $wpdb->prefix . "PopularPostsdata";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(30) NOT NULL,
	  hits mediumint(55) NOT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
 
      add_option("PopularPosts_db_version", $PopularPosts_db_version);
	  }
}


// popular_posts_options_page() displays the page content for the Test Options submenu
function popular_posts_options_page() {

    // variables for the field and option names 
    $opt_name_5 = 'mt_PopularPosts_plugin_support';
	$opt_name_6 = 'mt_PopularPosts_title';
	$opt_name_7 = 'mt_PopularPosts_number';
    $hidden_field_name = 'mt_PopularPosts_submit_hidden';
    $data_field_name_5 = 'mt_PopularPosts_plugin_support';
	$data_field_name_6 = 'mt_PopularPosts_title';
	$data_field_name_7 = 'mt_PopularPosts_number';

    // Read in existing option value from database
    $opt_val_5 = get_option($opt_name_5);
	$opt_val_6 = get_option($opt_name_6);
	$opt_val_7 = get_option($opt_name_7);

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val_5 = $_POST[$data_field_name_5];
		$opt_val_6 = $_POST[$data_field_name_6];
		$opt_val_7 = $_POST[$data_field_name_7];

        // Save the posted value in the database
        update_option( $opt_name_5, $opt_val_5 );
		update_option( $opt_name_6, $opt_val_6 );
		update_option( $opt_name_7, $opt_val_7 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Popular Posts Plugin Options', 'mt_trans_domain' ) . "</h2>";

    // options form
    
    $change3 = get_option("mt_PopularPosts_plugin_support");


if ($change3=="Yes" || $change3=="") {
$change3="checked";
$change31="";
} else {
$change3="";
$change31="checked";
}

    ?>
	<?php

?>	
<form name="form3" method="post" action="">
<h3>Most Popular Posts</h3>

<?php
   global $wpdb;
   $table_name = $wpdb->prefix . "PopularPostsdata";
   $num=get_option("mt_PopularPosts_number");
   
   if ($num=="") {
   $num=5;
   }
   
$rows = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY hits DESC LIMIT " . $num);
echo "<ul>";
foreach ($rows as $row) {
$title=get_the_title($row->id);
$permalink=get_permalink($row->id);
$hits=$row->hits;

echo "<li><a href='".$permalink."'>".$title."</a> (".$hits." visits)</li>";
}
echo "</ul>";

?>

<h3>Settings</h3>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Widget Title:", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_6; ?>" value="<?php echo $opt_val_6; ?>">
</p>

<p><?php _e("Number of Popular Posts to show:", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_7; ?>" value="<?php echo $opt_val_7; ?>">
</p>

<p><?php _e("Show Plugin Support?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="Yes" <?php echo $change3; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="No" <?php echo $change31; ?>>No
</p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p><hr />

</form>
</div>
<?php
}

function init_PopularPosts_widget() {
register_sidebar_widget('Popular Posts', 'show_PopularPostss');
}

function PopularPosts_set_cookie() {
global $single, $feed;

if ($single && !$feed) {
global $wpdb;
global $post;

$table_name = $wpdb->prefix . "PopularPostsdata";
$counter=0;
$thePostID = $post->ID;
$rows = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE id=".$thePostID);
foreach ($rows as $rows) {
$counter ++;
}

if ($counter==0) {
$query = $wpdb->query("INSERT INTO " . $table_name . " VALUES (".$thePostID.", 1)");
} else {
$query = $wpdb->query("UPDATE " . $table_name . " SET hits=hits+1 WHERE id=".$thePostID);
}

}
}

function show_PopularPostss($args) {
extract($args);

$supportplugin = get_option("mt_PopularPosts_plugin_support"); 
$num=get_option("mt_PopularPosts_number");
$title=get_option("mt_PopularPosts_title");

if ($title=="") {
$title="Popular Posts";
}

if ($num=="") {
$num=5;
}

global $wpdb;

echo $before_widget.$before_title.stripslashes($title).$after_title;
$table_name = $wpdb->prefix . "PopularPostsdata";
echo "<ul>";
$rows = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY hits DESC LIMIT " . $num);
$mylatestnum=0;
foreach ($rows as $row) {
$title=get_the_title($row->id);
$permalink=get_permalink($row->id);
$hits=$row->hits;
$mylatestnum ++;

echo "<li>".$mylatestnum.". <a href='".$permalink."' rel='nofollow'>".$title."</a></li>";
}
echo "</ul>";

if ($supportplugin=="Yes" || $supportplugin=="") {
if (get_option("popularposts_wp_saved")=="") {
$echome="<p style='font-size:x-small'>Popular Posts Plugin made by <a href='http://www.frostwire-p2p-download.com'>FrostWire</a>.</p>";
update_option("popularposts_wp_saved", $echome);
echo $echome;
} else {
$echome=get_option("popularposts_wp_saved");
echo $echome;
}
}

echo $after_widget;
}

add_action("plugins_loaded", "init_PopularPosts_widget");
add_action("get_header", "PopularPosts_set_cookie");

?>
