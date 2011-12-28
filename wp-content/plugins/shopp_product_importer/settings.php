<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<div class="wrap shopp">
<?php	
	if (isset($_GET['clean'])) {
		if ($_GET['clean'] == 'clean') {
			$this->clean_shopp_settings();
		}
	}
	global $Shopp;	
	$shopp_data_version = (int)$Shopp->Settings->get('db_version');
	$shopp_first_run = $Shopp->Settings->get('display_welcome');
	$shopp_setup_status = $Shopp->Settings->get('shopp_setup');	
	$shopp_maintenance_mode = $Shopp->Settings->get('maintenance');	
	
	//Maintenance Message??? 1.1 dev
	if (SHOPP_VERSION >= '1.1') {
		if ($data_version >= 1100 || $shopp_first_run != "off") {
			exit("<h2>Shopp Product Importer</h2><p>Complete Shopp installation prior to importing CSV's.</p>");
			return false;
		}
	} else {
		if ($shopp_setup_status != "completed" || $shopp_maintenance_mode != "off" || $shopp_first_run != "off") {
			exit("<h2>Shopp Product Importer</h2><p>Complete Shopp installation prior to importing CSV's.</p>");
			return false;
		}
	}
	
	$has_error = false;
	$uuid = uniqid();  	
	if (isset($_FILES["csvupload"])) {
  		$nonce=$_REQUEST['_wpnonce'];
  		if (! wp_verify_nonce($nonce, 'shopp-importer') ) die('Security check'); 
		$upload_name = "csvupload";
		$max_file_size_in_bytes = 2147483647;				
		$extension_whitelist = array("csv", "txt");	
		$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				
	
		$MAX_FILENAME_LENGTH = 260;
		$file_name = "";
		$file_extension = "";
		$uploadErrors = array(
	        0=>"There is no error, the file uploaded with success.",
	        1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
	        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
	        3=>"The uploaded file was only partially uploaded.",
	        4=>"No file was uploaded.",
	        6=>"Missing a temporary folder."
		);
	
		if (!isset($_FILES["csvupload"])) {
			$updated = "No upload found in \$_FILES for " . $upload_name;
			$has_error = true;
		} else if (isset($_FILES["csvupload"]["error"]) && $_FILES[$upload_name]["error"] != 0) {
			$updated = $uploadErrors[$_FILES["csvupload"]["error"]];
			$has_error = true;
		} else if (!isset($_FILES["csvupload"]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
			$updated = "Upload failed is_uploaded_file test.";
			$has_error = true;
		} else if (!isset($_FILES["csvupload"]['name'])) {
			$updated = "File has no name.";
			$has_error = true;
		} else {
			//echo " ALL GOOD SO FAR!!!";
		}
	
		clearstatcache();
		$file_size = @filesize($_FILES["csvupload"]["tmp_name"]);
		error_log("Filesize: ".print_r($_FILES["csvupload"],true),0);

		if (!isset($_FILES["csvupload"]["tmp_name"])) {
			$updated = "File was not accepted.";
			$has_error = true;
		} elseif (!$file_size || $file_size > $max_file_size_in_bytes) {
			$updated = "File exceeds the maximum allowed size.";
			$has_error = true;
		} else {
			//echo " ALL GOOD SO FAR!!!";
		}
	
		if ($file_size <= 0) {
			if ($_FILES["csvupload"]["error"] > 0) 
				$updated = $uploadErrors[$_FILES["csvupload"]["error"]];
			else 	
				$updated = "File size outside allowed lower bound.";
			$has_error = true;
		} else {
			//echo " ALL GOOD SO FAR!!!";
		}
	
		$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES["csvupload"]['name']));
		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
			/*seems to come through even when not uploading a file */
			//$updated = "Invalid file name (*)";
			$updated = "";
			$has_error = true;
		} else {
			//echo " ALL GOOD SO FAR!!!";
		}
		
		if (!$has_error) {
			$csvs_path = realpath(dirname(dirname(dirname(__FILE__)))).'/csvs/';
			if (file_exists($csvs_path.$file_name)) {
				unlink($csvs_path.$file_name);
			}
	
			$path_info = pathinfo($_FILES[$upload_name]['name']);
			$file_extension = $path_info["extension"];
			$is_valid_extension = false;
			foreach ($extension_whitelist as $extension) {
				if (strcasecmp($file_extension, $extension) == 0) {
					$is_valid_extension = true;
					break;
				}
			}
			
			if (!$is_valid_extension) {
				$updated = "Invalid file extension.";
			}
		
			if (is_uploaded_file($_FILES['csvupload']['tmp_name'])) {
				if (!file_exists($csvs_path)) mkdir($csvs_path);
				chmod($csvs_path,0755);
		   		$uploaded_file = file_get_contents($_FILES['csvupload']['tmp_name']);
		   		$handle = fopen($csvs_path.$file_name, "w");
		   		fwrite($handle, $uploaded_file );
		   		$updated = "File uploaded successfully: ".$csvs_path.$file_name;
		   		fclose($handle);
		   		$this->Shopp->Settings->save('catskin_importer_file',$file_name);		   		
			} else {
				echo "The file didn't upload...";
			}		

	  	} else {
	  		//Don't worry we aren't trying to upload a file in this case
	  	}
	  	
  	} 	
	
 ?>
	<?php if (!empty($updated)): ?><div id="message" class="updated fade"><p><?php echo $updated; ?></p></div><?php endif; ?>

	<form enctype="multipart/form-data" name="settings" id="general" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
		<?php wp_nonce_field('shopp-importer'); ?>
	<div id="main-container" style="position:relative; width:100%; float:left; line-height:200%;">
		<div style="float:right; line-height:160%;">
			<img src="<?php echo WP_PLUGIN_URL."/".$this->directory; ?>/images/shopp_product_importer_csv_logo.png" style="border:0px; position:relative; display:block; margin:12px 4px;"/><p>&nbsp;</p>
			
			<div style="background:#f33; color:#fff; font-weight:bold; font-size:13px; border-radius: 10px; padding:5px; margin:-10px 5px 5px 5px; width:266px;"><div style="background:#fff; color:#f00; border-radius:3px; padding:6px;">WARNING:</div><div style="padding:10px;">This version of shopp product importer is for fresh complete inserts only. <u>Disable this plugin</u> if you need to keep products, prices, categories, images and tags already saved into shopp.</div></div>		
			<div id="todo-list-container" style="display:none; position:relative; background:#ffa; font-weight:bold; font-size:13px; border-radius: 10px; padding:5px; margin:5px; width:266px;">
				<div style="background:#fff; color:#333; border-radius:3px; padding:6px;">TO-DO LIST:</div>
				<ol id="todo-list" style="font-weight:bold; line-height:210%; margin:10px; list-style:inside;"></ol>
			</div>	
		</div>
		<h2><?php _e('Shopp Product Importer','Shopp'); ?></h2>
		<p><?php _e('Click the help link for instructions and samples.	','Shopp'); ?><br/></p>
		<div style="margin:5px 300px 5px 0px; padding:20px; background:#fff; border-radius:10px;">
			<table cellspacing="4">
				<tr>
					<td style="padding:5px; width:300px; vertical-align:top;border-bottom:1px solid #fff;"><b><label for="catskin_importer_file"><?php _e('CSV to import','Shopp'); ?>: </label></b></td>
					<td style="padding:5px; width:auto; vertical-align:top;border-bottom:1px solid #fff;"><input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
		<?php 
			if ($handle = opendir($this->csv_get_path)) {
			    echo "<select name='settings[catskin_importer_file]' onchange='javascript:(function($){update_required();})(jQuery);' >";
			    echo "<option value='no-file-selected'>Select File</option>";
				
			    /* This is the correct way to loop over the directory. */
			    while (false !== ($file = readdir($handle))) {
					$path_parts = pathinfo($this->csv_get_path.$file);
					if ($path_parts['extension'] == 'csv') {			    	
				    	if (attribute_escape($this->Shopp->Settings->get('catskin_importer_file')) == $file) { $selected = ' selected="selected" '; } else { $selected = ''; }
				        echo "<option value='$file' $selected>$file</option>\n";
			        }
			    }
			
			    /* This is the WRONG way to loop over the directory. */
			    while ($file = readdir($handle)) {
			        echo "$file\n";
			    }
			 	echo "</select>";
			    closedir($handle);
			} else {
			    echo "<select name='settings[catskin_importer_file]' onchange='javascript:(function($){update_required();})(jQuery);' >";
			    echo "<option value='no-file-selected'>Select File</option>";
			 	echo "</select>";					
			}
		?>
		<?php _e("list contains csv's uploaded to <b>/wp-content/csvs/</b> folder.",'Shopp'); ?></td>
				</tr>
				<tr>
					<td style="padding:5px; width:300px; vertical-align:top; border-bottom:1px solid #fff;"><b><label for="csvupload"><?php _e('Upload a CSV','Shopp'); ?>: </label></b></td>
					<td style="padding:5px; width:auto; vertical-align:top;border-bottom:1px solid #fff;"><input type="file" name="csvupload"  onchange="javascript:(function($){update_required();})(jQuery);" /> <br/><i><?php _e('If the upload utility does not save your CSV file to the list, create the <b>/wp-content/csvs/</b> folder and FTP your CSV file to that location. Refresh this page and the CSV should then be available to use.','Shopp'); ?></i></td>
				</tr>
				<tr>
					<td style="padding:5px; width:300px; vertical-align:top; border-bottom:1px solid #fff;"><b><label for="catskin_importer_csv_path"><?php _e("Path to FTP'd HTML descriptions",'Shopp'); ?>: </label></b></td>
					<td style="padding:5px; width:auto; vertical-align:top;border-bottom:1px solid #fff;"><?php $html_path = (strlen($this->Shopp->Settings->get('catskin_importer_html_path'))>0?attribute_escape($this->Shopp->Settings->get('catskin_importer_html_path')):get_option("siteurl").'/wp-content/product_htmls/')?>				
		<input type="text" name="settings[catskin_importer_html_path]" value="<?php echo $html_path; ?>" id="catskin_importer_html_path" size="100" /><br/><i><?php _e('Only required when mapping product descriptions to <b>Description [HTML uploaded filename]</b>.','Shopp'); ?></i></td>
				</tr>
				<tr>
					<td style="padding:5px; width:300px; vertical-align:top;"><b><label><?php _e('CSV contain a header row','Shopp'); ?>: </label></b></td>
					<td style="padding:5px; width:auto; vertical-align:top;"><input type="hidden" name="settings[catskin_importer_has_headers]" value="no" /><input type="checkbox" name="settings[catskin_importer_has_headers]" value="yes" id="catskin_importer_has_headers"<?php if ($this->Shopp->Settings->get('catskin_importer_has_headers') == "yes") echo ' checked="checked"'?> onchange="javascript:update_required();"/><label for="catskin_importer_has_headers"></label></td>
				</tr>
			</table>		
			<input type="hidden" name="settings[catskin_importer_empty_first]" value="yes" /><!--<input type="checkbox" name="settings[catskin_importer_empty_first]" value="yes" id="catskin_importer_empty_first"<?php if ($this->Shopp->Settings->get('catskin_importer_empty_first') == "yes") echo ' checked="checked"'?> onchange="javascript:update_required();" /><label for="catskin_importer_empty_first"> <?php _e('Empty existing products prior to import?','Shopp'); ?></label><br />-->
			<input type="hidden" name="settings[catskin_importer_clear_prices]" value="no" /><!--<input type="checkbox" name="settings[catskin_importer_clear_prices]" value="yes" id="catskin_importer_clear_prices"<?php if ($this->Shopp->Settings->get('catskin_importer_clear_prices') == "yes") echo ' checked="checked"'?> onchange="javascript:update_required();" /><label for="catskin_importer_clear_prices"> <?php _e('If Product exists, clear existing pricelines (Variations) prior to import?','Shopp'); ?></label><br /> -->
			<input type="hidden" name="settings[catskin_importer_do_not_alter_additional_details]" value="no" /><!--<input type="checkbox" name="settings[catskin_importer_do_not_alter_additional_details]" value="yes" id="catskin_importer_do_not_alter_additional_details"<?php if ($this->Shopp->Settings->get('catskin_importer_do_not_alter_additional_details') == "yes") echo ' checked="checked"'?> onchange="javascript:update_required();" /><label for="catskin_importer_do_not_alter_additional_details"> <?php _e("if Checked, DO NOT alter product's existing Descriptions and Images...",'Shopp'); ?></label><br />--> 
		</div>
		<div style=" padding:10px; margin:15px 0px; background:#fff; border:1px solid #999;-moz-border-radius:8px;-webkit-border-radius:8px; color:#000; clear:both;">
			<div style="float:right;" class="ready"><input type="submit" class="button-primary" id="run-spi-import-now" name="perform_import" value="<?php _e('Run Importer','Shopp'); ?>" /></div>
			<div style="float:left; display:none;" class="needs-update"><input type="submit" class="button-primary" name="save" value="<?php _e('Update Importer Settings','Shopp'); ?>" /></div>
			<div style="clear:both;"></div>
		</div>
		
		<div id="spi-show-when-importing" style="display:none;">
			<div>
				<div><div id="spi-ajax-loader" style="float:left;"><div style="float:left;"><img src="<?php echo get_option('siteurl').'/wp-content/plugins/'.$this->directory.'/ajax-loader.gif'?>" /></div><div id="progressbar" style="float:left;"></div></div></div>
				<div id="imported-rows" style="-moz-border-radius:8px;-webkit-border-radius:8px;-moz-box-shadow: 1px 1px 3px #000; -webkit-box-shadow: 1px 1px 3px #000; border:1px solid #bbb;font-size:12px; font-weight:bold; padding:3px; margin:2px; background:#eee; color:#000; clear:both; padding:10px;"></div>
			</div>
		</div>
		<?php if (!isset($importing_now) && ($this->Shopp->Settings->get('catskin_importer_file') != 'no-file-selected') && strlen($this->Shopp->Settings->get('catskin_importer_file')) != 0): ?>
			<?php 
				unset($_SESSION['spi_product_importer_data']);
				unset($_SESSION['spi_mapped_product_ids']);
			?>
			<?php 
				function doselector($column_number,$object_context) {
					$existing_map = $object_context->Shopp->Settings->get('catskin_importer_column_map');
					$output = "";
					$catskin_importer_type = $existing_map[$column_number]['type'];	
					$output .= "<select class='field-type-selector' name='settings[catskin_importer_column_map][{$column_number}][type]'  onchange='javascript:update_required();'>";
					$output .= "<option value='' ".(''==$catskin_importer_type?'selected=selected':'').">Don't Import</option>";			
					$output .= "<option value='id' ".('id'==$catskin_importer_type?'selected=selected':'').">Product Identifier</option>";			
					$output .= "<option value='category' ".('category'==$catskin_importer_type?'selected=selected':'').">Category(s) [Slash Delimited Text]</option>";			
					$output .= "<option value='description' ".('description'==$catskin_importer_type?'selected="selected"':'').">Description [HTML uploaded filename]</option>";
					$output .= "<option value='descriptiontext' ".('descriptiontext'==$catskin_importer_type?'selected="selected"':'').">Description [Text]</option>";
					$output .= "<option value='detail' ".('detail'==$catskin_importer_type?'selected="selected"':'').">Detail [Text]</option>";
					$output .= "<option value='featured' ".('featured'==$catskin_importer_type?'selected="selected"':'').">Featured [ON,OFF] (*auto OFF)</option>";
					$output .= "<option value='image' ".('image'==$catskin_importer_type?'selected="selected"':'').">Image [URL external filename]</option>";	
					$output .= "<option value='inventory' ".('inventory'==$catskin_importer_type?'selected="selected"':'').">Inventory [ON,OFF] (*auto OFF)</option>";							
					$output .= "<option value='name' ".('name'==$catskin_importer_type?'selected="selected"':'').">Name [Text]</option>";
					$output .= "<option value='price' ".('price'==$catskin_importer_type?'selected="selected"':'').">Price [Text]</option>";							
					$output .= "<option value='published' ".('published'==$catskin_importer_type?'selected="selected"':'').">Published [ON,OFF] (*auto ON)</option>";		
					$output .= "<option value='sale' ".('sale'==$catskin_importer_type?'selected="selected"':'').">Sale [ON,OFF] (*auto OFF)</option>";							
					$output .= "<option value='saleprice' ".('saleprice'==$catskin_importer_type?'selected="selected"':'').">Sale Price [Text]</option>";							
					$output .= "<option value='shipfee' ".('shipfee'==$catskin_importer_type?'selected="selected"':'').">Shipping Fee [Text]</option>";	
					$output .= "<option value='sku' ".('sku'==$catskin_importer_type?'selected="selected"':'').">SKU [Text]</option>";
					$output .= "<option value='slug' ".('slug'==$catskin_importer_type?'selected="selected"':'').">Slug [Text] (*auto product-name)</option>";
					$output .= "<option value='order' ".('order'==$catskin_importer_type?'selected="selected"':'').">Sort Order [Number] (*auto 0)</option>";
					$output .= "<option value='stock' ".('stock'==$catskin_importer_type?'selected="selected"':'').">Stock Quantity [Number]</option>";							
					$output .= "<option value='summary' ".('summary'==$catskin_importer_type?'selected="selected"':'').">Summary [Text]</option>";
					$output .= "<option value='tag' ".('tag'==$catskin_importer_type?'selected="selected"':'').">Tag(s) [Text]</option>";
					$output .= "<option value='tax' ".('tax'==$catskin_importer_type?'selected="selected"':'').">Tax [ON,OFF] (*auto ON)</option>";							
					$output .= "<option value='price_type' ".('price_type'==$catskin_importer_type?'selected="selected"':'').">Type [Shipped,Virtual,Download,Donation,N/A] (*auto Shipped)</option>";
					$output .= "<option value='variation' ".('variation'==$catskin_importer_type?'selected="selected"':'').">Variation(s) [Text]</option>";
					$output .= "<option value='weight' ".('weight'==$catskin_importer_type?'selected="selected"':'').">Weight [Text]</option>";
					$output .= "</select>";		
					$output .= "<input type='text' class='catskin_variation_label_editor' id='catskin_importer_column_map_label_{$column_number}' name='settings[catskin_importer_column_map][{$column_number}][label]' value='".$existing_map[$column_number]['label']."'  style='display:none;'  onchange='javascript:update_required();'/><label for='settings[catskin_importer_column_map][{$column_number}][label]' style='display:none; float:left; padding-top:5px; padding-left:5px;'>Name: </label>";	
					return $output;
				}
				$filename = $this->Shopp->Settings->get('catskin_importer_file');
				$spi_files = new spi_files($this);
				$this->examine_data = $spi_files->load_examine_csv($filename,true);	
				unset($spi_files);
				if (isset($this->examine_data) && strlen($filename) > 0) {
					if (is_array($this->examine_data)) {
						$row_count = count($this->examine_data);
						if ($this->Shopp->Settings->get('catskin_importer_has_headers') == "yes") $row_count--;
						$col_count = count($this->examine_data[0]);
						$this->Shopp->Settings->save('catskin_importer_row_count',$row_count);
						$this->Shopp->Settings->save('catskin_importer_column_count',$col_count);
						$this->ajax_load_file();
						echo "<p>Rows in file: ".$row_count."</p>";
						
						echo "<table cellspacing='0' style='border:1px solid #999; width:100%;-moz-border-radius:8px;-webkit-border-radius:8px;-moz-box-shadow: 1px 1px 3px #000; -webkit-box-shadow: 1px 1px 3px #000; border:1px solid #bbb;font-size:12px; font-weight:bold; padding:3px; margin:2px; background:#eee; color:#000; clear:both; padding:10px; line-height:100%;'><tr><td style='border-bottom:1px solid #999; color:#fff; background:#BBB; padding:5px;'>Cols</td><td style='border-bottom:1px solid #999; color:#fff; background:#BBB;  padding:5px;'>First Row</td><td style='border-bottom:1px solid #999; color:#fff; background:#BBB;  padding:5px;'>Column Mapping</td><td style='border-bottom:1px solid #999; color:#fff; background:#BBB;  padding:5px;'>Second Row</td></tr>";
						$col_counter = 0;
						for ($i = 0; $i < count($this->examine_data[0]); $i++) {
							$col_counter++;
							echo "<tr><td style='border-bottom:1px solid #ccc; padding:5px; background:#eee;'><span style='color:#999;'>Col. {$col_counter}:</span></td><td style='border-bottom:1px solid #ccc; padding:5px; background:#fff;'><b>".$this->examine_data[0][$i]."</b></td><td style='border-bottom:1px solid #ccc; padding:5px; background:#eee;'>". doselector($i,$this)."</td><td style='border-bottom:1px solid #ccc; padding:5px; background:#fff;'><span style='color:#999;'>Next Row Data :</span> ".$this->examine_data[1][$i]."</td></tr>";
						}
						echo "</table>";
						?>
								<div style=" padding:10px; margin:15px 0px; background:#fff; border:1px solid #999;-moz-border-radius:8px;-webkit-border-radius:8px; color:#000; clear:both;">
									<div style="float:right;" class="ready"><input type="submit" class="button-primary" id="run-spi-import-now" name="perform_import" value="<?php _e('Run Importer','Shopp'); ?>" /></div>
									<div style="float:left;" class="needs-update"><input type="submit" class="button-primary" name="save" value="<?php _e('Update Importer Settings','Shopp'); ?>" /></div>
									<div style="clear:both;"></div>
								</div>					
						<?php
					}	
				}?>
			<?php elseif (isset($importing_now)): ?>
			<script type="text/javascript">
			(function($){

				function view_start() {
					$('#spi-show-when-importing').show('fast');
					$('#run-spi-import-now').attr("value","Please Wait... The importer is running");
					$(".needs-update > input").attr("value","Emergency Stop!");		
					$(".needs-update > input").css("background","#a00");			
					$('.needs-update').show(100);	
				}
				
				function view_finish() {
					$(".needs-update > input").attr("value","All Done");		
					$(".needs-update > input").css("background","#0a0");			
				}				

				function view_requesting() {
					$('#spi-ajax-loader').show('slow');
					$('#importing-rows').append("<div style='clear:both;'></div>");
					$('#imported-rows').append("<div style='clear:both;'></div>");				
				}		

				function view_error() {
					$('#spi-ajax-loader').hide('slow');
					$('#run-spi-import-now').attr("value","An Error Occured... Update Importer Settings before trying again...");
					$('#spi-show-when-importing').hide('fast');			
				}

				function view_after_all_rows_imported() {
					$('#spi-ajax-loader').hide('slow');	
					$('#importing-rows').prepend("<div>Import Complete!</div>").show('slow');
					$('#run-spi-import-now').hide('slow');
					view_finish();
				}
				
				function import_csv() {
					view_start();
					view_requesting();
					var maindata = {
							action: 'import_csv'
					};					
					jQuery.ajax({
						type: "POST",
						url: ajaxurl, 
						async: true,
						data: maindata, 
						success: function(response){
							$("#imported-rows").append(response);						
							import_products();
						},
						error: function(response){
							view_error();
						}
					});
				}
				
				function import_products() {
					var maindata = {
							action: 'import_products'
					};					
					jQuery.ajax({
						type: "POST",
						url: ajaxurl, 
						async: true,
						data: maindata, 
						success: function(response){		
							$("#imported-rows").append(response);
							$("#imported-rows").append("<h3>Uploading Images...</h3>");
							$("#imported-rows").append("<p>Sit back, relax, grab a coffee...</p>");
							next_image();
						},
						error: function(response){
							view_error();
						}
					});
				}	
				
				function next_image() {
					var maindata = {
							action: 'next_image',
							status: 20
					};					
					jQuery.ajax({
						type: "POST",
						url: ajaxurl, 
						async: true,
						data: maindata, 
						success: function(response){		
							if (response != 'no-more' && response != 'no-images') {
								$("#imported-rows").append(response);
								next_image();
							} else {
								if (response == 'no-more') $("#imported-rows").append("<div style='clear:both;'></div><p>There are no more images to upload from this CSV file</p><p><b>Congratulations! Your CSV has been imported, images and all...</b></p>");
								if (response == 'no-images') $("#imported-rows").append("<div style='clear:both;'></div><p>There are no images to upload from this CSV file</p><p><b>Congratulations! Your CSV has been imported...</b></p>");
								view_after_all_rows_imported();
							}
						},
						error: function(response){
							view_error();
						}
					});
				}												
				import_csv();
			})(jQuery);
			</script>	
		<?php else:?>
			<?php 
				unset($_SESSION['spi_product_importer_data']);
				unset($_SESSION['spi_mapped_product_ids']);
			?>		
			<script type="text/javascript">
				(function($){
					jQuery('#run-spi-import-now').hide();			
				})(jQuery);	
			</script>				
	    <?php endif; ?>
	    </div>
	</form>
</div>

<script type="text/javascript">
	function validate() {
		var validates = true;
		if (jQuery("select[name*='catskin_importer_file']").val() == 'no-file-selected'){
			validates = false;
			jQuery("#todo-list").append("<li>Upload or choose a CSV file to import.</li>");
			
		}
		var has_html_files = false;
		var path_exists = "<?php echo file_exists($this->html_get_path)?"YES":"NO"; ?>"
		if (path_exists === "NO"){
			jQuery("option[value='description']").each(function() {
				if (jQuery(this).attr("selected") == true) {
					has_html_files = true;
				}
			});	
			if (has_html_files) {
				validates = false;
				jQuery("#todo-list").append("<li>You need to specifiy the location of your uploaded HTML product description files, the one listed in 'Path to HTML files' doesn't exist.</li>");
			}	
		}
			
		var has_id = 0;
		jQuery("option[value='id']").each(function() {
			if (jQuery(this).attr("selected") == true) {
				has_id = has_id + 1;
			}
		});	
		if (has_id === 0) {
			validates = false;
			jQuery("#todo-list").append("<li>One column must contain a PRODUCT Identifier : The Product Identifier must be unique to the Product. If the product has Variations all variations should share the same Product Identifier.</li>");
		} else if (has_id > 1) {
			validates = false;
			jQuery("#todo-list").append("<li>Your column map contains multiple SKU columns.</li>");		
		}
				
		var has_sku = 0;
		jQuery("option[value='sku']").each(function() {
			if (jQuery(this).attr("selected") == true) {
				has_sku = has_sku +1;
			}
		});	
		if (has_sku === 0) {
			validates = false;
			jQuery("#todo-list").append("<li>One column must contain an SKU : The SKU must be a unique identifier for each row in the CSV file.</li>");
		} else if (has_sku > 1) {
			validates = false;
			jQuery("#todo-list").append("<li>Your column map contains multiple SKU columns</li>");		
		}
		var has_name = 0;
		jQuery("option[value='name']").each(function() {
			if (jQuery(this).attr("selected") == true) {
				has_name = has_name + 1;
			}
		});	
		if (has_name === 0) {
			validates = false;
			jQuery("#todo-list").append("<li>One column must contain a product name</li>");
		} else if (has_name > 1) {
			validates = false;
			jQuery("#todo-list").append("<li>Your column map contains multiple product name columns</li>");		
		}
		var has_price = 0;
		jQuery("option[value='price']").each(function() {
			if (jQuery(this).attr("selected") == true) {
				has_price = has_price + 1;
			}
		});	
		if (has_price === 0) {
			validates = false;
			jQuery("#todo-list").append("<li>One column must contain a price</li>");
		} else if (has_price > 1) {
			validates = false;
			jQuery("#todo-list").append("<li>Your column map contains multiple price columns</li>");				
		}
		
		var valid_variation_names = true;
		var no_variation_names = false;
		jQuery(".catskin_variation_label_editor").each(function() {
			if (jQuery(this).is(':visible')) {
				var the_name = jQuery(this).val();
				if (the_name.indexOf(' ') > -1) {
					valid_variation_names = false;
				}
				if (the_name.length === 0) {
					no_variation_names = true;
				}
			}
		});
		
		if (!valid_variation_names) {
			validates = false;
			jQuery("#todo-list").append("<li>Variation & Detail Names Must be Alpha Numeric without spaces! (Applies to Shopp Product Importer, not Shopp Itself). </li>");
		}		
		
		if (no_variation_names) {
			validates = false;
			jQuery("#todo-list").append("<li>Variations and Details must be named using Alpha Numeric characters without spaces! (Colour, Size, Print, Condition). </li>");
		}	
					
		
		var has_category = false;
		jQuery("option[value='category']").each(function() {
			if (jQuery(this).attr("selected") == true) {
				has_category = true;
			}
		});	
				
		if (!has_category) {
			validates = false;
			jQuery("#todo-list").append("<li>At least one column must contain a category. You can have as many category columns as required. Sub-categories can be specified using forward slash (eg. /Main Category/Sub Category/Sub Sub Category). The importer will create the parent nodes of the Category tree for you. </li>");
		}						
			
		if (validates) {
			jQuery("#todo-list-container").hide(300);
		} else {
			jQuery("#todo-list-container").show(300);
		}
		return validates;
	}
	
	function perform_checks() {
		var is_importing_now = "<?php echo print_r(isset($importing_now)?"YES":"NO",true); ?>";
		if (is_importing_now == "NO") {
			does_val = validate();
			if (does_val === true) {
				ready_for_import();
			} else {
				update_required();
			}
		}
	}

	function update_required() {
			jQuery('.needs-update').show(100);
			jQuery(".needs-update > input").attr("value","Update Importer Settings");		
			jQuery(".needs-update > input").css("background","");
			jQuery('.ready').hide(1000);							
	}
	
	function ready_for_import() {	
			jQuery('.needs-update').hide(1000);
			jQuery('.ready').show(100);				
	}				

	(function($){	
		$('.field-type-selector').each(function() {
			if ($(this).attr("value") == 'variation' || $(this).attr("value") == 'detail' ) {
				$(this).next().show().next().show();
			}
		});					
		$('.field-type-selector').change(function() {
			if ($(this).attr("value") == 'variation' || $(this).attr("value") == 'detail') {
				$(this).next().show(400).next().show(400);
				
			} else {
				$(this).next().hide(100).attr("value","").next().hide(100);
			}
		});
		perform_checks();	
	})(jQuery);
</script>
