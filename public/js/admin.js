$('.close-alert').on('click', function (e) {
    e.preventDefault();
    $('.alert').remove();
});

jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });

    jQuery('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        var counter = list.data('widget-counter') || list.children().length;
        var newWidget = list.attr('data-prototype');
        console.log(newWidget);
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data(' widget-counter', counter);
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.addClass('delete_' + counter);
        newElem.find('.vich-file').append('<button data-id="delete_' + counter + '" type="button" id="delete-select-btn" class="btn btn-danger">Видалити</button>');
      //  newElem.appendTo(list);
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