/********************
// Get the data from the form and post it to wp-admin
********************/																																																																																																																																																																																																																																																																											

jQuery(document).ready(function(){
	
	//Get the value of the input "number of posts".
	jQuery('#fetch').on('click', function(){

		// In order to make the function dynamic, we make the site url a variable.
		var siteUrl = siteUrlobject.siteUrl+'/wp-admin/admin-ajax.php';

		// Method 1 :: Get the individual value of the input so we know how many posts to retrieve
		var noofposts = jQuery('#noofposts').val();	

		// Method 2 :: For larger amounts of information you can serialise the data into a single string which can be broken up at the other end. 
		//var data = jQuery('#the-name-of-a-form').serialise();				
		
		jQuery(function(){
		    jQuery.ajax({
	            url:siteUrl,
	            type:'POST',	

	            // Method 1 :: Send the individual value to our function            
	            data:'action=getposts&noofposts=' + noofposts,

	            //Method 2 :: Send the variable to our function
	            // data:'action=getposts&data=' + data 

	            //Now go and have a look at the PHP and we'll come back to the JS shortly!  

	            //Welcome back - When the PHP has finished, do this with it. 
	            success:function(data){
	            
	            	jQuery('#result').html(data);
	            
	            }
			});
		});

	})
})
