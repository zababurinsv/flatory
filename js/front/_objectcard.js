//$(document).ready(function() {
//    $('.fancybox-thumbs').fancybox({
//        prevEffect: 'none',
//        nextEffect: 'none',
//        closeBtn: true,
//        arrows: false,
//        nextClick: true,
//        helpers: {
//            thumbs: {
//                width: 50,
//                height: 50
//            }
//        }
//    });
//})

// This is the connector function.
// It connects one item from the navigation carousel to one item from the
// stage carousel.
// The default behaviour is, to connect items with the same index from both
// carousels. This might _not_ work with circular carousels!
var connector = function(itemNavigation, carouselStage) {
    return carouselStage.jcarousel('items').eq(itemNavigation.index());
};
var carouselStage;
var carouselNavigation;
var stage = $(".carousel-stage");
var navigation = $(".carousel-navigation");
function init(i) {
    // Setup the carousels. Adjust the options for both carousels here.
    carouselStage = $('.' + stage[i].className.substring(24)).jcarousel();
    carouselNavigation = $('.' + navigation[i].className.substring(29)).jcarousel();
    // We loop through the items of the navigation carousel and set it up
    // as a control for an item from the stage carousel.
    $('.' + navigation[i].className.substring(29)).jcarousel().jcarousel('items').each(function() {
        var item = $(this);

        // This is where we actually connect to items.
        var target = connector(item, $('.' + stage[i].className.substring(24)).jcarousel());

        item
                .on('jcarouselcontrol:active', function() {
                    $('.' + navigation[i].className.substring(29)).jcarousel().jcarousel('scrollIntoView', this);
                    item.addClass('active');
                })
                .on('jcarouselcontrol:inactive', function() {
                    item.removeClass('active');
                })
                .jcarouselControl({
                    target: target,
                    carousel: $('.' + stage[i].className.substring(24)).jcarousel()
                });
    });
}
var text_gen = '';
var text_inf = '';
$(function() {
    for (var i = 0; i < stage.length; i++) {
        init(i)
    }

    var jcarousel = $('.jcarousel');

    jcarousel.on('jcarousel:reload jcarousel:create', function() {
        var width = jcarousel.innerWidth();
        jcarousel.jcarousel('items').css('width', width + 'px');
    })
            .jcarousel({
                wrap: 'circular'
            });


    // Setup controls for the stage carousel
    $('.prev-stage')
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .jcarouselControl({
                target: '-=1'
            });

    $('.next-stage')
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .jcarouselControl({
                target: '+=1'
            });

    // Setup controls for the navigation carousel
    $('.prev-navigation')
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .jcarouselControl({
                target: '-=1'
            });

    $('.next-navigation')
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .jcarouselControl({
                target: '+=1'
            });

    $('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

    $('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });

    $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 6,
                item: function(page, carouselItems) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });

    $('.simple_carusel .jcarousel-pagination').jcarouselPagination({
        perPage: 1,
        item: function(page, carouselItems) {
            return '<a href="#' + page + '">' + page + '</a>';
        }
    });

    $('.carousel-stage').height($('.carousel-stage img').eq(0).height());
    $('.connected-carousels').on('click', function() {
        var elem = $(this).find('.carousel-stage');
        var num = $(elem).parents('.connected-carousels').children('.navigation').children('.carousel-navigation').children('ul').find('li.active').index();
        $(elem).css('height', $(this).children('.stage').children('.carousel-stage').find('img').eq(num).css('height'));
//        console.log($(this).children('.stage').children('.carousel-stage').find('img').eq(num).css('height'));

    })
});


function open_album(id, tupe, d) {
    $('#' + tupe).hide();
    $('#uid_' + d + id).show();

    var elem = $('#uid_' + d + id).find('.carousel-stage');
    var num = $(elem).parents('.connected-carousels').children('.navigation').children('.carousel-navigation').children('ul').find('li.active').index();
    $(elem).css('height', $(elem).find('img').eq(num).css('height'));
}
$('.cart_title').click(function() {
    $(this).next().toggle();
    if ($(this).children('.toggle').attr('src') == '/images/toggle.png') {
        $(this).children('.toggle').attr('src', '/images/toggle_right.png');
        $(this).parent('div').css('padding-bottom', '25px');
    } else {
        $(this).children('.toggle').attr('src', '/images/toggle.png');
        $(this).parent('div').css('padding-bottom', '54px');
    }

});
$('.connected-carousels .prev-stage img').hover(
        function() {
            $(this).attr('src', '/images/arrows/no-opacity_l.png')
        },
        function() {
            $(this).attr('src', '/images/arrows/opacity_l.png')
        }
);
$('.connected-carousels .next-stage img').hover(
        function() {
            $(this).attr('src', '/images/arrows/no-opacity_r.png')
        },
        function() {
            $(this).attr('src', '/images/arrows/opacity_r.png')
        }
);
$('.connected-carousels .carousel-navigation li').hover(
        function() {
            $(this).append('<img id="mask" style="position: absolute;top: 0;left: -5px; width: 90px;height: 62px" src="/images/mask.png">')
        },
        function() {
            $('#mask').remove();
        }
);

if(location.hash)
    $(location.hash).parent('.cart_title').trigger('click');