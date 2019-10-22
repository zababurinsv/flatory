// smoth scroll by anchor
$(function () {
    $('a[href*=#]:not([href=#])').click(function () {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            var target = $(this.hash), h = this.hash;
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 1000);
                location.hash = h;
                return false;
            }
        }
    });
});
// jcarousel fix - vertical-alight = middle for small images
$('.fl-gallery .jcarousel img').each(function () {
    var d = $(this).parents('.jcarousel').height() % $(this).height();
    if (d > 5)
        $(this).css({"margin-top": d / 2 + 'px'});
});
// js-tabs
$('.js-tabs-nav a').off('click').on('click', function (e) {
    var tabNavEl = $(this).parent('li').length ? $(this).parent('li') : $(this);
    var group = $(this).data('tab-group'), tab = $(this).data('tab'), tabContent;
    // rm active tab
    $(this).parents('.js-tabs-nav').find('.active').removeClass('active');
    $('.js-tabs-content[data-tab-group="' + group + '"]').removeClass('active');
    tabContent = $('.js-tabs-content[data-tab-group="' + group + '"][data-tab="' + tab + '"]');

    if (!tabContent.length) {
        console.log('Tab "' + tab + '" content for group "' + group + '" not found!');
        return false;
    }

    tabContent.addClass('active');
    tabNavEl.addClass('active');

});

