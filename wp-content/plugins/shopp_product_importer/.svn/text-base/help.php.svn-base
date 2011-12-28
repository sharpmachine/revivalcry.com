<?php 
/**
  	Copyright: Copyright © 2010 Catskin Studio
	Licence: see index.php for full licence details
 */
?>
<div class="wrap">
	<h2><?php _e('Importer Help','Shopp'); ?></h2>
	
	<ul id="help">
		<li>
			<h3><?php _e('What to do','Shopp'); ?></h3>
			<p><?php _e("Upload the CSV file, When the file is fully uploaded, which could take some time, enter the name of the file in the CSV filename field (not the path, just the file name eg. import.csv). Click 'Update Importer Settings'. If the file upload worked, you'll see a table appear providing you with the ability to map your CSV columns to the Shopp field types. Once the columns are mapped click 'Update Import Settings' again to save the column mappings. When ready click run importer to import the CSV file.",'Shopp'); ?></p>
			<h3><?php _e('How to format the CSV file','Shopp'); ?></h3>
			<p><?php _e('At this stage the import routine copes with all data except for product specs.','Shopp'); ?></p>
			<p><?php _e('Some fields have specific formatting requirements as follows:','Shopp'); ?></p>
			<ul>
				<li>
					<h4><?php _e('Product Identifier','Shopp'); ?></h4>
					<p>
					<?php _e('One column must be a Product Identifier for the import routine to work.','Shopp'); ?><br/>
					<?php _e("The Product Identifier is a unique ID that relates to a product. This field doesn't actually get imported but it helps the importer understand multi-variation products that span multiple rows of the CSV file. It can be any unique alpha-numeric code. ",'Shopp'); ?>
					</p>
				</li>
				<li>
					<h4><?php _e('Category','Shopp'); ?></h4>
					<p>
					<?php _e('Multiple columns can be specified as Category columns','Shopp'); ?><br/>
					<?php _e("A category is slash delimited to create the sub levels eg. Electronics/Cameras/Expansion Memory would create the electronics category with a sub category of Cameras which in turn has a sub category of Expansion Memory. To place a product in multiple sub branches simply use multiple columns in the CSV file to denote the categories in which products should be included.",'Shopp'); ?>
					</p>
				</li>		
				<li>
					<h4><?php _e('Price Variations','Shopp'); ?></h4>
					<p>
					<?php _e('Multiple columns can be specified as Price Variations','Shopp'); ?><br/>
					<?php _e("eg. Size & Colour variations would go into their own column. For products that don't require these variations, leave their cells blank and no option will be created for that variation. All rows with the same Product Identifier should share the same variation options. A name must be provided for each variation regardless of whether column headers are used or not.",'Shopp'); ?>
					</p>
				</li>	
				<li>
					<h4><?php _e('Description','Shopp'); ?></h4>
					<p>
					<?php _e('At this stage the descriptions are picked up from external html files.','Shopp'); ?><br/>
					<?php _e("Each row of a single Product Identifier should also share a description HTML file as only one ends up being used for the product",'Shopp'); ?><br/>
					<?php _e("Descriptions must be uploaded seperately using FTP to the folder specified in the HTML upload path.",'Shopp'); ?><br/>
					</p>
				</li>
				<li>
					<h4><?php _e('Images','Shopp'); ?></h4>
					<p>
					<?php _e('Multiple Image columns can be provided.','Shopp'); ?><br/>
					<?php _e("Image columns should be specified as url's to external image files. eg. http://www.google.com.au/intl/en_au/images/logo.gif",'Shopp'); ?>
					</p>
				</li>	
				<li>
					<h4><?php _e('Tags','Shopp'); ?></h4>
					<p>
					<?php _e('Multiple Tag columns can be provided.','Shopp'); ?><br/>
					</p>
				</li>	
				<li>
					<h4><?php _e('Sample CSV file - 1','Shopp'); ?></h4>
					<table cellpadding="10" cellspacing="10">
						<tr>
							<th>Product Identifier</th><th>SKU</th><th>Price</th><th>Product Name</th><th>Size Variation</th><th>Colour Variation</th><th>Category</th><th>Image</th><th>Description</th><th>Tag 1</th><th>Tag 2</th>
						</tr>						
						<tr>
							<td>1</td><td>ABC-123BS</td><td>12.50</td><td>T-Shirt</td><td>Small</td><td>Black</td><td>Mens Wear/Summer/T-Shirts</td><td>http://theimageiamusing.com.au/image1.jpg</td><td>product1.html</td><td>Mens Clothes</td><td>Men's T-Shirts</td>
						</tr>
						<tr>
							<td>1</td><td>ABC-123BM</td><td>12.50</td><td>T-Shirt</td><td>Medium</td><td>Black</td><td>Mens Wear/Summer/T-Shirts</td><td></td><td>product1.html</td><td>Mens Clothes</td><td>Men's T-Shirts</td>
						</tr>	
						<tr>
							<td>1</td><td>ABC-123RS</td><td>12.50</td><td>T-Shirt</td><td>Small</td><td>Red</td><td>Mens Wear/Summer/T-Shirts</td><td>http://theimageiamusing.com.au/image3.jpg</td><td>product1.html</td><td>Mens Clothes</td><td>Men's T-Shirts</td>
						</tr>											
					</table>
					<p>This CSV would result in a single product with a black and red t-shirt image, and a variation matrix of two colours and two sizes. The Red/Medium T-Shirt would be disabled as it doesn't appear in the CSV.</p>		
					<h4><?php _e('Sample CSV file - 2','Shopp'); ?></h4>
					<table cellpadding="10" cellspacing="10">
						<tr>
							<th>Product Identifier</th><th>SKU</th><th>Price</th><th>Product Name</th><th>Size Variation</th><th>Colour Variation</th><th>Category</th><th>Image</th><th>Description</th><th>Tag 1</th><th>Tag 2</th>
						</tr>						
						<tr>
							<td>1</td><td>ABC-123BS</td><td>12.50</td><td>T-Shirt Logo 1</td><td>Small</td><td>Black</td><td>Mens Wear/Summer/T-Shirts</td><td>http://theimageiamusing.com.au/image1.jpg</td><td>product1.html</td><td>Mens Clothes</td><td>Men's T-Shirts</td>
						</tr>
						<tr>
							<td>1</td><td>ABC-123RS</td><td>12.50</td><td>T-Shirt Logo 1</td><td>Small</td><td>Red</td><td>Mens Wear/Summer/T-Shirts</td><td>http://theimageiamusing.com.au/image2.jpg</td><td>product1.html</td><td>Mens Clothes</td><td>Men's T-Shirts</td>
						</tr>	
						<tr>
							<td>2</td><td>ABC-456BS</td><td>15.50</td><td>T-Shirt Logo 2</td><td>Small</td><td>Black</td><td>Mens Wear/Summer/T-Shirts</td><td>http://theimageiamusing.com.au/image3.jpg</td><td>product2.html</td><td>Mens Clothes</td><td>Men's T-Shirts</td>
						</tr>											
					</table>	
					<p>This CSV would result in two products, one with a black and red t-shirt image, and a variation matrix of two colours and one size, the other with a single black image and a variation matrix of one colour and one size.</p>		
												
				</li>																					
			</ul>
		</li>
		<li><h2>YOUR AGREEMENT:</h2><h3>BACKUP!. ONLY USE IN TEST ENVIRONMENT! THIS PLUGIN IS NOT COMMERCIAL & IS PROVIDED FOR YOUR CONVENIENCE! CATSKIN STUDIO WILL NOT BE HELD RESPONSIBLE FOR LOSS CAUSED BY THE USE OF THIS PLUGIN.</h3><h4>This plugin is not well tested and it would be great to get some feedback so that we can rectify any issues. If you want help contact info at catskinstudio.com</h4><h4>If you don't agree, don't use it. :)</h4></li>
	</ul>
	
</div>