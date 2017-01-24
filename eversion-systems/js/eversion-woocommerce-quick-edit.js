(function($) {
	//https://github.com/mladjom/woocommerce-product-samples/blob/master/admin/bulk_quick_edit.js
	
	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;
	
	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
	
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );
		
		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' )
			$post_id = parseInt( this.getId( id ) );
			
		if ( $post_id > 0 ) {
            // define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			
			// get the film rating
			//var $member_price = $( '#member_price-' + $post_id ).text();
			var $member_price = $( '#member_price-' + $post_id ).data('price');
			// set the member price
			$edit_row.find( 'input[name="member_price"]' ).val( $member_price );	
		}
	};
})(jQuery);