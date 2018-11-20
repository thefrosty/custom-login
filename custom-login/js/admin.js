(function($) {
	
	"use strict";
	
	/**
	 * Match the header with the WP-admin user color selection.
	 * Load before the document is ready.
	 */
	$('.cl-header').css('background-color', $('#adminmenuwrap').css('background-color'));
	
	$(document).ready(function() {
		
		/**
		 * Global vars
		 *
		 * @return string
		 */
		var $this = null,
			activetab,
			clicked_group,
			loaded = [],
			form;
			
		/**
		 *** Header *************************
		 ************************************
		 ************************************
		 */
		$('.cl-header').contrastColor();
			
		/**
		 *** Active *************************
		 ************************************
		 ************************************
		 */
		$('span.tgl_input').replaceWith( $('input[id="custom_login_general[active]"]').clone() );
		
		$(document).on('change', 'input[id="custom_login_general[active]"]', function() {
			$('input[id="custom_login_general[active]"]').prop('checked', this.checked);
      $.ajax({
          type: "POST",
          data: {
            action: cl_settings_api.prefix + '_activate_check',
            nonce: cl_settings_api.nonce,
            active_value: this.checked.toString()
          },
          dataType: "json",
          url: ajaxurl
        })
        .done(function (response) {
          if (response.success) {
          } else {
            
          }
        })
        .fail(function () {
          throw new Error('Error');
        });
		});
			
		/**
		 *** Sidebar Nav + Main Group *******
		 ************************************
		 ************************************
		 */
		//localStorage.setItem('activetab', '');
		$('.cl-main .group').hide();
		if (typeof(localStorage) != 'undefined' ) {
			activetab = localStorage.getItem('activetab');
		}
		if (activetab != '' && $(activetab).length ) {
			$(activetab).fadeIn();
			$('input[id="cl_save"]').val( 'Save ' + $('a[href="' + activetab + '"]').text() );
		}
		else {
			var first_group = $('.cl-main .group:first');
			first_group.fadeIn();
			$('input[id="cl_save"]').val( 'Save ' + $('.cl-sidebar .cl-sections-menu li:first a').text() );
		}

		if (activetab != '' && $('.cl-sections-menu a[href="' + activetab + '"]').length ) {
			$('.cl-sections-menu a[href="' + activetab + '"]').addClass('active');
		}
		else {
			$('.cl-sections-menu a:first').addClass('active');
		}
		// on.click event
		$('.cl-sections-menu a').on('click',function(e) {
			$this = $(this);
			clicked_group = $this.attr('href');
			
			$('input[id="cl_save"]').val( 'Save ' + $('a[href="' + clicked_group + '"]').text() );
			
			$('.cl-sections-menu a').removeClass('active');
			$this.addClass('active').blur();
			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem('activetab', clicked_group);
			}
			
			$('[class^="chosen-"]').each(function(index, element) {
				if ( $(this).css('width') == '0px' ) $(this).css('width','220px');
			});
			
			$('.cl-main .group').hide();
			$(clicked_group).fadeIn();
			e.preventDefault();
		});
				
		/**
		 *** Sticky *************************
		 ************************************
		 ************************************
		 */
		var $sticky = $('#cl-sticky').sticky({
			topSpacing		: $('#wpadminbar').length ? $('#wpadminbar').height() : 0,
			getWidthFrom	: $('.cl-container')
		});
		$(window).scroll(function() {
			if ( $(window).scrollTop() + $(window).height() > $(document).height() - 200) {
				$sticky.hide();
			} else {
				$sticky.show();
			}
		});
	
		/**
		 *** Form Submit ********************
		 ************************************
		 ************************************
		 */			
		$(document.body).on('click', 'input[name="cl_save"]', function(e) {
			$this = $(this);
			form  = $('.cl-main > div.group:visible > form');
			$this.attr('form',form.attr('id'));
			form.submit();
		}); //*/
	
		/**
		 *** callback_html ******************
		 ************************************
		 ************************************
		 *
		$.each( $('div[class^="section-custom_login"]'), function( index, value ) {
			$(value).parents('tr').find('th').prop('colspan','2').append('<hr>');
			$(value).parent('td').remove();
		}); //*/
		
		/**
		 *** callback_raw *******************
		 ************************************
		 ************************************
		 */
		$.each( $('div[class="raw-html"]'), function( index, value ) {
			$(value).parents('tr').find('td').prop('colspan','2');
			$(value).parents('tr').find('th').remove();
		});
		
		/**
		 *** callback_text_array ************
		 ************************************
		 ************************************
		 */
		$('body').on('click', 'a[class^="button docopy-"]', function(e) {
			e.preventDefault();
			
			$this = $(this).prev().children();
			var clone = $('input[id="' + $this.children().prop('id') + '"]');
			var value = clone.data('key');
			var newValue = parseInt(value) + 1;

			var newInput = $this.last().clone();
			newInput.insertAfter( clone.parent().last() );
			newInput.children().val('').data('key',newValue);
			return false;
		});		
		$('body').on('click', 'a[class^="button dodelete-"]', function(e) {
			e.preventDefault();
			
			$(this).parent().remove();
		});
		
		/**
		 *** callback_colorpicker ***********
		 ************************************
		 ************************************
		 */
		if ( cl_settings_api.callback_colorpicker ) {
			$.each( cl_settings_api.callback_colorpicker, function( index, value ) {
				$this = value.section + '[' + value.id;
				$('input[name="' + $this + ']"]').wpColorPicker();
				
				if ( $('select[name="' + $this + '_opacity]"]').hasClass('hidden') ) {
					$('select[name="' + $this + '_opacity]"]').removeClass('hidden').chosen().addClass('hidden');
				} else {
					$('select[name="' + $this + '_opacity]"]').chosen();
				}
				$('select[name="' + $this + '_opacity]"]').trigger('chosen:updated');
				
				/**
				 * .replace @ref	http://stackoverflow.com/a/3812077/558561
				 */
				var str = $this + '_opacity';
				if ( !$('input[name="' + $this + '_checkbox]"]').is(':checked') ) {
					$('#' + str.replace( /[\[\]]/g, '_' ) + '__chosen').hide();
				}
				
				$('input[name="' + $this + '_checkbox]"]').on('change', function() {
					$('#' + str.replace( /[\[\]]/g, '_' ) + '__chosen').toggle();
				});
			});
		}
		
		/**
		 *** callback_select ****************
		 ************************************
		 ************************************
		 */
		if ( cl_settings_api.callback_select ) {
			$.each( cl_settings_api.callback_select, function( index, value ) {
				$('select[name="' + value.section + '[' + value.id + ']"]').chosen();
				$('select[name="' + value.section + '[' + value.id + ']"]').trigger('chosen:updated');
			});
		}
		
		/**
		 *** callback_file ******************
		 ************************************
		 ************************************
		 */
		if ( cl_settings_api.callback_file ) {
			$.each( cl_settings_api.callback_file, function( index, value ) {
				// WP 3.5+ uploader
				var file_frame;
				var wp_media_post_id = wp.media.model.settings.post.id;
				var set_to_post_id = 0;
				window.formfield = '';
				
				$(document.body).on('click', 'input[type="button"].button.' + value.id + '-browse', function(e) {		
					e.preventDefault();
		
					$this = $(this);				
					window.formfield = $this.closest('td');
		
					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
						file_frame.open();
						return;
					} else {
						// Set the wp.media post id so the uploader grabs the ID we want when initialised
						wp.media.model.settings.post.id = set_to_post_id;
					}
		
					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media({
						frame: 'post',
						state: 'insert',
						title: $this.data( 'uploader_title' ),
						button: {
							text: $this.data( 'uploader_button_text' ),
						},
						library: {
							type: 'image',
						},
						multiple: false  // Set to true to allow multiple files to be selected
					});
		
					file_frame.on( 'menu:render:default', function(view) {
						// Store our views in an object.
						var views = {};
		
						// Unset default menu items
						view.unset('library-separator');
						view.unset('gallery');
						view.unset('featured-image');
						view.unset('embed');
		
						// Initialize the views in our view object.
						view.set(views);
					});
		
					// When an image is selected, run a callback.
					file_frame.on( 'insert', function() {	
						var attachment = file_frame.state().get('selection').first().toJSON();
						
					//	console.log(attachment);
					//	console.log(window.formfield.find('input[type="text"]').attr('id'));
						
						window.formfield.find('input[type="text"]').val(attachment.url);
						window.formfield.find('#' + value.id + '_preview').html('<div class="img-wrapper" style="width:250px"><img src="' + attachment.url + '" alt="" ><a href="#" class="remove_file_button" rel="' + value.id + '">Remove Image</a></div>');
						
						// @since		3.0.1
						// @updated	3.0.3
						if ( window.formfield.find('input[type="text"]').attr('id') == 'custom_login_design[logo_background_url]' ) {
							window.formfield.parents('table').find('input[name="custom_login_design[logo_background_size_width]"]').val(attachment.width);
							window.formfield.parents('table').find('input[name="custom_login_design[logo_background_size_height]"]').val(attachment.height);
						}
						
					});
		
					// Finally, open the modal
					file_frame.open();
				});
				
				$('input[type="button"].button.' + value.id + '-clear').on('click', function(e) {  
					e.preventDefault();
					$(this).closest('td').find('input[type="text"]').val('');
					$(this).closest('td').find('#' + $(this).prop('id').replace( '_clear', '_preview') + ' div.image').remove();
				});
				$('a.remove_file_button').on( 'click', function(e) {
					e.preventDefault();
					$(this).closest('td').find('input[type="text"]').val('');
					$(this).parent().slideUp().remove();
				});
			});
		}
	
		if ( typeof( ace ) !== 'undefinded' ) {
				
			/**
			 *** Custom CSS Textarea ************
			 */
			var custom_css_textarea = $('textarea[id$="custom_css]"]');
				custom_css_textarea.parents('tr').find('td').prop('colspan','2');
				custom_css_textarea.parents('tr').find('th').remove();
			$('<div id="custom_login[custom_css]_ace"/>').insertAfter(custom_css_textarea);
			var custom_css = ace.edit("custom_login[custom_css]_ace");
			custom_css.setOptions({
				maxLines: 30,
				autoScrollEditorIntoView: true
			});
			custom_css.getSession().setMode("ace/mode/css");
			custom_css.setTheme("ace/theme/github");
			custom_css_textarea.hide();
			custom_css.getSession().setValue(custom_css_textarea.val());
			custom_css.getSession().on('change', function(){
				custom_css_textarea.val(custom_css.getSession().getValue());
			});
					
			/**
			 *** Custom HTML Textarea ***********
			 */
			var custom_html_textarea = $('textarea[id$="custom_html]"]');
				custom_html_textarea.parents('tr').find('td').prop('colspan','2');
				custom_html_textarea.parents('tr').find('th').remove();
			$('<div id="custom_login[custom_html]_ace"/>').insertAfter(custom_html_textarea);
			var custom_html = ace.edit("custom_login[custom_html]_ace");
			custom_html.setOptions({
				maxLines: 30,
				autoScrollEditorIntoView: true
			});
			custom_html.getSession().setMode("ace/mode/html");
			custom_html.setTheme("ace/theme/github");
			custom_html_textarea.hide();
			custom_html.getSession().setValue(custom_html_textarea.val());
			custom_html.getSession().on('change', function(){
				custom_html_textarea.val(custom_html.getSession().getValue());
			});
					
			/**
			 *** Custom JS Textarea *************
			 */
			var custom_js_textarea = $('textarea[id$="custom_jquery]"]');
				custom_js_textarea.parents('tr').find('td').prop('colspan','2');
				custom_js_textarea.parents('tr').find('th').remove();
			$('<div id="custom_login[custom_jquery]_ace"/>').insertAfter(custom_js_textarea);
			var custom_js = ace.edit("custom_login[custom_jquery]_ace");
			custom_js.setOptions({
				maxLines: 30,
				autoScrollEditorIntoView: true
			});
			custom_js.getSession().setMode("ace/mode/javascript");
			custom_js.setTheme("ace/theme/github");
			custom_js_textarea.hide();
			custom_js.getSession().setValue(custom_js_textarea.val());
			custom_js.getSession().on('change', function(){
				custom_js_textarea.val(custom_js.getSession().getValue());
			});
		} // ace
	
		/**
		 *** Setting Function ***************
		 ************************************
		 ************************************
		 */
		function get_setting( id ) {
			
			// @ref		http://stackoverflow.com/a/8769051/558561
			if ( $.inArray(id, loaded) === -1 ) {
				
				var jqxhr = $.post( ajaxurl, {
					action	: cl_settings_api.prefix + '_get_form',
					nonce	: cl_settings_api.nonce,
					form_id	: id,
				}, function(response) {
					if ( response.error == 0 ) {
						$(id).html(response.html);
					}
				}, 'json' );
				
				loaded.push( id );
			}
			
		} // get_setting()
					
	}); // (document)
	
	/**
	 * Helper function to create contracting color
	 * @ref		http://codeitdown.com/jquery-color-contrast/
	 */
	$.fn.contrastColor = function() {
		return this.each(function() {
			var bg = $(this).css('background-color');
			//get r,g,b and decide
			var rgb = bg.replace(/^(rgb|rgba)\(/,'').replace(/\)$/,'').replace(/\s/g,'').split(',');
			var yiq = ((rgb[0]*299)+(rgb[1]*587)+(rgb[2]*114))/1000;
			if ( yiq >= 128 ) {
				$(this).children().css('color','#111111');
			} else {
				$(this).children().css('color','#ffffff');
			}
		});
	};
	
}(jQuery));