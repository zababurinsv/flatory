var FlPagination = (function () {
    var app = {
        stack: [],
        config: {
            prev: false,
            next: true,
            current: 1,
            total: 0,
            active: 'current',
            url: 'javascript:void(0)',
            limit: 20
        },
        attrs: {class: 'pagination'},
        init: function () {
            app.setUpListeners();
        },
        setUpListeners: function () {
        },
        /**
         * Set config
         * @param {object} config
         * @returns {undefined}
         */
        _setConfig: function (config) {
            for (var k in config) {
                if (this.config[k] !== undefined)
                    this.config[k] = config[k];
            }
        },
        /**
         * Set attrs
         * @param {object} config
         * @returns {undefined}
         */
        _setAttr: function (config) {
            for (var k in config) {
                if (this.attrs[k] !== undefined)
                    this.attrs[k] = config[k];
            }
        },
        /**
         * create pagination
         * @returns {undefined}
         */
        _create: function () {
            // clear stack
            this.stack = [];
            var lastPage = Math.ceil(this.config.total / this.config.limit);

            if (this.config.current === -1) {
                this.config.current = 1;
                this.config.active = 'total';
            } else {
                this.config.active = 'current';
            }


            if (this.config.prev || this.config.current > 1)
                this._createItem('<span>&laquo;</span>', this.config.url, {pg: 1});

            if (this.config.current - 3 >= 1)
                this._createItem(this.config.current - 3, this.config.url, {pg: this.config.current - 3});

            if (this.config.current - 2 >= 1)
                this._createItem(this.config.current - 2, this.config.url, {pg: this.config.current - 2});

            if (this.config.current - 1 >= 1)
                this._createItem(this.config.current - 1, this.config.url, {pg: this.config.current - 1});
            // current page
            this._createItem(this.config.current, this.config.url, {pg: this.config.current}, this.config.active === 'current');

            if (this.config.current + 1 <= lastPage)
                this._createItem(this.config.current + 1, this.config.url, {pg: this.config.current + 1});

            if (this.config.current + 2 <= lastPage)
                this._createItem(this.config.current + 2, this.config.url, {pg: this.config.current + 2});

            if (this.config.next && this.config.current !== lastPage)
                this._createItem('<span>&raquo;</span>', this.config.url, {pg: lastPage});

            if (this.config.total)
                this._createItem('Все ' + this.config.total, this.config.url, {pg: -1}, this.config.active === 'total');
        },
        /**
         * Create item
         * @param {string/$} content
         * @param {string} url
         * @param {bool} isActive
         * @returns {undefined}
         */
        _createItem: function (content, url, dataAttr, isActive) {
            var link = $('<a>', {href: url, html: content});
            var itemParams = {html: link};
            if (isActive)
                itemParams.class = 'active';
            for (var k in dataAttr)
                itemParams['data-' + k] = dataAttr[k];
            var item = $('<li>', itemParams);
            this.stack.push(item);
        },
        selectLimit: function () {

            var el = $('<select>', {name: 'pagination_limit', class: 'pagination-limit'}), l = [10, 25, 50, 100], o;

            if ($.inArray(this.config.limit, l) === -1) {
                l.push(this.config.limit).sort(function (a, b) {
                    return a - b;
                });
            }

            for (var k in l) {
                o = $('<option>', {value: l[k], text: l[k]});
                if (l[k] === this.config.limit)
                    o.attr('selected', 'selected');
                el.append(o);
            }

            return el;
        },
        publ: {
            /**
             * Set pagination attributes
             * @param {object} attr
             * @returns {undefined}
             */
            setAttr: function (attr) {
                app._setAttr(attr);
            },
            /**
             * Render pagination
             * @param {object} config
             * @returns {_L1.app.publ.render.tpl}
             */
            render: function (config) {
                app._setConfig(config);
                app._create();

                var tpl = $('<ul>', app.attrs);
                for (var k in app.stack)
                    tpl.append(app.stack[k]);

                var nav = $('<nav>', {class: 'nav-pagination', 'aria-label': 'Page navigation'});
                nav.append(app.selectLimit()).append(tpl);
                return nav;
            }
        }
    };
    app.init();
    return app.publ;
}());