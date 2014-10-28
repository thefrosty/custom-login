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
	
	// Allow tab key
	$('textarea[name="custom_login[custom_css]"]').on("keydown",function(e) {
		if ( e.keyCode == 9 ) {
			e.preventDefault();
			insertAtCursor(document.getElementById("custom_login[custom_css]"), "    ");
		}
	});
	
	// Allow tab key
	if ( $('div.img').length ) {
		$('div.img').each(function() {
			var $size = $(this).parent().width();
			$(this).css({
				'max-width': $size,
				'width'		: $size
			}).slideDown('fast');
		});
	}
	
	// Remote API helper
	$('#custom_login_extensions a[data-toggle]').on('click',function(e) {
		e.preventDefault();
		$('#' + $(this).data('toggle')).toggle();
	});
	
	// Show Purchase button
    $('a[data-edd-install]').each(function() {
		var $this = $(this);
		setTimeout( function() {
			if ( $this.prev('.eddri-status').text() === 'Not Installed' ) {
				$this.closest( $this.parent() ).children('a.button').hide();
				$this.closest( $this.parent() ).children('a.button.show-if-not-purchased').show();
			}
		}, 500 );
	});
    
});

/**
 * @ref		http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript#comment-3817
 */
function insertAtCursor(myField, myValue) {
	//IE support
	if (document.selection) {
		var temp;
		myField.focus();
		sel = document.selection.createRange();
		temp = sel.text.lenght;
		sel.text = myValue;
		if (myValue.length == 0) {
			sel.moveStart('character', myValue.length);
			sel.moveEnd('character', myValue.length);
		} else {
			sel.moveStart('character', -myValue.length + temp);
		}
		sel.select();
		}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
	} else {
		myField.value += myValue;
	}
}