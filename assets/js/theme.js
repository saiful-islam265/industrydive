jQuery(function($){ 
    var queryData = {
        'action': 'loadmore_posts',
        'page' : $("#archive_section").length > 0 ? 3 : 2
    };

    //Featured Slider
    $('.featured-slider').slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        //autoplay: true
    });

    // Loadmore
	$('.loadmore-post').click(function(){
 
		var button = $(this),
            postWrap = $('#latest_posts');
 
		$.ajax({ 
			url : id.ajaxurl, 
			data : queryData,
			type : 'POST',
			beforeSend : function ( xhr ) {
				button.text('Loading...');
			},
			success : function( data ){
				if( data ) { 
					button.text( 'Load More' );
                    postWrap.append(data.posts); 
					queryData.page++;
 
					if ( queryData.page == data.max_page ) 
						button.remove();
				} else {
					button.remove();
				}
			}
		});
	});

    // Affix nav
    var target = $('.site-navbar');
    var position = target.position();
    window.addEventListener('scroll', function () {
        var height = $(window).scrollTop();
        if (height > position.top) {
            target.addClass('affix');
        } else {
            target.removeClass('affix');
        }
    });

    // Mobile nav toggle
    $('.btn-menu-toggle').on('click', function() {
        if ($('.site-navbar').is(':hidden')) {
            $('.site-navbar').slideDown();
            $(this).html('<span class="dashicons dashicons-no-alt"></span>');
        } else {
            $('.site-navbar').slideUp();
            $(this).html('<span class="dashicons dashicons-menu"></span>');
        }
    });

    $(window).on('resize orientationchange', function(){
        if ($(window).width() < 1024) {
            $('.site-navbar').css('display', 'none');
        } else {
            $('.site-navbar').css('display', 'block');
        }
    });

    // Subscribe toggle
    $('.subscription-toggle').on('click', function(){
        $('.subscription-popup-wrapper').addClass('shown');
    });
    $('.subscription-close').on('click', function(){
        $('.subscription-popup-wrapper').removeClass('shown');
    });
    $(document).on('click', function(e) {
        if(e.target.classList.contains('subscription-popup-wrapper')){
            $('.subscription-popup-wrapper').removeClass('shown');
        }
    });

    //Subscribe action
    $('#subsciption-form').on('submit', function(e) {
        e.preventDefault();
        var data = {action: "subscribe_email"};
        $(this).serializeArray().map(function(x){data[x.name] = x.value;});

        var that = this;
        $.ajax({
            type: "POST",
            url: id.ajaxurl,
            data: data,
            beforeSend: function(xhr){
                $(that).children('.subscribe-form-response').html('Be patiend. Submitting your email').addClass('subscription-wait').removeClass('subscription-success').removeClass('subscription-error');
            },            
            success: function (response) {
                if(response.success){
                    $(that).children('.subscribe-form-response').html(response.data).addClass('subscription-success').removeClass('subscription-wait').removeClass('subscription-error');
                } else {
                    $(that).children('.subscribe-form-response').html(response.data).addClass('subscription-error').removeClass('subscription-wait').removeClass('subscription-success');
                }
            },
            error : function(error){
                $(that).children('.subscribe-form-response').html(error.statusText).addClass('subscription-error').removeClass('subscription-success').removeClass('subscription-wait');
            }
        })
    });
});