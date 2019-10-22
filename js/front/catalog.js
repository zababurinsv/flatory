;
var FlCatalog = (function() {
    var app = {
        init: function() {
            this.setUpListeners();

            $('.jcarousel-pagination')
                    .on('jcarouselpagination:active', 'a', function() {
                        $(this).addClass('active');
                    })
                    .on('jcarouselpagination:inactive', 'a', function() {
                        $(this).removeClass('active');
                    })
                    .on('click', function(e) {
                        e.preventDefault();
                    });

            if ($('.simple_carusel .jcarousel-pagination').length)
                $('.simple_carusel .jcarousel-pagination').jcarouselPagination({
                    perPage: 1,
                    item: function(page, carouselItems) {
                        return '<a href="javascript:void(0)">' + page + '</a>';
                    }
                });

            // crutch: hide pagination if less than 1
            $('.simple_carusel').each(function() {
                if ($(this).find('li').length <= 1)
                    $(this).find('.jcarousel-pagination').hide();
            });

            if (location.hash)
                $(location.hash).parent('.cart_title').trigger('click');
        },
        setUpListeners: function() {
            $('.cart_title').off('click').on('click', app.toggleSection);
            $('.album').off('click').on('click', app.showAlbum);
            $('.album_submit').off('click').on('click', app.showAllAlbums);
        },
        toggleSection: function(e) {
            $(this).next().toggle(300);
            if ($(this).children('.toggle').attr('src') === '/images/toggle.png') {
                $(this).children('.toggle').attr('src', '/images/toggle_right.png');
                $(this).parent('div').css('padding-bottom', '25px');
            } else {
                $(this).children('.toggle').attr('src', '/images/toggle.png');
                $(this).parent('div').css('padding-bottom', '10px');
            }
        },
        /**
         * Show current album
         * @param {object} e
         * @returns {undefined}
         */
        showAlbum: function(e) {
            var section = $(this).parents('.ficha');

            section.find('.simple_carusel').hide(300);
            section.find('.albums_controls .album_date').text($(this).data('name'));
            section.find('.albums_controls').show(300);
            section.find('.album_content[data-id="' + $(this).data('id') + '"]').show(300);
        },
        /**
         * Hide current album & show all albums
         * @param {object} e
         * @returns {undefined}
         */
        showAllAlbums: function(e) {
            var section = $(this).parents('.ficha');
            section.find('.albums_controls').hide(300);
            section.find('.album_content').hide(300);
            section.find('.simple_carusel').show(300);
        },
        publ: {}
    };
    app.init();
    return app.publ;
}());