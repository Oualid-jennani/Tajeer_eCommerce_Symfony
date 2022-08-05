$(document).ready( function () {

    $('.delete-line').on('click',function () {
        $(this).parents("li").remove();
    })


    $('#customer_form_country').on('change', function () {
        $country = $(this).val();
        $.ajax({
            'url' : '/provinces', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#customer_form_province').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#customer_form_province').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });

    $('#customer_form_province').on('change', function () {
        const $province = $(this).val();
        $.ajax({
            'url' : '/province-cities', // To change with Routing.generate later
            'data' : {
                'province' : $province
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#customer_form_city').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#customer_form_city').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });



    $('#order_country').on('change', function () {
        $country = $(this).val();
        $.ajax({
            'url' : '/provinces', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#order_form_province').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#order_province').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });

    $('#order_province').on('change', function () {
        const $province = $(this).val();
        $.ajax({
            'url' : '/province-cities', // To change with Routing.generate later
            'data' : {
                'province' : $province
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#order_city').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#order_city').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    });

});