/* Author: 

*/
/* Tooltipsy - for top menu */
 jQuery('ul#menu-top-nagivation li a').tooltipsy();
 
 /* scrollTo - for newsletter signup */
 jQuery(document).ready(function(){
	 jQuery('ul#menu-top-nagivation li.newsletter-signup a').click(function(e){
		 jQuery.scrollTo( this.hash || 0, 500);
		e.preventDefault();
		jQuery('#footer input.email').focus();
	});
});