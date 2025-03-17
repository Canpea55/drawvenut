$(function() {
    var $grid = $('.grid').masonry({
        itemSelector: '.grid-item',
    });

    // Layout Masonry after each image loads
    $('.grid img').each(function() {
        var $img = $(this);
        imagesLoaded($img[0], function() {
            $grid.masonry('layout');
        });
    });
});