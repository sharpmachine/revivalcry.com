<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<?php
require_once('Image.php');
require_once('spi_db.php');
class spi_images {
	
	function spi_images($spi) {
		$this->spi = $spi;
		$this->image_put_path = $spi->image_put_path;
		$this->image_map = $spi->image_map;
	}
	
	function import_images($Product,$row) {
		if (is_array($this->image_map)) {
		foreach ($this->image_map as $key ){
			$images[] = $row[$key];
		}	
		$spi_db = new spi_db($this->spi);
		foreach ($images as $img) {
			$pathinfo = pathinfo($img);
			if (strtoupper($pathinfo['extension']) == 'JPG' || strtoupper($pathinfo['extension']) == 'JPEG'  || strtoupper($pathinfo['extension']) == 'GIF') {
				if ($this->image_exists($Product->id, $pathinfo['basename']) != $Product->id) $this->load_image_from_url($Product,$img);
			}
			
		}	
		unset($spi_db);
		}
		return $images;
	}
	
	function import_product_images($product_id,$images) {
		$spi_db = new spi_db($this->spi);
		$process_count = 0;
		foreach ($images as $img) {
			$pathinfo = pathinfo($img);
			if (strtoupper($pathinfo['extension']) == 'JPG' || strtoupper($pathinfo['extension']) == 'JPEG'  || strtoupper($pathinfo['extension']) == 'GIF') {
				if ($this->image_exists($product_id, $pathinfo['basename']) != $product_id) {
					$this->_load_image_from_url($product_id,$img);
					$process_count++;
				}
			}
			
		}	
		unset($spi_db);
		return $process_count;
	}	
	
	function _load_image_from_url($product_id,$img) {
		global $Shopp;
		
		$QualityValue = array(100,92,80,70,60);
		$info = pathinfo($img);
		if (!$this->image_exists($product_id,$info['basename'])) {
			// Generate Small Size
			$SmallSettings = array();
			$SmallSettings['width'] = $Shopp->Settings->get('gallery_small_width');
			$SmallSettings['height'] = $Shopp->Settings->get('gallery_small_height');
			$SmallSettings['sizing'] = $Shopp->Settings->get('gallery_small_sizing');
			$SmallSettings['quality'] = $Shopp->Settings->get('gallery_small_quality');			

			// Generate Thumbnail
			$ThumbnailSettings = array();
			$ThumbnailSettings['width'] = $Shopp->Settings->get('gallery_thumbnail_width');
			$ThumbnailSettings['height'] = $Shopp->Settings->get('gallery_thumbnail_height');
			$ThumbnailSettings['sizing'] = $Shopp->Settings->get('gallery_thumbnail_sizing');
			$ThumbnailSettings['quality'] = $Shopp->Settings->get('gallery_thumbnail_quality');
			
			$parent = $product_id;
			$data = file_get_contents($img);
			$Image = new ProductImage();
			$Image->parent = $parent;
			$Image->type = "image";
			$Image->name = "original";
			$Image->filename = $info['basename'];
			list($Image->width, $Image->height, $Image->mime, $Image->attr) = getimagesize($img);
			$Image->mime = image_type_to_mime_type($Image->mime);
			$Image->size = filesize($img);
			if (!file_exists($this->image_put_path.$Image->name)) {
				$_SESSION["spi_message"] = "Downloading image: ".$this->image_put_path.$Image->name;
				$Image->data = $data;
			} else unset($Image->data);	
			$Image->store($data);
			$Image->save();
			unset($Image->data); // Save memory for small image & thumbnail processing
			
			/*
			if (SHOPP_VERSION >= '1.1') $Image = new FileAsset(); else $Image = new Asset();
			$Image->parent = $parent;
			$Image->context = $context;
			$Image->datatype = "image";
			$Image->name = $info['basename'];
			list($width, $height, $mimetype, $attr) = getimagesize($img);
			$Image->properties = array(
				"width" => $width,
				"height" => $height,
				"mimetype" => image_type_to_mime_type($mimetype),
				"attr" => $attr);

			if (!file_exists($this->image_put_path.$Image->name)) {
				$_SESSION["spi_message"] = "Downloading image: ".$this->image_put_path.$Image->name;
				$Image->data = addslashes(file_get_contents($img));
			} else unset($Image->data);	
			$Image->save();
			unset($Image->data); // Save memory for small image & thumbnail processing
				
			if (SHOPP_VERSION >= '1.1') $Small = new FileAsset(); else $Small = new Asset();
			$Small->parent = $Image->parent;
			$Small->context = $context;
			$Small->datatype = "small";
			$Small->src = $Image->id;
			$Small->name = "small_".$Image->name;
			if (!file_exists($this->image_put_path.$Small->name)) {
				$Small->data = file_get_contents($img);
				$SmallSizing = new ImageProcessor2($Small->data,$width,$height);	
				switch ($SmallSettings['sizing']) {
						case "0": $SmallSizing->scaleToFit($SmallSettings['width'],$SmallSettings['height']); break;
						case "1": $SmallSizing->scaleCrop($SmallSettings['width'],$SmallSettings['height']); break;
				}
				$SmallSizing->UnsharpMask(75);
				$Small->data = addslashes($SmallSizing->imagefile($QualityValue[$SmallSettings['quality']]));			
			} else unset($Small->data);		

			$Small->properties = array();
			$Small->properties['width'] = $SmallSizing->Processed->width;
			$Small->properties['height'] = $SmallSizing->Processed->height;
			$Small->properties['mimetype'] = "image/jpeg";
			unset($SmallSizing);
			$Small->save();
			unset($Small);
			

			if (SHOPP_VERSION >= '1.1') $Thumbnail = new FileAsset(); else $Thumbnail = new Asset();
			$Thumbnail->parent = $Image->parent;
			$Thumbnail->context = $context;
			$Thumbnail->datatype = "thumbnail";
			$Thumbnail->src = $Image->id;
			$Thumbnail->name = "thumbnail_".$Image->name;
			if (!file_exists($this->image_put_path.$Thumbnail->name)) {
				$Thumbnail->data = file_get_contents($img);
				$ThumbnailSizing = new ImageProcessor2($Thumbnail->data,$width,$height);
				switch ($ThumbnailSettings['sizing']) {
					case "0": $ThumbnailSizing->scaleToFit($ThumbnailSettings['width'],$ThumbnailSettings['height']); break;
					case "1": $ThumbnailSizing->scaleCrop($ThumbnailSettings['width'],$ThumbnailSettings['height']); break;
				}
				$ThumbnailSizing->UnsharpMask();
				$Thumbnail->data = addslashes($ThumbnailSizing->imagefile($QualityValue[$ThumbnailSettings['quality']]));				
			} else unset($Thumbnail->data);		
			$Thumbnail->properties = array();
			$Thumbnail->properties['width'] = $ThumbnailSizing->Processed->width;
			$Thumbnail->properties['height'] = $ThumbnailSizing->Processed->height;
			$Thumbnail->properties['mimetype'] = "image/jpeg";
			unset($ThumbnailSizing);
			$Thumbnail->save();
			unset($Thumbnail->data);
			*/
		}
	}	
	
	function load_image_from_url($Product,$img) {
		global $Shopp;
		$QualityValue = array(100,92,80,70,60);
		$info = pathinfo($img);
		if (!$this->image_exists($Product->id,$info['basename'])) {
			
			// Generate Small Size
			$SmallSettings = array();
			$SmallSettings['width'] = $Shopp->Settings->get('gallery_small_width');
			$SmallSettings['height'] = $Shopp->Settings->get('gallery_small_height');
			$SmallSettings['sizing'] = $Shopp->Settings->get('gallery_small_sizing');
			$SmallSettings['quality'] = $Shopp->Settings->get('gallery_small_quality');			

			// Generate Thumbnail
			$ThumbnailSettings = array();
			$ThumbnailSettings['width'] = $Shopp->Settings->get('gallery_thumbnail_width');
			$ThumbnailSettings['height'] = $Shopp->Settings->get('gallery_thumbnail_height');
			$ThumbnailSettings['sizing'] = $Shopp->Settings->get('gallery_thumbnail_sizing');
			$ThumbnailSettings['quality'] = $Shopp->Settings->get('gallery_thumbnail_quality');
			
			$parent = $Product->id;
			$context = "product";
			if (SHOPP_VERSION >= '1.1') $Image = new FileAsset(); else $Image = new Asset();
			$Image->parent = $parent;
			$Image->context = $context;
			$Image->datatype = "image";
			$Image->name = $info['basename'];
			list($width, $height, $mimetype, $attr) = getimagesize($img);
			$Image->properties = array(
				"width" => $width,
				"height" => $height,
				"mimetype" => image_type_to_mime_type($mimetype),
				"attr" => $attr);

			
			if (!file_exists($this->image_put_path.$Image->name)) {
				$_SESSION["spi_message"] = "Downloading image: ".$this->image_put_path.$Image->name;
				$Image->data = addslashes(file_get_contents($img));
			} else unset($Image->data);	
			$Image->save();
			unset($Image->data); // Save memory for small image & thumbnail processing
				
			if (SHOPP_VERSION >= '1.1') $Small = new FileAsset(); else $Small = new Asset();
			$Small->parent = $Image->parent;
			$Small->context = $context;
			$Small->datatype = "small";
			$Small->src = $Image->id;
			$Small->name = "small_".$Image->name;
			if (!file_exists($this->image_put_path.$Small->name)) {
				$Small->data = file_get_contents($img);
				$SmallSizing = new ImageProcessor2($Small->data,$width,$height);	
				switch ($SmallSettings['sizing']) {
						case "0": $SmallSizing->scaleToFit($SmallSettings['width'],$SmallSettings['height']); break;
						case "1": $SmallSizing->scaleCrop($SmallSettings['width'],$SmallSettings['height']); break;
				}
				$SmallSizing->UnsharpMask(75);
				$Small->data = addslashes($SmallSizing->imagefile($QualityValue[$SmallSettings['quality']]));			
			} else unset($Small->data);		

			$Small->properties = array();
			$Small->properties['width'] = $SmallSizing->Processed->width;
			$Small->properties['height'] = $SmallSizing->Processed->height;
			$Small->properties['mimetype'] = "image/jpeg";
			unset($SmallSizing);
			$Small->save();
			unset($Small);
			

			if (SHOPP_VERSION >= '1.1') $Thumbnail = new FileAsset(); else $Thumbnail = new Asset();
			$Thumbnail->parent = $Image->parent;
			$Thumbnail->context = $context;
			$Thumbnail->datatype = "thumbnail";
			$Thumbnail->src = $Image->id;
			$Thumbnail->name = "thumbnail_".$Image->name;
			if (!file_exists($this->image_put_path.$Thumbnail->name)) {
				$Thumbnail->data = file_get_contents($img);
				$ThumbnailSizing = new ImageProcessor2($Thumbnail->data,$width,$height);
				switch ($ThumbnailSettings['sizing']) {
					case "0": $ThumbnailSizing->scaleToFit($ThumbnailSettings['width'],$ThumbnailSettings['height']); break;
					case "1": $ThumbnailSizing->scaleCrop($ThumbnailSettings['width'],$ThumbnailSettings['height']); break;
				}
				$ThumbnailSizing->UnsharpMask();
				$Thumbnail->data = addslashes($ThumbnailSizing->imagefile($QualityValue[$ThumbnailSettings['quality']]));				
			} else unset($Thumbnail->data);		
			$Thumbnail->properties = array();
			$Thumbnail->properties['width'] = $ThumbnailSizing->Processed->width;
			$Thumbnail->properties['height'] = $ThumbnailSizing->Processed->height;
			$Thumbnail->properties['mimetype'] = "image/jpeg";
			unset($ThumbnailSizing);
			$Thumbnail->save();
			unset($Thumbnail->data);
		}
	}	
		
	function image_exists($parent, $name) {
		global $wpdb;
		if (SHOPP_VERSION < '1.1') {
			$query = "SELECT `id` from `{$wpdb->prefix}shopp_asset` WHERE `context` = 'product' AND `name` = '{$name}' AND `parent` = '{$parent}'";
		} else {
			$query = "SELECT `id` from `{$wpdb->prefix}shopp_meta` WHERE `context` = 'product' AND `name` = '{$name}' AND `parent` = '{$parent}'";
		}		
		$results = $wpdb->get_var($query);
		$_SESSION["spi_error"] = $wpdb->last_error;
		return ($results);
	}		
}
?>