(function($) {
    var services_slide = $(".services_slide");
    services_slide.owlCarousel({
        autoplay: true,
        loop: true,
        dots: false,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        items: 6,
        responsive:{
            0:{
                items:1
            },
            480:{
                items:2,
                margin:15
            },
            768:{
                items:3,
                margin:15
            },
            991:{
                items:3,
                margin:30
            },
            992:{
                items:4,
                margin:30
            }
        }
    });

    var price_slide = $(".price_slide");
    price_slide.owlCarousel({
        autoplay: false,
        loop: true,
        dots: false,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        items: 6,
        responsive:{
            0:{
                items:1
            },
            480:{
                items:1
            },
            768:{
                items:2,
                margin:30
            },
            992:{
                items:3,
                margin:30
            }
        }
    });

})(jQuery);






