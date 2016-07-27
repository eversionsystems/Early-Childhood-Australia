(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	$(function() {
		$(".quantity_select select").change(function() {
			var selectedValue = $( this ).val();
			
			setSelectedPrice(selectedValue);
		});
		
		//Default the price on load
		var price = '';
		var selectedValue
		
		if ( bulk_prices.selected_quantity == 0 )
			selectedValue = $("[name='quantity']").val();
		else
			selectedValue = bulk_prices.selected_quantity;
		
		if(bulk_prices.product_slug == 'code-of-ethics-brochure')
			setSelectedPrice(selectedValue);
		
		function setSelectedPrice(selectedValue) {
			var price = '';
			
			for (var i = 0; i< bulk_prices.prices.length; i++) {
				
				if(bulk_prices.prices[i][0] == selectedValue) {
					if( bulk_prices.is_member )
						price = '<ins style="display: block;color:#000"><span class="woocommerce-Price-amount amount">' + accounting.formatMoney(bulk_prices.prices[i][2]) + '</span></ins>' +
								'<ins style="display: block;color:#77A464"><span class="woocommerce-Price-amount amount">' + accounting.formatMoney(bulk_prices.prices[i][1]) + '</span> Member Price <i class="fa fa-check"></i></ins>';
					else
						price = '<ins style="display: block;color:#000"><span class="woocommerce-Price-amount amount">' +  accounting.formatMoney(bulk_prices.prices[i][2]) + '</span><i class="fa fa-check"></i></ins>' +
								'<ins style="display: block;color:#77A464"><span class="woocommerce-Price-amount amount">' + accounting.formatMoney(bulk_prices.prices[i][1]) + '</span> Member Price</ins>';;
				}
			}
			
			//$( ".summary .price" ).text(accounting.formatMoney(price));
			$( ".summary .price" ).html(price);
		}
		
		$('#eca-table-discounts-heading').click(function(event){
			event.preventDefault();
			if($(this).hasClass('expanded')){
				$('.eca-table-discounts-content').slideToggle();
				$('#eca-table-discounts-heading a').html('<i class="fa fa-expand" aria-hidden="true"></i> Show quantity discounts');
				$(this).removeClass('expanded');
			}
			else {
				$('.eca-table-discounts-content').slideToggle();
				$('#eca-table-discounts-heading a').html('<i class="fa fa-expand" aria-hidden="true"></i> Hide quantity discounts');
				$(this).addClass('expanded');
			}
		});
		
	});

})( jQuery );
