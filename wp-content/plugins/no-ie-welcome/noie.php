<?php
/*
Plugin Name: No IE Welcome
Plugin URI: http://wordpress.losmuchachos.at/no_ie_welcome
Description: React in different Ways, when Vistor uses Microsoft Internet Explorer or IE of a specific Version Number.
Version: 1.0.1
Author: gnarf
Author URI: http://worpress.losmuchachos.at

*/


// initialisiert die globalen variablen $noie (=settings aus admin menü) und $iever = IE Version	
function noie_init(){
	global $noie, $iever;
    $noie = get_option("noie");
    $noie['purl']=WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
    $noie['path']=WP_PLUGIN_DIR.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
    
    if( preg_match("/msie.{0,1}([0-9]{1,2}).([0-9]{1,3})/i",$_SERVER["HTTP_USER_AGENT"],$ver) )
	    $iever = $ver[1];  
	    else $iever=0;
}


// entscheidet welches Theme angewendet wird 
function noie_decide_theme() { 
	global $iever, $noie;
	
	
	$th=get_theme(get_current_theme());
	$dir=stripslashes(str_replace($th['Theme Root'],"",$th['Template Dir']));
	if(!$noie["theme"]) return $dir; // no special theme chosen
    if($iever>0 and $iever <=$noie['version']) {
		$th=get_theme($noie['theme']);
		$dir=stripslashes(str_replace($th['Theme Root'],"",$th['Template Dir']));
    	return $dir;
    	} 
	return $dir;
}


// Fügt das Stylesheet für den Infoscreen hinzu.
function add_noie_stylesheet(){ 
	global $iever, $noie;
	$noieStyleFile = $noie['path']."noie.css";
    $noieStyleUrl = $noie['purl'].'noie.css';
       
        if ( file_exists($noieStyleFile) ) {
            wp_register_style('noie', $noieStyleUrl);
            wp_enqueue_style( 'noie');
		} 
		wp_enqueue_script("jquery");

} // end function add noie stylesheet



function add_noie_js(){
	global $noie, $iever;
		$purl=$noie['purl'];	
	if (!$noie["infowin"]) return;
	if (!$iever or $iever>$noie["version"]) return;
	
	echo ('<script type="text/javascript">
		jQuery(document).ready( function() {
			var noiebody = document.body;
			noiebody.innerHTML = "<div id=\'noie\'><div class=\'closebutton\'><a href=\'#\' onclick=\'javascript:js_hide_noie(); return false;\'><img src=\''.$purl.'images/close.png\' alt=\'X\'/></a></div><div style=\'width: 940px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;\'><img src=\''.$purl.'images/ie_skull.png\'/><div style=\'width: 275px; float: left; font-family: Arial, sans-serif;\'><div style=\'font-size: 14px; font-weight: bold; margin-top: 12px;\'>'.$noie['maintext'].'</div><div style=\'font-size: 12px; margin-top: 6px; line-height: 12px;\'>'.$noie['subtext'].'</div></div><div id=\'noielinks\'></div><div id=\'noiebetter\'><img src=\''.$purl.'images/better.png\'/><a style=\'position:absolute; bottom:0; right:3px;color:#444444;font-size:8px;\' href=\http://wordpress.losmuchachos.at/no_ie_welcome/>No IE Welcome - Plugin by gnarf</a></div></div></div>" + noiebody.innerHTML;
	jQuery("div#noie").show(2000);
	');
	
	$links="";
	if ($noie['link_firefox']) $links.='<a href=\'http://www.firefox.com\' target=\'_blank\' title=\'Get Firefox\'><img src=\''.$purl.'images/firefox.png\' alt=\'Get Firefox\'/></a>';

	if ($noie['link_safari']) $links.='<a href=\'http://www.apple.com/safari/download/\' target=\'_blank\' title=\'Get Safari\'><img src=\''.$purl.'images/safari.png\' alt=\'Get Firefox\'/></a>';

	if ($noie['link_chrome']) $links.='<a href=\'http://www.google.com/chrome/\' target=\'_blank\' title=\'Get Google Chrome\'><img src=\''.$purl.'images/chrome.png\' alt=\'Get Firefox\'/></a>';

	if ($noie['link_ie']) $links.='<a href=\'http://www.browserforthebetter.com/download.html\' target=\'_blank\' title=\'Update IE\'><img src=\''.$purl.'images/ie.png\' alt=\'Update IE\'/></a>';

	if ($noie['link_opera']) $links.='<a href=\'http://www.opera.com/\' target=\'_blank\' title=\'Get Opera\'><img src=\''.$purl.'images/opera.png\' alt=\'Get Opera\'/></a>';
	
	
	echo('document.getElementById("noielinks").innerHTML="'.$links.'";');
	

	if ($noie["hideafter"]){
			echo('setTimeout( function() {jQuery("div#noie").hide(1000);}, '.$noie["hideafter"].'000);');
	}



		echo('})
		function js_hide_noie(){jQuery("div#noie").hide(1000);}</script>');

	
} // end function add_noie_js




// --------------------------------------------------------------------------------------------------------------
// -------------------------------------- admin options menu ----------------------------------------------------
// --------------------------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------------------------



function noie_admin_menu(){
	add_options_page('noieopts', 'No IE Welcome', 'administrator', 'noie', 'noie_options_menu');
}


function noie_options_menu() {
global $noie;
    $noie = get_option("noie"); 
	if ($_POST["action"] == "saveconfiguration") {
				$noie=noie_update_options($_POST['noie']);
				update_option('noie',$noie);
				echo '<div class="updated"><p><strong> Settings saved !! </strong></p></div>';
	}
	

	echo ('
		<div class="wrap">
		<form method="post" name="noieset">
			<h2>No IE Welcome - Settings Page</h2>
		
	');
	


	// Inside this block add all of your options.
	
	if($noie['infowin'] == 1){ $infowin = 'checked="checked"'; }
	if($noie['link_firefox'] == 1){ $link_firefox = 'checked="checked"'; }
	if($noie['link_safari'] == 1){ $link_safari = 'checked="checked"'; }
	if($noie['link_opera'] == 1){ $link_opera = 'checked="checked"'; }
	if($noie['link_ie'] == 1){ $link_ie = 'checked="checked"'; }
	if($noie['link_safari'] == 1){ $link_safari = 'checked="checked"'; }
	if($noie['link_chrome'] == 1){ $link_chrome = 'checked="checked"'; }

	
	echo ('

		<table>
		<tr>
		<td>React if IE-version <= </td>
		<td><select name="noie[version]">
			<option value="6"');
		if ($noie['version']==6) echo('selected="selected" ');
		echo('>6</option>
			<option value="7"');
		if ($noie['version']==7) echo('selected="selected" ');
		echo('>7</option>
			<option value="999"');
		if ($noie['version']==999) echo('selected="selected" ');
		echo('>all Versions</option>
		
		</td>
		</tr>

		
		<tr>
		<td>Theme for IE:</td>
		<td><select name="noie[theme]"><option value="">no special Theme</option>');
		
		$th=get_themes();
		foreach($th as $name => $values){
			echo ('<option ');
			
			if ($name==$noie['theme']) echo('selected="selected" ');
			
			echo('value="'.$name.'">'.$name.'</option>');
			
			}	

		
		
	echo('</select></td>
		</tr>
	
		<tr>
        <td>Show Infobar at top of Screen</td>
   		<td><input type="checkbox" value="1" '.$infowin.' name="noie[infowin]"></td>
   		</tr>

	
		<tr>
		<td>Infobar Main Text:</td>
		<td><input type="text" value="'.$noie[maintext].'" size="30" name="noie[maintext]"></td>
		</tr>
		
		
		<tr>
		<td>Infobar details:</td>
		<td><input type="text" value="'.$noie[subtext].'" size="80" name="noie[subtext]"></td>
		</tr>
		
		<tr>
		<td>hide Infobar after :</td>
		<td><input type="text" value="'.$noie[hideafter].'" size="5" name="noie[hideafter]"> sec. (0 = never)</td>
		</tr>

		<tr><td colspan="2"><b>Show the following Links to other Browsers</b></td></tr>

		<tr>
        <td>Firefox</td>
   		<td><input type="checkbox" value="1" '.$link_firefox.' name="noie[link_firefox]"></td>
   		</tr>

		<tr>
        <td>Safari</td>
   		<td><input type="checkbox" value="1" '.$link_safari.' name="noie[link_safari]"></td>
   		</tr>

		<tr>
        <td>Google Chrome</td>
   		<td><input type="checkbox" value="1" '.$link_chrome.' name="noie[link_chrome]"></td>
   		</tr>

		<tr>
        <td>Opera</td>
   		<td><input type="checkbox" value="1" '.$link_opera.' name="noie[link_opera]"></td>
   		</tr>

		<tr>
        <td>IE</td>
   		<td><input type="checkbox" value="1" '.$link_ie.' name="noie[link_ie]"></td>
   		</tr>



		
	');
		
	echo ('	</table>
			<input type="hidden" name="action" value="saveconfiguration">
			<input type="submit" value="Save">
			</form></fieldset></div>');    

}






function noie_update_options($options){
global $noie;
//This is the section where we add individual rules to single options (see checkbox part.)


// End that section.
	while (list($option, $value) = each($options)) {
// this line here just fixes individual server bugs.
// If our user has magic quotes turned on and then wordpress tries to add slashes to it we will have everything double slashed.
		if( get_magic_quotes_gpc() ) { 
		$value = stripslashes($value);
		}
		$noie[$option] =$value;
	}
	
	
$noie['pu_maintext'] = stripslashes(htmlspecialchars($noie['pu_maintext'],ENT_QUOTES));
if(!$options['infowin']){$noie['infowin'] = 0; }
if(!$options['link_ie']){$noie['link_ie'] = 0; }
if(!$options['link_firefox']){$noie['link_firefox'] = 0; }
if(!$options['link_opera']){$noie['link_opera'] = 0; }
if(!$options['link_safari']){$noie['link_safari'] = 0; }
if(!$options['link_chrome']){$noie['link_chrome'] = 0; }
	
	
return $noie;
}


function noie_debug() {
	global $iever, $noie;

	if(!isset($_GET['noiedebug'])) return;
	echo ("<hr><h1> NoIE Debug</h1>");
	echo ('<table><tr><td>HTTP_USER_AGENT</td><td>'.$_SERVER["HTTP_USER_AGENT"].'</td></tr>');
	echo ('<tr><td>iever</td><td>'.$iever.'</td></tr>');
	echo ('<tr><td>noie[version]</td><td>'.$noie['version'].'</td></tr>');
	
	
	echo ('</table>');
	if($iever>0 and $iever <=$noie['version']) {echo ('<h2>NoIE Welcome should trigger !!</h2>');} else echo('<h3>NoIE should NOT trigger !</h3>');
	
		
		
}




add_action('admin_menu', 'noie_admin_menu'); // admin settings menü zufügen
add_filter('template', 'noie_decide_theme'); // richtiges theme wählen
add_filter('stylesheet', 'noie_decide_theme'); // richtiges stylesheet anwenden
add_action('wp_print_styles', 'add_noie_stylesheet');
add_action('wp_head', 'add_noie_js'); // javascript hinzufügen
add_action('wp_footer', 'noie_debug'); // debugging
add_action('plugins_loaded', 'noie_init'); // plugin und globale variablen initialisieren


?>