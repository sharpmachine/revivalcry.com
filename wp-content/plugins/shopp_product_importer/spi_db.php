<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<?php
class spi_db {
	
	function spi_db($spi) {
	}		
					
	function product_id_by_name_and_sku($name, $sku) {
		global $wpdb;
		$name = stripslashes($name);
		$sku = stripslashes($sku);
		if (strlen($name) > 0 && strlen($sku) > 0) {
			$query = "SELECT prod.`id` from `{$wpdb->prefix}shopp_product` prod INNER JOIN `{$wpdb->prefix}shopp_price` pri ON prod.`id` = pri.`product`  WHERE pri.`sku` = '{$sku}' AND prod.`name` = '{$name}'";
			$results = $wpdb->get_var($query);
		}
		return ($results);
	}

	function product_id_by_sku($sku) {
		global $wpdb;
		$results = "";
		$sku = stripslashes($sku);
		if (strlen($sku) > 0) {
			$query = "SELECT prod.`id` from `{$wpdb->prefix}shopp_product` prod INNER JOIN `{$wpdb->prefix}shopp_price` pri ON prod.`id` = pri.`product`  WHERE pri.`sku` = '{$sku}'";
			$results = $wpdb->get_var($query);
		}
		return ($results);
	}
	
	function product_exists_in_shopp($csv_row) {
		$shopp_product_id = false;
		$shopp_product_id = $this->product_id_by_name_and_sku($csv_row['name'],$csv_row['sku']);
		if (!strlen($shopp_product_id) > 0) {
			$shopp_product_id = $this->product_id_by_sku($csv_row['sku']);
		}
		return $shopp_product_id;		
	}		

	function price_id_by_product_priceline($product_id) {
		global $wpdb;
		$query = "SELECT `id` FROM `{$wpdb->prefix}shopp_price` WHERE `context` = 'product' AND `product` = '{$product_id}'";
		$results = $wpdb->get_var($query);
		$_SESSION["spi_error"] = $wpdb->last_error;
		return ($results);
	}		
	
	function image_exists_by_basename($basename) {
		global $wpdb;
		$query = "SELECT asset.`id` from `{$wpdb->prefix}shopp_asset` asset WHERE asset.`name` = '{$basename}'";
		$results = $wpdb->get_var($query);
		return ($results);
	}	
	
	function save_product ($Product) {
		global $wpdb;
		$db = DB::get();
		
		$data = $db->prepare($Product);
		$id = $Product->{$Product->_key};
		// Update record
		if (!empty($id)) {
			$check_exists_query = "SELECT `id` FROM {$wpdb->prefix}shopp_product WHERE `id` = '{$id}'";
			$check_exists_result = $wpdb->get_var($check_exists_query);
			if (strlen($check_exists_result) > 0) {
				if (isset($data['modified'])) $data['modified'] = "now()";
				$dataset = $Product->dataset($data);
				$db->query("UPDATE $Product->_table SET $dataset WHERE $Product->_key=$id");
				return $Product->id;
			} else {
				//Insert Record
				if (isset($data['created'])) $data['created'] = "now()";
				if (isset($data['modified'])) $data['modified'] = "now()";
				$dataset = $Product->dataset($data);
				//print "INSERT $Product->_table SET $dataset";
				$Product->id = $db->query("INSERT $Product->_table SET $dataset");	
				return $Product->id;			
			}
		// Insert new record
		} else {
			if (isset($data['created'])) $data['created'] = "now()";
			if (isset($data['modified'])) $data['modified'] = "now()";
			$dataset = $Product->dataset($data);
			//print "INSERT $Product->_table SET $dataset";
			$Product->id = $db->query("INSERT $Product->_table SET $dataset");
			return $Product->id;
		}
	}		
}
?>