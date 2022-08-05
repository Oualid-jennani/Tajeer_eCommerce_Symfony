$(document).ready( function () {
    $(".spinner-border").hide();
    /*const url = Routing.generate('cities', {});*/
    var $country = $(this).val();

    $('#seller_form_country').on('change', function () {
        $country = $(this).val();
        $.ajax({
            'url' : '/provinces', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#seller_form_province').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#seller_form_province').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });

    $('#seller_form_province').on('change', function () {
        const $province = $(this).val();
        $.ajax({
            'url' : '/province-cities', // To change with Routing.generate later
            'data' : {
                'province' : $province
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#seller_form_city').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#seller_form_city').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });

    // -----------  Js Add City ------------

    $('#city_form_country').on('change', function () {
        $country = $(this).val();
        $.ajax({
            'url' : '/provinces', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#city_form_province').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#city_form_province').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });
    // Js Edit City
    $('#edit_city_country').on('change', function () {
        $country = $(this).val();
        $.ajax({
            'url' : '/provinces', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#edit_city_province').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#edit_city_province').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });

    // Icons Category script for AddCategory And Edit
    var value = false;
    var iconClass = 'lni lni-user';
    // Verify a value of icons for edit
    if ($('#category_icon').val()!= '') {
        value = true;
        iconClass = $('#category_icon').val().split(" ")[1];
        $('.'+iconClass).parents( "div .theme-icons" ).css({
            'background-color':'#AEEBFF'
        });
        $('div.overflow-auto').animate({scrollTop: $('.'+iconClass).offset().top -450 }, 'slow');
    }
    $(".theme-icons").click(function () {
        if (value === true) {
            $('.theme-icons').css({
                'border':'none',
                'background-color':'white'
            });
            iconClass = $(this).find('.lni').attr('class');
            $(this).css({
                'background-color':'#AEEBFF'
            });
        }else {
            value=true;
            iconClass = $(this).find('.lni').attr('class');
            $(this).css('border',{
                'background-color':'#AEEBFF'
            });
        }
        $('#category_icon').val(iconClass);
    })
});