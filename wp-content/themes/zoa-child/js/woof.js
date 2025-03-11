jQuery(document).ready(function($){
	$('ul.woof_list_label > li').each( function() {
		var labelName = $(this).find('input.woof_label_term').attr('name');
		$(this).children('.woof_label_term').addClass('label-'+labelName);
	});
	$(window).on('load resize', function() {
		var $win = $(window);
		if ($win.width() > 991) {
			$('.shop-sidebar .WOOF_Widget').addClass('show');
			$('.shop-sidebar .WOOF_Widget').removeClass('hide');
			$('#closeRefinement').hide();
		} else {
			$('.shop-sidebar .WOOF_Widget').addClass('hide');
			$('.shop-sidebar .WOOF_Widget').removeClass('show');
			$('#closeRefinement').show();
		}
	});
	/***
	*
	* Product filter clear button *
	*/	
	var productFilterClear=function(){
		$('.woof_container').each(function( index ) {
			var checked=$(this).find('.woof_checkbox_term:checked').length;
			if(checked > 0){
				$(this).find('h4.toggle__name').after('<a href="javascript:void(0);" class="filterClear" >Clear</a>');
			}
		  
		});	
		$('a.filterClear').on('click',function(e){
			e.stopPropagation();
			var chkbox=$(this).parents('div.woof_container_inner').find('.woof_checkbox_term').attr('checked',false);
			var tax=$(this).parents('div.woof_container_inner').find('.woof_checkbox_term').data('tax');
			$(this).parents('div.woof_container_inner').find('.woof_checkbox_term').parent('div').removeClass('checked');	
			woof_current_values[tax]='';
			woof_submit_link(woof_get_submit_link());				
		});	
	};
	
	var customFilterSidebar=function(){
		$(".woof_sid_widget .woof_redraw_zone > .woof_container > .woof_container_inner > h4 > .woof_front_toggle").on("click", function(e){		
			e.preventDefault();
			var $win = $(window);
			if ($win.width() < 992) {
				$('#shop-overlay').addClass("set--active");
			} else if ($win.width() > 991 && $('#shop-overlay').hasClass('set--active')) {
				$('#shop-overlay').removeClass("set--active");
			}
			
			/*
			if(!$(this).hasClass("toggle--active")) {			
			$(this).addClass("toggle--active");
			$(this).next(".toggle__content").addClass("toggle--active");
			$(this).parent().addClass("toggle--active");
			if ($(window).width() < 992) {
				$('#shop-overlay').addClass("set--active");
			}
		} else if($(this).hasClass("toggle--active")) {
			$(this).removeClass("toggle--active");
			$(this).next(".toggle__content").removeClass("toggle--active");
			$(this).parent().removeClass("toggle--active");
			if ($(window).width() < 992) {
				$('#shop-overlay').addClass("set--active");
			}
		}*/
		});
	};
	//refiment toggle
	$(document).on("click", function(e){
		if ($(window).width() < 992) {
			if($(e.target).closest('#refinementsBarTrigger').length && !$('#refinementsBarTrigger').hasClass("toggle--active")) {
				$('#refinementsBarTrigger').addClass("toggle--active");
				$('#shop-overlay').addClass("set--active");
				$('#refinementsBarTrigger').next(".WOOF_Widget").addClass("toggle--active");
				$('#refinementsBarTrigger').parent().addClass("toggle--active");
				$('body').css('overflow', 'hidden');
			} else if(!$(e.target).closest('.WOOF_Widget').length || $(e.target).closest('#closeRefinement').length && $('#refinementsBarTrigger').hasClass("toggle--active") || !$(e.target).closest('.woof_sid_widget').length) {
				$('#refinementsBarTrigger').removeClass("toggle--active");
				$('#shop-overlay').removeClass("set--active");
				$('#refinementsBarTrigger').next(".WOOF_Widget").removeClass("toggle--active");
				$('#refinementsBarTrigger').parent().removeClass("toggle--active");
				$('body').css('overflow', 'auto');
			}
		}
	});
	
	customFilterSidebar(); 
	jQuery(document).on("woof_ajax_done", woof_ajax_done_handler);
	function woof_ajax_done_handler(g) {
		g.preventDefault();
		customFilterSidebar();
		productFilterClear();
	}
	function moveSizeChart()
	{
		var tab_length = $('.wc-tabs-wrapper .wc-tabs li').length;
		if (tab_length > 1 || $('.wc-tabs-wrapper .wc-tabs li.custom_tab_tab').length == 0)
		{
			$('.wc-tabs-wrapper').show();
		}
		//$('#size_chart_content').html($('#tab-custom_tab'));
	}
	
	moveSizeChart();
	productFilterClear();
});
