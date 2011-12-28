<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<?php
require_once("spi_db.php");
class spi_data {
	
	function spi_data($spi) {

	}
	
	function map_product_ids() {
		$spi_db = new spi_db($this);
		
		if (isset($_SESSION['spi_product_importer_data'])) $data = $_SESSION['spi_product_importer_data'];
		if (isset($_SESSION['spi_mapped_product_ids'])) $mapped_product_ids = $_SESSION['spi_mapped_product_ids'];
		
		foreach ($data as $row) {
			$this_sku = stripslashes($row['sku']);
			if (strlen($this_sku) > 0) {
				$id = $spi_db->product_id_by_sku($this_sku);
				if (strlen($id) > 0) $mapped_product_ids[$row['id']] = $id;
			}
		}
		unset($spi_db);
	}	
}
?>