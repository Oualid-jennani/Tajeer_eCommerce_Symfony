//-------- href delete -------------
function deleteAction(link) {
    document.getElementById("linkDelete").href = link;
}
//-------- href delete -------------

$(".alert").fadeTo(3000, 500).slideUp(200);
$(".errors").fadeTo(5000, 500).fadeOut(200);


if($('#product_subCategory').find('option').length == 0)
{
    $('.subCat').hide();
}

function changeSubCategory($category){
    $.ajax({
        'url' : '/admin/subCategory', // To change with Routing.generate later
        'data' : {
            'category' : $category
        },
        'type': 'post',
        'beforeSend' : function() {
            $('#product_subCategory').find('option').remove();
            $('.spinner-border').show();
        },
        success: function (data) {
            console.log(data.length);
            if(data.length == 0)
            {
                $('.subCat').hide();
                $('.spinner-border').hide();
                $('#product_subCategory').prop('placeholder','No Sub Category');
            }
            else
            {
                $('.subCat').show();
            }
            $.each(data, function (i, p) {
                $('#product_subCategory').append(new Option(p.name, p.id));
                $('.spinner-border').hide();
            });
        }
    });
}

if($)
changeSubCategory($('#product_category').val());

$('#product_category').on('change', function () {
    const $category = $(this).val();
    changeSubCategory($category)
});

