jQuery(document).ready(
	function($) {
		var Version = jQuery.fn.jquery;
		
		$('.upload_image_button').click(function() {
			if( Version > '1.6' )
				formfield = $(this).parent().find('.upload_image').prop('name');
			else
				formfield = $(this).parent().find('.upload_image').attr('name');
			//console.log(formfield);
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			return false;
		});
		
		window.send_to_editor = function(html) {
			if( Version > '1.6' )
				imgurl = $('img',html).prop('src');
			else
				imgurl = $('img',html).attr('src');
			$('#' + formfield).val(imgurl);
			tb_remove();
		}
		
		// Tabs
		$('div.tabbed div').hide();
		$('div.t1').show();
		$('div.tabbed ul.tabs li.t1 a').addClass('tab-current');
		$('div.tabbed ul li a').css('cursor','pointer');
	
		$('div.tabbed ul li a').click(function(){
			var thisClass = this.className.slice(0,2);
			$('div.tabbed div').hide();
			$('div.' + thisClass).show();
			$('div.tabbed ul.tabs li a').removeClass('tab-current');
			$(this).addClass('tab-current');
		});
		
		// Queations
		$('#normal-sortables span.hide').hide();
		$('#normal-sortables a.question').click(function() {
			$(this).next().next().toggleClass('hide').toggleClass('show').toggle(380);
		});
		
		$('textarea').autosize({
			append	: "\n"
		});
		
		// External links
		$('a').filter(function() {
			return this.hostname && this.hostname !== location.hostname;
		}).attr('target','_blank');
	
	}
);