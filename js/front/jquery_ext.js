;
/**
 * Content by columns
 * Flatory.ru
 * @param {int} numColumns
 * @return {jQuery|@pro;window@pro;$|Window.$}
 */
$.fn.byColumns = function (numColumns) {

    var n = isNaN(Number(numColumns)) ? 2 : Number(numColumns);
    var view = $('<div>', {'class': 'js-by-colomns-list'}), it, colWidth = 100 / n, cols = {};

    if (!$(this).length)
        return $();

    for (var k = 1; k <= n; k++) {
        it = $('<div>', {'data-col': k});
        it.css({width: colWidth + '%', 'float': 'left'});
        cols[k] = it;
    }

    $(this).each(function (a, b) {
        var c = (a + 1) > n ? (((a + 1) % n) === 0 ? n : (a + 1) % n) : (a + 1);

        if (typeof cols[c] !== 'undefined') {
            $(cols[c]).append($(this).clone());
        }
    });

    for (var k in cols) {
        $(view).append($(cols[k]));
    }

    view.prependTo($(this).parent('div'));

    // remove this
    $(this).remove();

    return $(this).parent('div').find('.js-by-colomns-list');
};
/**
 * Panel with tabs
 * @return {undefined}
 */
$.fn.panelTabs = function () {
    var app = {
        _init: function (panel) {
            this.panel = panel;
            this.tabs = $(panel).find('.nav-tabs');

            if (!this.tabs.length)
                return false;

            this._refresh();
            this._show();
            this._listener();
            return true;
        },
        _refresh: function () {
            this.sections = $(this.tabs).find('.active a').data('tab').split(',');
            this.title = $(this.tabs).find('.active a').attr('title');
            this.group = $(this.tabs).data('tab-group');
        },
        _listener: function () {
            $(this.tabs).find('a').on('click', app._click);

        },
        _click: function (e) {
            if ($(this).parent('li').hasClass('active'))
                return;
            app.tabs.find('.active').removeClass('active');
            $(this).parent('li').addClass('active');
            app._refresh();
            app._show();
        },
        _show: function () {
            
            $(this.panel).find('[data-tab-group="'+ this.group +'"][data-tab-content]').hide();
            for(var k in this.sections){
                $(this.panel).find('[data-tab-group="'+ this.group +'"][data-tab-content="'+ this.sections[k] +'"]').show();
            }
            
            $(this.panel).find('[data-tab-title]').text(this.title);
        }
    };

    app._init($(this));
};