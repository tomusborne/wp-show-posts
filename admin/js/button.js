function wpsp_get_post_lists() {
    var response = jQuery.getJSON({
		type: 'POST',
		url: ajaxurl,
		data: {
			action: 'wpsp_get_post_lists',
			wpsp_nonce: wpsp_nonce
		},
		async: false,
		dataType: 'json'
	});
				
	return response.responseJSON;
}

console.log(wpsp_get_post_lists());

(function() {
    tinymce.PluginManager.add('wpsp_shortcode_button', function( editor, url ) {
        editor.addButton( 'wpsp_shortcode_button', {
			title: wpsp_add_posts,
            icon: 'wpsp-add-icon',
			onclick: function() {
				editor.windowManager.open( {
					width: 300,
					height: 75,
					title: wpsp_add_posts,
					body: [
					{
						type: 'listbox', 
						name: 'wpsp_add_posts', 
						label: false, 
						'values': wpsp_get_post_lists(),
					}],
					onsubmit: function( e ) {
						editor.insertContent( '[wp_show_posts id="' + e.data.wpsp_add_posts + '"]');
					}
				});
			}
        });
    });
})();