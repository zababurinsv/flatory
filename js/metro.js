;
var FlMetro = function(selectorPlace) {

    var _conf = {
        place: {
            width: '100%',
            position: 'relative'
        },
        page: {
            width: '100%',
            height: 'auto',
            src: '/images/metro/mos.jpg'
        },
        marker: {
            padding: '2px',
            background: 'rgba(255,235,61,0.3)',
            border: '1px dashed #444',
            position: 'absolute',
            width: '20px',
            height: '12px',
            'min-width': '20px',
            'min-height': '10px',
            top: '10%',
            left: '10%'
        },
        point: {
            width: '15px',
            height: '15px',
            position: 'absolute',
            top: '10%',
            left: '9%',
            'border-radius': '50%',
            background: 'rgba(255,235,61,0.3)',
            border: '1px dashed #444'
        },
        nav: {
        }
    },
    self = this;

    /**
     * replace config - return first config with replece by second config
     * @param {object} first
     * @param {object} second
     * @returns {object}
     */
    function _replace_config(first, second) {
        if (typeof first !== 'object')
            first = {};

        if (typeof second !== 'object')
            return first;

        for (var k in first) {
            if (second.hasOwnProperty(k) && typeof first[k] === typeof second[k])
                first[k] = second[k];
        }

        return first;
    }

    function _createplace() {
        var m = $('<div>', {'data-metro': 'place'}),
                img = $('<img>', {
                    'data-metro': 'background',
                    src: _conf.page.src
                }).css({
            width: _conf.page.width,
            height: _conf.page.height
        });

        $(m).css(_conf.place);
        $(m).append(img);
        return m;
    }

    this.addMarker = function(config) {

        if ($(self.marker).length || !$(self.place).length)
            return;

        var marker = $('<div>', {'data-metro': 'marker'})
                .css(_replace_config(_conf.marker, config));
        $(marker).draggable().resizable();
        self.marker = marker;
        $(self.place).append(self.marker);
        return marker;
    };

    this.addPoint = function(config) {
        if (!$(self.place).length)
            return;

        var point = $('<div>', {'data-metro': 'point'})
                .css(_replace_config(_conf.point, config));
        $(point).draggable();
        if (!$.isArray(self.points))
            self.points = [point];
        else
            self.points.push(point);

        $(self.place).append(point);
        return point;
    };

    this.renderNav = function(config, list) {
        if (!$(self.place).length)
            return;

        var nav = $('<div>', {'data-metro': 'nav'})
                .css(_replace_config(_conf.nav, config)),
                defaultNavs = {
                    addPoint: $('<button>', {type: 'button', class: 'btn btn-xs btn-default', text: 'Добавить точку'}),
                    addMarker: $('<button>', {type: 'button', class: 'btn btn-xs btn-default', text: 'Добавить маркер'})
                }, callback;

        list = _replace_config(defaultNavs, list);

        for (var k in list) {
            nav.append(list[k]);

            if (typeof self[k] === 'function') {
                $(list[k]).off('click').on('click', self[k]);
            }
        }

        self.nav = nav;
        $(self.place).before(self.nav);
        return nav;
    };

    function _buildElementState(el) {
        return {
            width: el.outerWidth() ? el.outerWidth() + 'px' : null,
            height: el.outerHeight() ? el.outerHeight() + 'px' : null,
            top: el.css('top'),
            left: el.css('left'),
            topPercents: Number(el.css('top').split('px').join('')) * 100 / $(self.place).height(),
            leftPercents: Number(el.css('left').split('px').join('')) * 100 / $(self.place).width()
        };
    }

    this.getStateMarker = function() {
        return _buildElementState($(self.marker));
    };

    this.getStatePoints = function() {
        var v = [];
        for (var k in self.points) {
            v.push(_buildElementState($(self.points[k])));
        }
        return v;
    };

    this.dropPoints = function (){
        self.points = [];
        $(self.place).find('[data-metro="point"]').remove();
    };
    
    function _init() {
        if (!$(selectorPlace).length)
            throw new Error('Can\'t create metro. Place not found!');

        self.place = _createplace();
        $(selectorPlace).prepend(self.place);
    }

    _init();
};