// JavaScript Document
jQuery(document).ready(function($){
	var grid_content = jQuery('.portfolio-grids').clone();
	var $grid = $('.portfolio-grids').masonry({
		// option
		columnWidth: '.grid-item', itemSelector: '.grid-item', percentPosition: true
	});
	
	function sort_masonry (){
		var options;
		/*$('.portfolio-grids .grid-item:visible').each(function(index, element){
			var total = $('.portfolio-grids .grid-item:visible').length;
			var $count = index + 1;
			var $col_class = '';
			if ($count %3 == 0 && total >= 6) {
				$col_class ='col-lg-6 col-md-4 col-sm-6 col-6';
			} else if ($count % 4 == 0) {
				$col_class ='col-lg-3 col-md-8 col-sm-12 col-6';
			} else {
				$col_class ='col-lg-3 col-md-4 col-sm-6 col-6';
			}
			
			$(element).removeClass('col-lg-6 col-md-4 col-sm-6 col-6 col-lg-3 col-md-8 col-sm-12 col-6 col-lg-3 col-md-4 col-sm-6 col-6');
			$(element).addClass($col_class);
		});*/
		
		$('.portfolio-grids').css('opacity', 1);
	  // trigger layout
	  //$grid.masonry('layout');
$grid.imagesLoaded().progress( function() {
    console.log('ca3');
  $grid.masonry('layout');
});
	}
	//setTimeout(function(){$grid.masonry();}, 1500);
	
// init Masonry
//var $grid = $('.grid').masonry({
//  // options...
//});
// layout Masonry after each image loads
$grid.imagesLoaded().progress( function() {
    console.log('ca1');
  $grid.masonry('layout');
});
	
	
	$("body").on('click', '.portfolio-sidebar li:not(.init)', function(){
		$('.portfolio-sidebar li').removeClass('active');
		$(this).addClass('active');
		
		var id=jQuery(this).attr('data-id');
		var cat_title = $(this).find('.filter_link').text();
		var cat_desc = $(this).find('.portfolio_cat_des').text();
		var cat_parent_title = $(this).find('.portfolio_cat_parent_title_hidden').text();
		var cat_parent_desc = $(this).find('.portfolio_cat_parent_des_hidden').text();
		
		if (!cat_title && !cat_desc && !cat_parent_title && !cat_parent_desc)
		{
			$('.series_catch').removeClass('active');
		}
		else {
			$('.series_catch').addClass('active');
		}
		
		if ($('.cat-portfolio-mobile-wraper').is(':visible'))
		{
			$("html, body").animate({ scrollTop: $('.series_catch').offset().top - 68}, 1000);
		}
		else {
			$("html, body").animate({ scrollTop: $('.series_catch').offset().top - 80}, 1000);
		}

		$('.portfolio-grids').css('opacity', 0);
//		jQuery(".portfolio-grids .all-port").show();
		
		var remove_elements = $('.portfolio-grids .grid-item');
		$('.portfolio-grids').masonry( 'remove', remove_elements );
		remove_elements.remove();
		
		var grid_content_clone = grid_content.clone();
		var grid_content_html = $(grid_content_clone.html());
		
		$('.portfolio_cat_title').text(cat_title);
		$('.portfolio_cat_desc').text(cat_desc);
		if (cat_parent_title)
		{
			$('.portfolio_cat_parent_title').text(cat_parent_title);
			$('.portfolio_cat_parent_title').show();
		}
		else {
			$('.portfolio_cat_parent_title').hide();
		}
		
		// SHow child title, description for parent category 
		$('.portfolio_cat_child_title').remove();
		$('.portfolio_cat_child_desc').remove();
		
		$(this).find('.portfolio_cat_child_title_hidden').each(function(index, element){
			var title = $(this).text();
			var description = $(this).closest('li').find('.portfolio_cat_child_des_hidden:eq('+ index +')').text();
			
			if (title)
				$('.series_catch').append('<h3 class="heading heading--small portfolio_cat_child_title">'+ title +'</h3>');
			if (description)
				$('.series_catch').append('<div class="desc portfolio_cat_child_desc">'+ description +'</div>');
			
		})
		
		if (cat_parent_desc)
		{
			$('.portfolio_parent_cat_desc').text(cat_parent_desc);
			$('.portfolio_parent_cat_desc').show();
		}
		else {
			$('.portfolio_parent_cat_desc').hide();
		}
		
		
		$grid.append( grid_content_html ).masonry( 'appended', grid_content_html );
		
		if(id!='all'){
			$(".portfolio-grids .all-port:not(."+ id +")").hide();
			$(".portfolio-grids ."+id).show();
			
			setTimeout(function(){
				$('.portfolio-grids .grid-item').each(function(index, element){
					if (!$(element).hasClass(id))
					{
						$('.portfolio-grids').masonry( 'remove', element );
					}
				});
				setTimeout(function(){sort_masonry();}, 300)
			}, 200)
		}
		else {
                    $(".portfolio-grids .all-port").show();
			setTimeout(function(){sort_masonry();}, 300);
		}
	});
	
	$("ul.cat-portfolio-mobile").on("click", ".init", function() {
		$(this).closest("ul.cat-portfolio-mobile").children('li:not(.init)').toggleClass('portfolio-mobile-active');
	    $(this).closest("ul.cat-portfolio-mobile").children('li:not(.init)').toggle();
		$(this).closest("ul.cat-portfolio-mobile").toggleClass('active');
	});

	var allOptions = $("ul.cat-portfolio-mobile").children('li:not(.init)');
	$("ul.cat-portfolio-mobile").on("click", "li:not(.init)", function() {
	    allOptions.removeClass('selected');
	    $(this).addClass('selected');
	    $("ul.cat-portfolio-mobile").children('.init').html($(this).html());
	    $("ul.cat-portfolio-mobile").children('.init').attr('data-id', $(this).attr('data-id'));
	    allOptions.toggle();
	});
	
	$('body').on('click', '.pf_link', function(e){
		  e.preventDefault();
		  var inst = $('[data-remodal-id=portfolio_modal]').remodal(); 
		  var wraper = $(this).closest('.grid-item');
		  var pID = $(this).attr('data-id');
		  
		  if ($(this).attr('data-serie_index') != undefined)
		  {
			  
			  var image_serie_index = $(this).attr('data-serie_index');
			  var data = {action: 'get_portfolio', id: pID, image_serie_index: image_serie_index };
		  }
		  else {
			  var data = {action: 'get_portfolio', id: pID};
		  }
		  
		  $('body').LoadingOverlay('show');
		  $.ajax({
		        type: "post",
		        url: gl_ajax_url,
		        data: data,
		        dataType : "html",
		        scriptCharset: 'utf-8'
		    }).success(function(response){
		    	$('.remodal_wraper').html(response);
		    	 inst.open();
			 	
		    	 $('body').LoadingOverlay('hide');
				 
		    }).complete(function(){
				//slick in modal				
				$('.slick-gallery').slick({
					//rtl: true
				});
			});
		$('.slick-list > .slick-track > .attachment-portfolio').each( function() {
			//i.preventDefault();
			var slickW = $(this).width();
			var slickH = $(this).height();
			console.log('PfImageW：' + slickW + 'px');
			console.log('PfImageH：' + slickH + 'px');
			if (slickW < slickH) {
				slickW.css('width', 'auto');
				slickH.css('height', '100%');
			}
		});
		
	  });
});