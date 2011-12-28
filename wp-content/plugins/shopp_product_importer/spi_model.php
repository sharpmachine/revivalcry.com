<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<?php
require_once('spi_files.php');
require_once('spi_images.php');
class spi_model {

	//Initialize Externals
	var $Shopp;
	var $spi;
	
	//Initialize Workers
	var $map 		= array();
	var $variations = array();
	var $products 	= array();
	var $categories = array();
	
	//Initialize Product
	var $global_variation_counter = 0;
	
	//Initialize Categories
	var $cat_index = 0;
	
	function spi_model($spi) {	
		$this->spi = $spi;
		$this->Shopp = $spi->Shopp;
		//Is this used?
		$this->cat_index = $this->get_next_shopp_category_id();
	}
	
	// !bookmark : Execution Functions
	function execute() {
		//The link between CSV and Known Shopp Fields... 
		//Map them out so we can work with them
		$this->initialize_map();
		
		//get_next_product selects the next product from the shopp_product_importer data table
		//which meets the status code criteria.
		
		//get_next_set selects all products in the table with the id returned by get_next_product
		
		//process_set updates the status for the rows we've just used so we don't reuse them.
		
		//0 - Initialize Variations
		$this->variations = array();
		while ($p_row = $this->get_next_product(0)) {
				$p_set = $this->get_next_set($p_row->spi_id);
				$this->initialize_variations($p_row->spi_id);
				$this->process_set($p_row->spi_id,1);
		}
		//1 - Populate Variations
		while ($p_row = $this->get_next_product(1)) {
				$p_set = $this->get_next_set($p_row->spi_id);
				$this->populate_variations($p_set);
				$this->process_set($p_row->spi_id,2);
		}
			
		//2 - Initialize Categories
		$this->categories = array();
		while ($p_row = $this->get_next_product(2)) {
				$p_set = $this->get_next_set($p_row->spi_id);
				$this->initialize_categories($p_row->spi_id);
				$this->process_set($p_row->spi_id,3);
		}
			
		//3 - Initialize Products
		$base_id = $this->get_next_shopp_product_id();
		while ($p_row = $this->get_next_product(3)) {
			$p_set = $this->get_next_set($p_row->spi_id);
			//Does the product already exist in shopp? 
			$existing_product = $this->product_exists($p_row->spi_name,$p_row->spi_sku);
			if ($existing_product) {
				$this->products[$existing_product] = new map_product();
				$this->initialize_product($this->products[$existing_product],$p_row->spi_id,$existing_product);
				$this->process_set($p_row->spi_id,10,$existing_product);
			} else {
				$this->products[$base_id] = new map_product();
				$this->initialize_product($this->products[$base_id],$p_row->spi_id,$base_id);
				$this->process_set($p_row->spi_id,10,$base_id);
				$base_id++;
			}
		}		
		//10 - Initialize Prices
		foreach ($this->products as $map_product) {
			$this->initialize_prices($map_product);
		}
		$this->process_all(20);
		//20 - All Done!
		return $this->products;
	}
	
	function execute_images() {		
		
		$this->initialize_map();
	
		//Populate Images
		$cnt = 0;
		$output = "";
		$images = array();
		//Debugging Any Images Exist... Check console or remove debug line from function any_images_exist
		if ($this->any_images_exist() > 0) {
			if ($p_row = $this->get_next_product(20)) {
				$p_set = $this->get_next_product(20,true);
				foreach ($p_set as $pmap) {	
					foreach ($this->map as $mset) {
						switch ($mset['type']) {
							case 'image':
								$value = $this->get_row_mapped_var($pmap->id,$mset['header']);
								if (strlen($value) > 0) {
									$cnt = $cnt + $this->_populate_image($p_set,$pmap,$mset);
									$output .= "<div style='float:left; display:inline; width:110px; height:130px; border:1px solid #CCC; background:#FFF; margin:3px; padding:5px; margin-top:15px;'><p style='text-align:center;'>sku: ".$this->get_row_mapped_var($p_row->id,"spi_sku")."</p><img src='".$value."' style='max-width:100px; max-height:90px;'/></div>";
								}
								break;
						}
					}
				}	
				$this->process_product($p_row->id,50);	
				return $output;
						
			} else {
				//$this->process_product($p_row->spi_id,50);	
				return "no-more";
			}
		} else {		
			return "no-images";
		}
	}	
	
	function execute_mega_query() {
		global $wpdb;
		$product_index = 0;
		$price_index = 0;
		$tag_index = 0;
		$detail_index = 0;
		$category_index = 0;
		$catalog_index = 0;
		$next_tag_id =  $this->get_next_shopp_tag_id();
		$used_tags = array();
		$next_category_id = $this->get_next_shopp_category_id();
		$used_categories = array();
		$values = "";
		$prices = "";
		$tags = "";
		$catalogs = "";
		$categories = "";
		foreach ($this->products as $map_product) {
			if ($product_index > 0) $append = ","; else $append = "";
			$spi_files = new spi_files($this->spi);
			if (strlen($map_product->description) > 0) {
				$description = $spi_files->load_html_from_file($map_product->description); 
			} else { 
				$description = 	$map_product->description_text;
			}
			unset($spi_files);
			$values .= $append."(";
				$values .= "'".mysql_real_escape_string($map_product->id) ."',";
				$values .= "'".mysql_real_escape_string($map_product->name) ."',";	
				$values .= "'".mysql_real_escape_string($this->defval($map_product->slug,sanitize_title($map_product->name))) ."',";	
				$values .= "'".mysql_real_escape_string($map_product->summary) ."',";	
				$values .= "'".mysql_real_escape_string($description) ."',";	
				$values .= "'".mysql_real_escape_string($this->defval($map_product->published,'on')) ."',";	
				$values .= "'".mysql_real_escape_string($this->defval($map_product->featured,'off')) ."',";	
				/*Existing method of setting the variations on|off field*/
				//$values .= (strlen($map_product->options) > 0)?"'on',":"'off',";	
				/*New Method of setting variations on|off field*/
				$values .= "'".mysql_real_escape_string($this->defval($map_product->has_variations,'off')) ."',";	
				$values .= "'".mysql_real_escape_string($map_product->options)."',";
				$values .= "CURDATE(),CURDATE(),";
				$values_minprice = 0;
				
				foreach ($map_product->prices as $price) {
					if ($price['price'] > $values_minprice) $values_minprice = $price['price'];
					if ($price_index > 0) $pappend = ","; else $pappend = "";
					$prices .= $pappend."(";
					
						$prices .= "'".mysql_real_escape_string($price['product'])."',";
						$prices .= "'".mysql_real_escape_string($price['options'])."',";
						$prices .= "'".mysql_real_escape_string($price['optionkey'])."',";
						$prices .= "'".mysql_real_escape_string($price['label'])."',";
						$prices .= "'".mysql_real_escape_string($price['context'])."',";
						$prices .= "'".mysql_real_escape_string($price['type'])."',";
						$prices .= "'".mysql_real_escape_string($price['sku'])."',";
						$prices .= "'".mysql_real_escape_string($price['price'])."',";
						$prices .= "'".mysql_real_escape_string($price['saleprice'])."',";
						$prices .= "'".mysql_real_escape_string($price['weight'])."',";
						$prices .= "'".mysql_real_escape_string($price['shipfee'])."',";
						$prices .= "'".mysql_real_escape_string($price['stock'])."',";
						$prices .= "'".mysql_real_escape_string($price['inventory'])."',";
						$prices .= "'".mysql_real_escape_string($price['sale'])."',";
						$prices .= "'".mysql_real_escape_string($price['shipping'])."',";
						$prices .= "'".mysql_real_escape_string($price['tax'])."',";
						$prices .= "'".mysql_real_escape_string($price['donation'])."',";
						$prices .= "'".mysql_real_escape_string($price['sortorder'])."',";
						$prices .= "CURDATE(),CURDATE()";
					
					$prices .= ")";
					$price_index++;
				}
				
				$values .= $values_minprice;
				
				if (count($map_product->tags) > 0) {
					foreach ($map_product->tags as $tag) {
						if (array_key_exists(htmlentities($tag->value, ENT_QUOTES, "UTF-8"), $used_tags) === false) {
							$used_tags[htmlentities($tag->value, ENT_QUOTES, "UTF-8")] = $next_tag_id;
							if ($tag_index > 0) $tappend = ","; else $tappend = "";
							$tags .= $tappend."(";
								$tags .= "'".mysql_real_escape_string($next_tag_id)."'".",";
								$tags .= "'".mysql_real_escape_string($tag->value)."'".",";	
								$tags .= "CURDATE(),CURDATE()";													
							$tags .= ")";
							$next_tag_id++;
							$tag_index++;
						}
						if ($catalog_index > 0) $cappend = ","; else $cappend = "";
						$catalogs .= $cappend."(";		
							$catalogs .= "'".mysql_real_escape_string($map_product->id)."'".",";
							$catalogs .= "'".mysql_real_escape_string($tag_index)."'".",";	
							$catalogs .= "'tag','0',";	
							$catalogs .= "CURDATE(),CURDATE()";									
						$catalogs .= ")";
						$catalog_index++;					
					}
				}
				
				if (count($map_product->details) > 0) {
					error_log(print_r($map_product,true),0);
					foreach ($map_product->details as $detail) {
						if (array_key_exists(htmlentities($detail->value, ENT_QUOTES, "UTF-8"), $used_details) === false) {
							$used_details[htmlentities($detail->value, ENT_QUOTES, "UTF-8")] = $next_detail_id;
							if ($detail_index > 0) $dappend = ","; else $dappend = "";
							$details .= $dappend."(";
								$details .= "'".mysql_real_escape_string($map_product->id)."'".","; //parent
								$details .= "'product'".","; //context
								$details .= "'spec'".","; //type
								$details .= "'".mysql_real_escape_string($detail->name)."'".","; //name
								$details .= "'".mysql_real_escape_string($detail->value)."'".","; //value
								$details .= "'".mysql_real_escape_string($next_detail_id)."'".","; //numeral
								$details .= "'".mysql_real_escape_string($next_detail_id)."'".","; //sortorder	
								$details .= "CURDATE(),CURDATE()";													
							$details .= ")";
							$next_detail_id++;
							$detail_index++;
						}				
					}
				}
				
				
				foreach ($this->categories as $category) {
					/*old code*/
					//if ($category->csv_product_id == $map_product->csv_id) {
					/*new code*/
					if (in_array($map_product->csv_id,$category->csv_product_ids,true)) {
					/*end new code*/
						if (array_key_exists($category->id, $used_categories) === false) {
							$used_categories[$category->id] = $category->id;
							if ($category_index > 0) $ctappend = ","; else $ctappend = "";
							$categories .= $ctappend."(";
								$categories .= "'".mysql_real_escape_string($category->id)."',";
								$categories .= "'".mysql_real_escape_string($category->parent_id)."',";	
								$categories .= "'".mysql_real_escape_string($category->value)."'".",";
								$categories .= "'".mysql_real_escape_string($category->slug)."',";	
								$categories .= "'".mysql_real_escape_string($category->uri)."',";
								$categories .= "'',";
								$categories .= "'off',";
								$categories .= "'off',";
								$categories .= "'off',";
								$categories .= "'disabled',";	
								$categories .= "'',";		
								$categories .= "'',";
								$categories .= "'',";
								$categories .= "'',";						
								$categories .= "CURDATE(),CURDATE()";													
							$categories .= ")";
							$next_category_id++;
							$category_index++;				
						} 
						
						foreach ($category->csv_product_ids as $csv_id) {
							if ($csv_id == $map_product->csv_id) {
								$prd = $this->product_by_csv_id($csv_id);
								if ($catalog_index > 0) $cappend = ","; else $cappend = "";
								$catalogs .= $cappend."(";		
									$catalogs .= "'".mysql_real_escape_string($prd->id)."',";	
									$catalogs .= "'".mysql_real_escape_string($category->id)."',";	
									$catalogs .= "'category','0',";	
									$catalogs .= "CURDATE(),CURDATE()";									
								$catalogs .= ")";
								$catalog_index++;
							}
						}	
					}	
				}				
					
			$values .= ")";
			$product_index++;
		}
		//Import Product Lines
		$query = " INSERT INTO {$wpdb->prefix}shopp_product (id,name,slug,summary,description,publish,featured,variations,options,created,modified,minprice) VALUES {$values};";
		$result1 = $wpdb->query($query);
		//Import Price Lines
		$query = " INSERT INTO {$wpdb->prefix}shopp_price (product,options,optionkey,label,context,type,sku,price,saleprice,weight,shipfee,stock,inventory,sale,shipping,tax,donation,sortorder,created,modified) VALUES {$prices}; ";
		$result2 = $wpdb->query($query);
		if (strlen($tags) > 0) {
		//Import Tags
			$query = " INSERT INTO {$wpdb->prefix}shopp_tag (id,name,created,modified) VALUES {$tags}; ";
			$result3 = $wpdb->query($query);
		} else {
			$result3 = 0;
		}
		if (strlen($categories) > 0) {
		//Import Categories
			$query = " INSERT INTO {$wpdb->prefix}shopp_category (id,parent,name,slug,uri,description,spectemplate,facetedmenus,variations,pricerange,priceranges,specs,options,prices,created,modified) VALUES {$categories}; ";
			$result4 = $wpdb->query($query);
		} else {
			$result4 = 0;
		}	
		if (strlen($catalogs) > 0) {		
		//Create Catalog Items
			$query = " INSERT INTO {$wpdb->prefix}shopp_catalog (product,parent,type,priority,created,modified) VALUES {$catalogs}; ";
			$result5 = $wpdb->query($query);
		} else {
			$result5 = 0;
		}	
		if (strlen($details) > 0) {
		//Import Details
			$query = " INSERT INTO {$wpdb->prefix}shopp_meta (parent,context,type,name,value,numeral,sortorder,created,modified) VALUES {$details}; ";
			$result3 = $wpdb->query($query);
		} else {
			$result3 = 0;
		}					
		
		return $result1 . " Products Imported, " . $result2 ." Prices Imported, ".$result3 ." Tags Imported ".$result4 ." Categories Imported ".$result5 ." Catalog Items Created... <br/>";
	}	
	
	function index_content() {
	
	
	}	
	
	// !bookmark : (End) Execution Functions	
	// !bookmark : Processing Fuctions	

	function initialize_map() {
		//Load the map from shopp's Settings table. Saved there by column mapping in 
		//importer settings page and apply it to an array that we can use to understand the 
		//data being pulled in.
		$map = $this->Shopp->Settings->get('catskin_importer_column_map');
		//initialize counters
		$column = 0;
		$variation = 0;
		$detail = 0;
		$category = 0;
		$tag = 0;
		$image = 0;
		//Using $map array create a global field map based on the currently active CSV 
		foreach ($map as $item) {
			//does the map item have a special power? 
			//Special power columns arent exclusive so we need to count 
			//how many of each special powers we have. 
			//$hidx holds the index conter for that special power
			switch ($item['type']) {
				case 'variation': $variation++; $hidx = $variation; break;
				case 'detail': $detail++; $hidx = $detail; break;
				case 'category': $category++; $hidx = $category; break;
				case 'tag': $tag++; $hidx = $tag; break;
				case 'image': $image++; $hidx = $image; break;	
				default: $hidx = '';
			}
			
			//We handle variations by name for labeling purposes so instead of getting an index 
			//it's given a name
			if ($item['type'] == 'variation' || $item['type'] == 'detail') 
				$column_header = 'spi_'.$item['label']; 
			else
				$column_header = 'spi_'.$item['type'].$hidx;
			
			//add the item to the map.
			$this->map[] = array('type'=>$item['type'],'label'=>$item['label'],'header'=>$column_header,'idx'=>$hidx);
		}
	}	
	
	function initialize_variations($csv_product_id) {
		foreach ($this->map as $mset) {
			switch ($mset['type']) {
				case 'variation':
					if ($this->any_exist($mset['header'],$csv_product_id) > 0) {
						$map_variation = new map_variation();
						$map_variation->name =  $mset['header'];
						$map_variation->csv_product_id = $csv_product_id;
						$map_variation->values = array();
						if (array_search($map_variation,$this->variations) === false) {
							$this->variations[] = $map_variation; 
						}
					}
					break;			
				}
		}				
	}	
	
	function populate_variations($product_set) {
		foreach ($product_set as $pmap) {	
			foreach ($this->map as $mset) {
				switch ($mset['type']) {
					case 'variation':
						$variation_value = new map_variation_value();
						eval('$variation_value->value = $pmap->'.$mset['header'].';');
						if ($this->find_variation($mset['header'],$pmap->spi_id) > -1 && 
							$this->find_variation_value(
								$this->variations[$this->find_variation($mset['header'],$pmap->spi_id)]->values,
									$variation_value->value) == -1)
										$this->variations[$this->find_variation($mset['header'],$pmap->spi_id)]->values[] = $variation_value;						
						break;
				}
			}
		}	
	}	
	
	
	function initialize_categories($csv_product_id) {
		$cat_index = $this->cat_index;
		$parent_index = 0;
		foreach ($this->map as $mset) {
			switch ($mset['type']) {
				case 'category':
					if ($this->any_exist($mset['header'],$csv_product_id) > 0) {
						//initialize our arrays for reuse
						$uri_array = array();
						//cat_string = the raw slash delimited category data
						$cat_string = $this->get_mapped_var($csv_product_id,$mset['header']);
						$cat_array = explode('/',$cat_string);
						//reverse the array for ease of use
						array_reverse($cat_array);
						for ($i=0; $i<sizeof($cat_array);$i++) {
							//build an array of category uri's we're going to use these as the 
							//unique identifier for categories
							$uri_array[$i] = sanitize_title_with_dashes($cat_array[$i]);
						}			
						for ($i=0; $i<sizeof($cat_array);$i++) {
							$map_category = new map_category();
							$map_category->name =  $mset['header'];
							$map_category->value = $cat_array[$i];
							$map_category->slug = sanitize_title_with_dashes($cat_array[$i]);
							$map_category->id = $cat_index;
							$map_category->parent_id = $parent_index;
							$map_category->csv_product_id = $csv_product_id;
							$map_category->csv_product_ids[] = $csv_product_id;
							$pop_array = $uri_array;							
							for ($j=0;$j<(sizeof($cat_array)-($i+1)); $j++ ) {
								array_pop($pop_array);
							}
							$parent_pop_array = $uri_array;
							for ($j=0;$j<(sizeof($cat_array)-($i)); $j++ ) {
								array_pop($parent_pop_array);
							}							
							if (sizeof($pop_array) == 1) {
								$map_category->parent_id = 0; 
							} else {
								$map_category->parent_id = $parent_index; 
							}
							$map_category->uri = join('/',$pop_array);
							$map_category->parent_uri = join('/',$parent_pop_array);
							$existing_shopp_category = $this->category_exists($map_category->uri);
							if (!is_null($this->category_by_uri($map_category->uri))) {
								$this->categories[$this->key_to_category_by_uri($map_category->uri)]->csv_product_ids[] = $csv_product_id;
							} else {
								if (is_null($this->category_by_uri($map_category->parent_uri))) {
									$map_category->parent_id = 0;
								} else {
									$parent_category = $this->category_by_uri($map_category->parent_uri);
									$map_category->parent_id = $parent_category->id;
								}
								if ($existing_shopp_category) {
									$map_category->id = $existing_shopp_category->id;
									$map_category->parent_id = $existing_shopp_category->parent;
								} else {
									$cat_index++;
								}
								$this->categories[] = $map_category;							
							}
						} 
						
					}
					break;			
				}
		}	
		$this->cat_index = $cat_index;	
	}		
	
	function initialize_product(&$map_product,$csv_product_id,$shopp_product_id) {
		$map_product->id = $shopp_product_id;
		$map_product->csv_id = $csv_product_id;
		$cat_index = $this->cat_index;
		foreach ($this->map as $mset) {
			$parent_index = 0;
			switch ($mset['type']) {
				case 'description':
					$map_product->description = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;					
				case 'descriptiontext':
					$map_product->description_text = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;															
				case 'featured':
					$map_product->featured = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'image':
					$map_image = new map_image();
					$map_image->name =  $mset['header'];
					$map_image->value = $this->get_mapped_var($csv_product_id,$mset['header']);
					if (strlen($map_image->value) > 0) $map_product->images[] = $map_image; 
					break;
				case 'inventory':
					$map_product->inventory = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'name':
					$map_product->name = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'price':
					$map_product->price = $this->parse_float($this->get_mapped_var($csv_product_id,$mset['header']));
					break;
				case 'published':
					$map_product->published = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'sale':
					$map_product->sale = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'saleprice':
					$map_product->sale_price = $this->parse_float($this->get_mapped_var($csv_product_id,$mset['header']));
					break;
				case 'shipfee':
					$map_product->ship_fee = $this->parse_float($this->get_mapped_var($csv_product_id,$mset['header']));
					break;
				case 'sku':
					$map_product->sku = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'slug':
					$map_product->slug = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'order':
					$map_product->order = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'stock':
					$map_product->stock = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'summary':
					$map_product->summary = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'tag':
					$map_tag = new map_tag();
					$map_tag->name =  $mset['header'];
					$map_tag->value = $this->get_mapped_var($csv_product_id,$mset['header']);
					if (strlen($map_tag->value) > 0) $map_product->tags[] = $map_tag; 
					break;
				case 'detail':
					$map_detail = new map_detail();
					$map_detail->name =  str_replace('spi_','',$mset['header']);
					$map_detail->value = $this->get_mapped_var($csv_product_id,$mset['header']);
					if (strlen($map_detail->value) > 0) $map_product->details[] = $map_detail; 
					break;						
				case 'tax':
					$map_product->tax = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'pricetype':
					$map_product->price_type = $this->get_mapped_var($csv_product_id,$mset['header']);
					break;
				case 'variation':
					if ($this->any_exist($mset['header'],$csv_product_id) > 0) {
						$map_variation = new map_variation();
						$map_variation->name =  $mset['header'];
						$this->global_variation_counter++;
						$map_variation->id = $this->global_variation_counter;
						$map_variation->values = array();
						$map_product->variations[] = $map_variation; 
					}
					break;
				case 'weight':
					$map_product->weight = $this->parse_float($this->get_mapped_var($csv_product_id,$mset['header']));
					break;
			}
		}
		if (!isset($map_product->variations)) { 
			$map_product->has_variations = 'off'; 
		} else {
			if (!is_array($map_product->variations)) {
				$map_product->has_variations = 'off'; 
			} else {
				if (count($map_product->variations) == 0) {
					$map_product->has_variations = 'off'; 
				} else {
					$map_product->has_variations = 'on'; 
				}
			}
		} 
		$map_product->options = $this->determine_product_options($map_product,$csv_product_id);
	}
	
	function initialize_prices($map_product) {
		if (count(unserialize($map_product->options)) > 0) {
			$combinations = array();
			$product_options = unserialize($map_product->options);
			foreach ($product_options as $option_group) {
				$sets = false;
				foreach ($option_group['options'] as $options) {
					$sets[]= $options['id'];
				}
				$groups[] = $sets;
			}		
			$this->_get_combos($groups,$combinations);
		}
		unset($row_data);
		$row_data = $this->get_importer_data($map_product);
		$row_type = (isset($groups))?"N/A":$this->defval($row_data->spi_type,"Shipped");
		$row_price = (isset($groups))?"0.00":$this->defval($row_data->spi_price,"0.00");


		$tc1 = array(
			"product"=>$map_product->id,
			"options"=>"",
			"optionkey"=>"0",
			"label"=>"Price & Delivery",
			"context"=>"product",
			"type"=>$row_type,
			"sku"=>(isset($groups))?"":$this->defval($row_data->spi_sku,""),
			"price"=>$this->parse_float($row_price),
			"saleprice"=>$this->parse_float((isset($groups))?"0.00":$this->defval($row_data->spi_saleprice,"0.00")),
			"weight"=>$this->parse_float((isset($groups))?"0.000":$this->defval($row_data->spi_weight,"0.000")),
			"shipfee"=>$this->parse_float((isset($groups))?"0.00":$this->defval($row_data->spi_shipfee,"0.00")),
			"stock"=>(isset($groups))?"0":$this->defval($row_data->spi_stock,"0"),
			"inventory"=>(isset($groups))?"off":$this->defval($row_data->spi_inventory,"off"),
			"sale"=>(isset($groups))?"off":$this->defval($row_data->spi_sale,"off"),
			"shipping"=>(isset($groups))?"on":$this->defval($row_data->spi_shipping,"on"),
			"tax"=>(isset($groups))?"on":$this->defval($row_data->spi_tax,"on"),
			"donation"=>$this->defval($row_data->spi_donation,'a:2:{s:3:"var";s:3:"off";s:3:"min";s:3:"off";}'),
			"sortorder"=>(isset($groups))?"0":$this->defval($row_data->spi_order,"0")
		);
		$this->products[$map_product->id]->prices[] = $tc1;	
		if (isset($combinations)) {			
			foreach ($combinations as $combo) {
				unset($row_data);
				$row_data = $this->get_importer_data($map_product,implode(',',$combo));		
				$row_type = $this->defval($row_data->spi_type,"Shipped");
				$row_price = $this->defval($row_data->spi_price,"0.00");
				if ($row_price == "0.00" || $row_price == "0" || strlen($row_price) == 0){
					$row_type = "N/A";	
					$row_price = "";
				}			
				
				$tc1 = array(
					"product"=>$map_product->id,
					"options"=>implode(',',$combo),
					"optionkey"=>$this->get_option_optionkey($map_product,$combo),
					"label"=>$this->get_option_label($map_product,$combo),
					"context"=>"variation",
					"type"=>$row_type,
					"sku"=>$this->defval($row_data->spi_sku,""),
					"price"=>$this->parse_float($row_price),
					"saleprice"=>$this->parse_float($this->defval($row_data->spi_saleprice,"0.00")),
					"weight"=>$this->parse_float($this->defval($row_data->spi_weight,"0.000")),
					"shipfee"=>$this->parse_float($this->defval($row_data->spi_shipfee,"0.00")),
					"stock"=>$this->defval($row_data->spi_stock,"0"),
					"inventory"=>$this->defval($row_data->spi_inventory,"off"),
					"sale"=>$this->defval($row_data->spi_sale,"off"),
					"shipping"=>$this->defval($row_data->spi_shipping,"on"),
					"tax"=>$this->defval($row_data->spi_tax,"on"),
					"donation"=>$this->defval($row_data->spi_donation,'a:2:{s:3:"var";s:3:"off";s:3:"min";s:3:"off";}'),
					"sortorder"=>$this->defval($row_data->spi_order,"0")					
				);
				$this->products[$map_product->id]->prices[] = $tc1;
			}
		}
		
	}
	
	// !bookmark : (End) Processing Functions	
	
	//Checks to see if a specific type of field exists in the shopp_product_importer data table
	//csv_product_id relates to $this->map[$id]['header'] eg. spi_saleprice, spi_tag1, spi_name
	//returns a count of those existing.
	function any_exist($header,$csv_product_id) {
		global $wpdb;
			$query = "SELECT COUNT(NULLIF(TRIM({$header}), '')) FROM {$wpdb->prefix}shopp_importer WHERE spi_id = '{$csv_product_id}';";
			$result = $wpdb->get_var($query);		
		return $result;
	}
	
	function any_images_exist() {
		global $wpdb;
		$result = 0;
		foreach ($this->map as $mset) {
			switch ($mset['type']) {
				case 'image':
					$query = "SELECT COUNT(NULLIF(TRIM({$mset['header']}), '')) FROM {$wpdb->prefix}shopp_importer;";
					$result = $result + $wpdb->get_var($query);
					break;
			}
		}	
		return $result;
	}	
	
	function category_by_uri($uri) {
		foreach ($this->categories as $category) {
			if ($category->uri == $uri) return $category;
		}
		return null;
	}	
	
	function category_exists($uri) {
		global $wpdb;
			$query = "SELECT * FROM {$wpdb->prefix}shopp_category WHERE uri = '{$uri}';";
			$result = $wpdb->get_row($query);		
		return $result;
	}	
	
	function defval($value,$default) {
		return strlen($value)>0?$value:$default;
	}
		
	function determine_product_options($map_product,$csv_product_id) {
		$options = array();
		$options_index = 1;
		$option_value_uid = 1;
		foreach ($this->variations as $variation) {
			if ($variation->csv_product_id == $csv_product_id) {
				$option_values = array();
				$option_value_index = 0;
				foreach ($variation->values as $val) {
					$option_values[$option_value_index] = array(
						"id"=>(string)$option_value_uid,
						"name"=>$val->value,
						"linked"=>"off"
					);
					$option_value_uid++;
					$option_value_index++;
				}
				$options[$options_index] = 
					array(
						"id"=>(string)$options_index,
						"name"=>ltrim($variation->name,"spi_"),
						"options"=>$option_values);
				$options_index++;
			}
		}
		return serialize($options);
	}	
	
	function find_variation($name, $csv_product_id) {
		foreach ($this->variations as $index=>$var) {
			if ($var->name == $name && $var->csv_product_id == $csv_product_id) {
				return $index;
			}
		}	
		return -1;
	}	
	
	function find_variation_value($valuearray,$value) {
		foreach ($valuearray as $index=>$var) {
			if ($var->value == $value) {
				return $index;
			}
		}	
		return -1;
	}		

	private function _get_combos(&$lists,&$result,$stack=array(),$pos=0)
	{
		$list = $lists[$pos];
	 	if(is_array($list)) {
	  		foreach($list as $word) {
	   			array_push($stack,$word);
	   			if(count($lists)==count($stack)) {
	   				$result[]=$stack;
	   			} else {
	   				if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
	   					$this->_get_combos($lists,$result,$stack,$pos+1);
	   				} else {
	   					$this->_get_combos(&$lists,&$result,$stack,$pos+1);
	   				}
	   			}			 
	   			array_pop($stack);
	  		}
	 	}
	}	
	
	function get_importer_data($map_product,$combostring = '') {
		global $wpdb;
		
		$empty_result = (object)null;
		$empty_result->spi_type = null;
		$empty_result->spi_price = null;
		$empty_result->spi_sku = null;
		$empty_result->spi_saleprice = null;
		$empty_result->spi_weight = null;
		$empty_result->spi_shipfee = null;
		$empty_result->spi_stock = null;
		$empty_result->spi_inventory = null;
		$empty_result->spi_sale = null;
		$empty_result->spi_shipping = null;
		$empty_result->spi_tax = null;
		$empty_result->spi_donation = null;
		$empty_result->spi_order = null;

		if (strlen($combostring) > 0) {
			$combo = explode(",",$combostring);
			$combo_index = 0;
			$string = "";
			foreach ($map_product->variations as $variation) {
				$option_id_label = $this->get_optionid_label($map_product,$combo[$combo_index]);
				if ($combo_index > 0) $and = " AND "; else $and = "";
				$string .= "{$and} {$variation->name} = '{$option_id_label}' ";
				$combo_index++;
			}		
			$query = "SELECT * FROM {$wpdb->prefix}shopp_importer WHERE {$string} AND spi_id = '{$map_product->csv_id}'";
		} else {
			$query = "SELECT * FROM {$wpdb->prefix}shopp_importer WHERE spi_id = '{$map_product->csv_id}'";
		}
		$result = $wpdb->get_row($query);
		$merged_result = (object) array_merge((array) $empty_result, (array) $result);
		return $merged_result;
	}	
	
	function get_mapped_var($id,$column_header) {
		global $wpdb;
		$query = "SELECT {$column_header} FROM {$wpdb->prefix}shopp_importer WHERE (spi_id = '{$id}') ORDER BY id limit 1";
		$result = $wpdb->get_var($query);
		return $result;	
	}	
	
	//get_next_product selects the next product from the shopp_product_importer data table
	//which meets the status code criteria.	
	function get_next_product($status,$as_set=false) {
		global $wpdb;
		$query = "SELECT * FROM {$wpdb->prefix}shopp_importer WHERE (processing_status = {$status}) ORDER BY id limit 1";
		if ($as_set) $result = $wpdb->get_results($query,OBJECT);
		else $result = $wpdb->get_row($query,OBJECT);
		return $result;
	}
	
	//get_next_set selects all products in the table with the id returned by get_next_product	
	function get_next_set($id) {
		global $wpdb;
		$id = trim($id);
		$query = "SELECT * FROM {$wpdb->prefix}shopp_importer WHERE spi_id = '{$id}' ORDER BY id ";
		$result = $wpdb->get_results($query,OBJECT);
		return $result;
	}	
	
	function get_next_shopp_product_id() {
		global $wpdb;
		$query = "SELECT id FROM {$wpdb->prefix}shopp_product ORDER BY id DESC limit 1";
		$result = $wpdb->get_var($query);
		if (!is_numeric($result)) $result = 1; else $result++;
		return $result;		
	}
	
	function get_next_shopp_tag_id() {
		global $wpdb;
		$query = "SELECT id FROM {$wpdb->prefix}shopp_tag ORDER BY id DESC limit 1";
		$result = $wpdb->get_var($query);
		if (!is_numeric($result)) $result = 1; else $result++;
		return $result;		
	}	
	
	function get_next_shopp_category_id() {
		global $wpdb;
		$query = "SELECT id FROM {$wpdb->prefix}shopp_category ORDER BY id DESC limit 1";
		$result = $wpdb->get_var($query);
		if (!is_numeric($result)) $result = 1; else $result++;
		return $result;		
	}		
	
	function get_option_label($map_product,$combo) {
		
		if (is_array(unserialize($map_product->options))) {
			$product_options = unserialize($map_product->options);
			$lbl_index = 0;
			$label = "";
			foreach ($product_options as $gkey=>$option_group) {
				foreach($option_group['options'] as $okey=>$option) {
					foreach ($combo as $check_value) {
						if ($option['id'] == $check_value) {
							if ($lbl_index > 0) $seperator = ', '; else $seperator = '';
							$label .= $seperator.$option['name'];
							$lbl_index++;
						}
					}
				}
			}	
		}
		return $label;			
	}	
	
	function get_option_optionkey($map_product,$ids,$deprecated = false) {
		if ($deprecated) $factor = 101;
		else $factor = 7001;
		if (empty($ids)) return 0;
		$key = null;
		foreach ($ids as $set => $id) 
			$key = $key ^ ($id*$factor);
		return $key;			
	}				
	
	function get_optionid_label($map_product, $check_value) {
		if (is_array(unserialize($map_product->options))) {
			$product_options = unserialize($map_product->options);
			foreach ($product_options as $gkey=>$option_group) {
				foreach($option_group['options'] as $okey=>$option) {
					if ($option['id'] == $check_value) {
						return $option['name'];
					}
				}
			}	
		}
	}	
	
	function get_row_mapped_var($id,$column_header) {
		global $wpdb;
		$query = "SELECT {$column_header} FROM {$wpdb->prefix}shopp_importer WHERE (id = '{$id}') ORDER BY id limit 1";
		$result = $wpdb->get_var($query);
		return $result;	
	}		
	
	function key_to_category_by_uri($uri) {
		foreach ($this->categories as $key=>$category) {
			if ($category->uri == $uri) return $key;
		}
		return null;
	}	
	
	function parse_float($floatString){ 
	    if (is_numeric($floatString)) return $floatString;
	    $LocaleInfo = localeconv(); 
	    $thousep = strlen($LocaleInfo["mon_thousands_sep"]>0)?$LocaleInfo["mon_thousands_sep"]:",";
	    $decplac = strlen($LocaleInfo["mon_decimal_point"]>0)?$LocaleInfo["mon_decimal_point"]:".";
	    $newfloatString = str_replace($thousep, "", $floatString); 
	    $newfloatString = str_replace($decplac, ".", $newfloatString);
	    return floatval(preg_replace('/[^0-9.]*/','',$newfloatString)); 
	} 		
	
	// !bookmark : function populate_images (to be employed later)
	
	/*function populate_images($product_set,&$images = array()) {
		$product_set_id = '';
		foreach ($product_set as $pmap) {	
			foreach ($this->map as $mset) {
				switch ($mset['type']) {
					case 'image':
						$img = $this->get_mapped_var($pmap->spi_id,$mset['header']);
						if (array_search($img, $images) === false) {
							$images[] = $this->get_mapped_var($pmap->spi_id,$mset['header']);
							$product_set_id = $pmap->product_id;	
						}	
						break;
				}
			}
		}	
		if (strlen($product_set_id) > 0) {
			$spi_images = new spi_images($this->spi);
				$process_count = $spi_images->import_product_images($product_set_id,$images);
			unset($spi_images);
		}
		return $process_count;
	}*/
	
	function _populate_image($product_set,$pmap,$mset) {
		$process_count = 0;
		$product_set_id = $pmap->product_id;
		$img = $this->get_mapped_var($pmap->spi_id,$mset['header']);
		if (strlen($product_set_id) > 0) {
			$spi_images = new spi_images($this->spi);
				$process_count = $spi_images->import_product_images($product_set_id,array($img));
			unset($spi_images);
		}
		return $process_count;
	}	
	
	function process_all($status) {	
		global $wpdb;
		$query = "UPDATE {$wpdb->prefix}shopp_importer SET processing_status = {$status};";
		$result = $wpdb->query($query);
		return $result;	
	}	
	
	function process_image($id,$column_header,$column_value,$status) {
		global $wpdb;		
		$query = "UPDATE {$wpdb->prefix}shopp_importer SET processing_status = {$status} WHERE spi_id  = '{$id}' AND {$column_header} = '{$column_value}'";
		$result = $wpdb->query($query);
		return $result;	
	}	
	
	function process_product($row_id,$status) {
		global $wpdb;		
		$query = "UPDATE {$wpdb->prefix}shopp_importer SET processing_status = {$status} WHERE id = '{$row_id}'";
		$result = $wpdb->query($query);
		return $result;	
	}			
	
	function process_set($id,$status,$shopp_product_id = null) {
		global $wpdb;
		$id = trim($id);
		if (!is_null($shopp_product_id)) $prod_id = ", product_id = '{$shopp_product_id}'"; else $prod_id = "";
		$query = "UPDATE {$wpdb->prefix}shopp_importer SET processing_status = {$status} {$prod_id} WHERE spi_id = '{$id}'";
		$result = $wpdb->query($query);
		return $result;	
	}		
	
	function product_by_csv_id($csv_id) {
		foreach ($this->products as $product) {
			if ($product->csv_id == $csv_id) return $product;
		}
		return null;
	}			
	
	function product_exists($name,$sku) {
		global $wpdb;
			$query = "SELECT pd.id FROM {$wpdb->prefix}shopp_product pd, {$wpdb->prefix}shopp_price pc WHERE (pd.id = pc.product) AND (pd.name='".addslashes($name)."' AND pc.sku='{$sku}' ) LIMIT 1;";
			$result = $wpdb->get_var($query);		
		return $result;
	}		
	
	function tag_exists($name,$id) {
		global $wpdb;
			$query = "SELECT * FROM {$wpdb->prefix}shopp_tag t,{$wpdb->prefix}shopp_catalog c WHERE (t.id = c.tag) AND (t.name = '{$name}' AND c.product = '{$id}');";
			$result = $wpdb->get_row($query);		
		return $result;
	}	
}

class map_product {
	var $id;
	var $shopp_id;
	var $categories = array();
	var $description;
	var $description_text;
	var $details = array();
	var $featured;
	var $images = array();
	var $inventory;
	var $name;
	var $options = array();
	var $prices = array();
	var $price;
	var $published;
	var $sale;
	var $sale_price;
	var $ship_fee;
	var $sku;	
	var $slug;					
	var $order;
	var $stock;
	var $summary;
	var $tags = array();
	var $tax;
	var $price_type;
	var $variations = array();
	var $weight;	
}

class map_category {
	var $name;
	var $value;
	var $exists;
	var $id;
	var $parent_id;
	var $slug;
	var $uri;
}

class map_tag {
	var $name;
	var $value;
	var $exists;
	var $id;	
}

class map_detail {
	var $name;
	var $value;
	var $id;
	var $parent;
}

class map_image {
	var $name;
	var $value;
	var $exists;
	var $id;	
}

class map_variation {
	var $name;	
	var $id;	
	var $shopp_product_id;
	var $csv_product_id;
	var $values;
}

class map_variation_value {
	var $key;
	var $value;
	var $index;
}

class map_variations {
	var $name;	
	var $value;
	var $option_id;
	var $exists;
	var $id;		
}
?>