jQuery(document).ready(function($) {
	
	var Select = $('select[name="custom_login[logo_background_size]"]');
	var Custom = $('input[name="custom_login[logo_background_size_custom]"]');
	
	if ( 'custom' !== Select.val() ) {
		$(Custom).parent().parent().hide('slow');
	}
	
	$(Select).chosen().on('change',function() {
		var Val = $(this).val();
//		console.log(Val);
		
		if ( 'custom' === Val ) {
			$(Custom).parent().parent().show('fast');
		}
		else {
			$(Custom).parent().parent().hide('slow');
		}
		
	});
	
	// Remote API
	$('#custom_login_extensions a[data-toggle]').on('click',function(e) {
		e.preventDefault();
		$('#' + $(this).data('toggle')).toggle();
	});
    
});