/********************
 // Get the data from the form and post it to wp-admin
 ********************/

jQuery(document).ready(function () {

	//Get the value of the input "number of posts".
	jQuery('#fetch').on('click', function () {

		// In order to make the function dynamic, we make the site url a variable.
		// @see knoppy_ajax_add_ajax_library()
		//var siteUrl = siteUrlobject.siteUrl + '/wp-admin/admin-ajax.php';

		// Method 1 :: Get the individual value of the input so we know how many posts to retrieve
		var noofposts = jQuery('#noofposts').val();

		// Method 2 :: For larger amounts of information you can serialise the data into a single string which can be broken up at the other end. 
		//var data = jQuery('#the-name-of-a-form').serialise();				

		jQuery(function () {
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',

				// Method 2 :: Send the individual value to our function
				data: 'action=getposts&noofposts=' + noofposts,

				//Method 3 :: Send the variable to our function
				// data:'action=getposts&data=' + data

				//Now go and have a look at the PHP and we'll come back to the JS shortly!

				//Welcome back - When the PHP has finished, do this with it.
				success: function (data) {

					//console.log(data);

					// Empty last responce or you could just keep appending more in a different situation like load more setup
					jQuery('#result').html('');

					// Parse out returned jSON
					var responce = jQuery.parseJSON(data);
					var template = '';

					// Loop through the data
					jQuery.each(responce, function (key, item) {

						// These are our objects
						//console.log(item.title);
						//console.log(item.excerpt);

						// Templating this way gives your way more options, eg. Coffie, Angular, etc.. templating
						template = '<table><tr><td>' + item.title + '</td></tr><tr><td>' + item.excerpt + '</td></tr></table>';

						// Display our results
						jQuery(template).appendTo('#result');
					});

				}
			});
		});

	})
})
