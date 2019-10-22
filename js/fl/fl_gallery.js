var FlGallery = (function() {
    var app = {
        targets: {
            gallery: '.fl-gallery',
            carusel: '.jcarousel',
            prev: '.jcarousel-control-prev',
            next: '.jcarousel-control-next',
            albumMeta: '.album_meta'
        },
        init: function() {
            // init carusel
            if (typeof $.jcarousel === 'function')
                $(this.targets.carusel).jcarousel({
                    wrap: 'circular',
                    itemLoadCallback: function(e) {
                    }
                });
            // init fancybox
            $('.fancybox-thumbs').fancybox({
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                afterLoad: function() {
                    var meta = $(this.element).parents('li').data(), content;

                    if (!$.isEmptyObject(meta)) {
                        meta.total = $(this.element).parents(app.targets.carusel).find('li').length;
                        // render
                        content = $('<div/>').html(app.tpl.albumMeta(meta)[0].outerHTML);
                        content.find('.credits').html(content.find('.credits').text());
                        this.title = content.html();
                    }
                },
                arrows: true,
                nextClick: true
            });
            this.setUpListeners();
        },
        setUpListeners: function() {
            $(this.targets.prev).off('click').on('click', app.prev);
            $(this.targets.next).off('click').on('click', app.next);
            $(this.targets.carusel).off('jcarousel:targetin', 'li').on('jcarousel:targetin', 'li', app.caruselScroll);
        },
        caruselScroll: function(event, carousel) {

            var meta = $(this).data(), metaPlace = $(this).parents(app.targets.carusel).siblings(app.targets.albumMeta);

            metaPlace.find('.carusel-counter').text(meta.index + ' / ' + carousel._items.length);
            metaPlace.find('.caption').text(meta.caption);
            if (meta.credits) {
                metaPlace.find('.credits').html(meta.credits);
                metaPlace.find('.credits').show();
            } else {
                metaPlace.find('.credits').hide();
            }
        },
        prev: function(e) {
            $(this).parents(app.targets.gallery).find(app.targets.carusel).jcarousel('scroll', '-=1');
        },
        next: function(e) {
            $(this).parents(app.targets.gallery).find(app.targets.carusel).jcarousel('scroll', '+=1');
        },
        tpl: {
            /**
             * Render image meta data
             * @param {object} meta
             * @returns {jQuery}
             */
            albumMeta: function(meta) {
                var tpl = $(app.targets.albumMeta).clone();
                if (meta === undefined) {
                    tpl.find('.carusel-counter').empty();
                    tpl.find('.credits').empty();
                    tpl.find('.caption').empty();
                    tpl.find('.credits').hide();
                } else {
                    tpl.find('.carusel-counter').text(meta.index + ' / ' + meta.total);
                    tpl.find('.caption').text(meta.caption);
                    if (meta.credits) {
                        tpl.find('.credits').text(meta.credits);
                        tpl.find('.credits').show();
                    } else {
                        tpl.find('.credits').hide();
                    }
                }

                return tpl;
            }
        },
        publ: {
            tpl: function() {
                return app.tpl;
            }
        }
    };
    app.init();
    return app.publ;
}());