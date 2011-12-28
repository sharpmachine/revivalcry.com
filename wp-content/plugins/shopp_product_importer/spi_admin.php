<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<?php
class spi_admin {
	
	function spi_admin($spi) {
		$this->basepath = $spi->basepath;
		$this->directory = $spi->directory;
	}		
					
	function set_help() {
		ob_start();
		include($this->basepath.'/'.$this->directory."/help.php");
		$help = ob_get_contents();
		ob_end_clean();	
		return $help;		
	} 
}
?>