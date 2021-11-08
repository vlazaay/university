jQuery(document).ready(function () {
    jQuery('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        var counter = list.data('widget-counter') || list.children().length;
        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data(' widget-counter', counter);
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.addClass('delete_' + counter);
        newElem.find('.vich-file').append('<button data-id="delete_' + counter + '" type="button" id="delete-select-btn" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>');
        newElem.appendTo(list);
    });
    var form = $('form[name="auctions"]');
    form.on('click', ".btn-danger", function (e) {
        e.preventDefault();
        var classArr = $(this).data('id');
        $('.' + classArr + '').remove();
        console.log(classArr);
    });
});


$('#currencies').on('change', function (e) {
    e.preventDefault();
    var value = $(this).val();
    console.log(value);
    $.ajax({
        type: "POST",
        url: "/api/currency/set",
        data: {'code': value},
        success: function (msg) {
            console.log('success ' + msg);
            location.reload();
        },
        error: function (msg) {
            console.log('error ' + msg);
        }
    });
});

$('#language').on('change', function (e) {
    e.preventDefault();
    var value = $(this).val();
    console.log(value);
    $.ajax({
        type: "POST",
        url: "/api/languages/set",
        data: {'languages': value},
        success: function (msg) {
            //console.log('success1 '+ msg);
            var pathname = window.location.pathname,
                url = '',
                find = '',
                language = $('#language option');
            //console.log('success2 '+  pathname  );
            language.each(function (index) {
                if (pathname.indexOf('/' + $(this).text()) > -1) {
                    find = '/' + $(this).text();
                }
            });
            if (find != '') {
                //console.log('success3 '+  find  );
                if (value == 'ru') {
                    var url = pathname.replace(find, '');
                    //console.log('success3.1 '+  url  );
                } else {
                    var url = pathname.replace(find, '/' + value);
                    //console.log('success3.2 '+  url  );
                }
            } else {
                if (pathname == '/') {
                    url = '/' + value;
                    //console.log('success4 '+  url  );
                } else {
                    url = '/' + value + pathname;
                    //console.log('success5 '+  url  );
                }
            }
            if (url != '') {
                document.location.assign(url);
            } else {
                document.location.assign('/');
            }

            //location.reload();
        },
        error: function (msg) {
            console.log('error ' + msg);
        }
    });
});

function auctionsStatusActive(id, auctions_id) {
    console.log(id);
    console.log(auctions_id);

    $.ajax({
        url: '/api/auctions/bid/status',
        type: 'POST',
        dataType: 'json',
        async: true,
        data: {'bid': id, 'auctions_id': auctions_id},
        success: function (data, status) {
            console.log(data['data']);
            $('.list_id' + id).addClass('alert-success');
            $('#auctionsStatusActive').show(1);
            $('.container-auctions').addClass('status');
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Ajax request failed.');
        }
    })
}

$('#counterofferCheckbox').on('click', function (e) {
    if ($(this).is(":checked")) {
        $('#auctions_counteroffer_message_price').prop('disabled', false);
        $('#auctions_counteroffer_message_comment_div').show(1);
    } else {
        $('#auctions_counteroffer_message_price').prop('disabled', true);
        $('#auctions_counteroffer_message_comment_div').hide(1);
    }

});

$('#auctionsAddBidModal').on('click', function (e) {
    $('#auctionsAddBid').show(1);
});

$('#auctionsAddAutoBidModal').on('click', function (e) {
    $('#auctionsAddAutoBid').show(1);
});

$('#auctions_counteroffer_message_comment').on('change', function (e) {
    $(this).css({'border': '0 none'});
});

$('.flex-table.row').on('dblclick', function (e) {
    var id = $(this).attr("data-id");
    $('#auctionsAddBid' + id).show(1);
});

$('.modal .modal-content .close').on('click', function (e) {
    $('#' + $(this).data('id')).hide(1);
});

$('#auctions_category').on('change', function (e) {
    e.preventDefault();
    var category_id = $(this).find(":selected").val(),
        select = $('#characteristics_category');
    $.ajax({
        url: '/api/category/characteristics/list',
        type: 'GET',
        data: {'category_id': category_id},
        success: function (data, status) {
           // $('#characteristics_category_id').removeClass('hidden');
            // $.each(data, function (i, item) {
            //     select.append($('<option>', {
            //         value: i,
            //         text : item
            //     }));
            // });

        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr);
            console.log(textStatus);
            console.log(errorThrown);
        }
    })
});

$(document).ready(function () {
    $("#saveDoc").submit(function (e) {
        e.preventDefault();
        var $form = $(e.currentTarget);
        console.log($form);
        $.ajax({
            url: '/api/sing',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: $form.serialize(),
            success: function (data, status) {
                console.log(data);
                console.log(status);
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Ajax request failed.');
            }
        });
        return false;
    });

    $("#balance").submit(function (e) {
        e.preventDefault();
        var $form = $(e.currentTarget);
        console.log($form);
        $.ajax({
            url: '/api/balance/add',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: $form.serialize(),
            success: function (data, status) {
                console.log(data['data']['doc']);
                console.log(status);

                if (data['data']['doc'] != null) {
                    window.location.href = window.location.origin +'/' + data['data']['doc'];
                }
                alert('Оплата принята и обработана');
                //location.reload();
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Ajax request failed.');
            }
        });
        return false;
    });
});