/*-----------------------------------------------------------------------------------

 Template Name: reno
 Template URI: http://themes.pixelstrap.com/reno/
 Description: This Is multipurpose service bases template
 Author: Pixelstrap
 Author URI : https://themeforest.net/author_dashboard

 ----------------------------------------------------------------------------------- */

(function($) {
    $('.product-five').slick({
        dots: false,
        infinite: true,
        margin:30,
        speed: 300,
        arrow: false,
        slidesToShow: 5,
        slidesToScroll: 5,
        responsive: [
            {
                breakpoint: 1367,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4
                }
            },
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
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
                margin:15
            },
            992:{
                items:3,
                margin:15
            }
        }
    });
})(jQuery);






