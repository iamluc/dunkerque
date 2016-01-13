$(document).ready(function() {
    $('#repository-star').click(function(e) {
        var link = $(this);
        var icon = link.find('.glyphicon');

        $.ajax(
            icon.hasClass('glyphicon-star') ? link.attr('data-url-unstar') : link.attr('data-url-star'),
            {method: 'PUT'}
        ).done(function(data) {
            if (data.starred) {
                icon.addClass('glyphicon-star').removeClass('glyphicon-star-empty');
            } else {
                icon.addClass('glyphicon-star-empty').removeClass('glyphicon-star');
            }
        });
        e.preventDefault();
    });
})
