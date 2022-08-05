/*-----------------------------------------------------------------------------------

 Template Name: reno
 Template URI: http://themes.pixelstrap.com/reno/
 Description: This Is multipurpose service bases template
 Author: Pixelstrap
 Author URI : https://themeforest.net/author_dashboard

 ----------------------------------------------------------------------------------- */
// 1. Off set js
// 2. Menu add class
// 3. Tap to Top


(function($) {
    /*=====================
     1. Off set js
     ==========================*/
    if ($(window).width() >= 991) {
        $("nav a").on('click', function (event) {
            if (this.hash !== "") {
                event.preventDefault();
                var hash = this.hash;
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 45
                }, 1000, function () {
                });
                return false;
            }
        });
    } else {
        $("nav a").on('click', function (event) {
            if (this.hash !== "") {
                event.preventDefault();
                var hash = this.hash;
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 50
                }, 1000, function () {
                });
                return false;
            }
        });
    }

    /*=====================
     2. Menu add class
     ==========================*/
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 100) {
            $('.navbar').addClass("fixed");
        } else {
            $('.navbar').removeClass("fixed");
        }
    });

    /*=====================
     3. Tap to Top
     ==========================*/
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 500) {
            $('.tap-top').fadeIn();
        } else {
            $('.tap-top').fadeOut();
        }
    });
    $('.tap-top').on('click', function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

})(jQuery);
