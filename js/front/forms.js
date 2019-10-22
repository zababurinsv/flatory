(function (global, window, $, undefined) {
    var th = this;
    global.rangeSliderInit = function (selector, valMin, valMax, step, onchange) {
        var step = step === undefined ? 1 : step;
        var rangeSlider = $(selector).find('.range-slider');
        $(rangeSlider).each(function () {
            $(this).find('.range-slider-bar').slider({
                range: true,
                min: valMin,
                max: valMax,
                step: step,
                values: [valMin, valMax],
                slide: function (event, ui) {
                    if (typeof onchange === "function") {
                        onchange(event, ui, rangeSlider);
                    }
                    $(rangeSlider).find('.range-min input').val(Number(ui.values[ 0 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")));
                    $(rangeSlider).find('.range-max input').val(Number(ui.values[ 1 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")));
                }
//                change: function(event, ui) {
//                    if (typeof onchange == "function") {
//                        onchange(event, ui, rangeSlider);
//                    }
//                    
//                    console.log(ui.values[ 1 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));
//                    $(rangeSlider).find('.range-min input').val(ui.values[ 0 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));
//                    $(rangeSlider).find('.range-max input').val(ui.values[ 1 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));
//                }
            });
            $(this).find('.range-min input').val($(rangeSlider).find('.range-slider-bar').slider("values", 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));
            $(this).find('.range-max input').val($(rangeSlider).find('.range-slider-bar').slider("values", 1).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));
        });

        $(rangeSlider).find('.range-slider-val input').change(function () {
            var newMin = $(rangeSlider).find('.range-min input').val().toString().replace(/\s/g, '');
            var newMax = $(rangeSlider).find('.range-max input').val().toString().replace(/\s/g, '');
            $(rangeSlider).find('.range-slider-bar').slider('values', [Number(newMin), Number(newMax)]);
        });
    };
    global.rangeSliderValues = function (selector) {
        var slider = $(selector).find('.range-slider .range-slider-bar');
        if (typeof arguments[1] !== 'undefined') {
            if (typeof arguments[1] === 'object') {
                slider.slider('values', arguments[1]);
            } else if (typeof arguments[2] !== 'undefined') {
                slider.slider('values', arguments[1], arguments[2]);
            } else {
                return slider.slider("values", arguments[1]);
            }
        } else {
            return slider.slider("values");
        }
    };
    global.rangeSliderReset = function (selector) {
        $(selector).each(function () {
            var slider = $(this).find('.range-slider .range-slider-bar');
            slider.slider('values', 0, slider.slider("option", "min"));
            slider.slider('values', 1, slider.slider("option", "max"));
        });
    };
    global.rangeSliderDisable = function (selector) {
        $(selector).each(function () {
            var slider = $(this).find('.range-slider .range-slider-bar');
            slider.slider('disable');
        });
    };
    global.rangeSliderEnable = function (selector) {
        $(selector).each(function () {
            var slider = $(this).find('.range-slider .range-slider-bar');
            slider.slider('enable');
        });
    };
})(this, window, jQuery);

/**
 * Cache LS
 * @returns {app.publ|CacheLs.app.publ}
 */
var FlCache = (function () {
    var app = {
        ls: false,
        cacheVar: 'FlCache',
        cacheTimeVar: 'FlCacheLive',
        cache: {},
        cacheLive: {},
        liveTime: {
            start: 0,
            end: 0
        },
        init: function () {
            // check LS
            this.ls = this.publ.isLocalStorage();

            if (!this.ls)
                return false;

            // get data from LS to memory
            this._updateCache();
            // drop old cache
            this._dropOld();
        },
        /**
         * get data from LS to memory
         * @returns {undefined}
         */
        _updateCache: function () {
            var lsData = JSON.parse(localStorage.getItem(app.cacheVar));
            app.cache = lsData === null ? {} : lsData;
            var lsDataLive = JSON.parse(localStorage.getItem(app.cacheTimeVar));
            app.cacheLive = lsDataLive === null ? {} : lsDataLive;
        },
        /**
         * Set time of live cache
         * @param {string} varName
         * @param {int} limitSecond
         * @returns {undefined}
         */
        _setLiveTime: function (varName, limitSecond) {
            var d = new Date();
            var start = d.getTime();
            var self = this.liveTime;
            self.start = start;
            self.end = start + Number(limitSecond) * 1000;
            // set in LS
            app.cacheLive[varName] = self;
            localStorage.setItem(app.cacheTimeVar, JSON.stringify(app.cacheLive));
        },
        /**
         * Check is actual cache var
         * @param {string} varName
         * @returns {Boolean}
         */
        _isActualCache: function (varName) {
            var check = app.cacheLive[varName];
            // no var
            if (check === undefined)
                return false;

            var d = new Date();
            var cur = d.getTime();
            return (cur > check.end) ? false : true;
        },
        /**
         * Drop old cache
         * @returns {undefined}
         */
        _dropOld: function () {
            var withTtl = this.cacheLive;
            for (var k in withTtl) {
                if (this._isActualCache(withTtl[k]) === false) {
                    delete this.cache[withTtl[k]];
                    delete this.cacheLive[withTtl[k]];
                }
            }
            localStorage.setItem(this.cacheVar, JSON.stringify(this.cache));
            localStorage.setItem(this.cacheTimeVar, JSON.stringify(this.cacheLive));
        },
        /**
         * public methods
         */
        publ: {
            /**
             * Get cache var
             * @param {string} varName - cache index
             * @param {boolean} isCheckLive - is need check live of cache var
             * @returns {Boolean|app.cache}
             */
            get: function (varName, isCheckLive) {

                if (!app.ls)
                    return false;

                if (app.cache[varName] === undefined)
                    return false;
                // if need check live of cache var
                if (isCheckLive === true)
                    return app._isActualCache(varName) ? app.cache[varName] : false;

                return app.cache[varName];
            },
            /**
             * Set cache var
             * @param {type} varName - cache index
             * @param {all} content - content cache
             * @param {int} limitSecond - seconds for live cache
             * @returns {undefined}
             */
            set: function (varName, content, limitSecond) {
                if (!app.ls)
                    return false;
                // set live time
                if (limitSecond !== undefined)
                    app._setLiveTime(varName, limitSecond);
                // add in cache & sve in LS
                app.cache[varName] = content;
                localStorage.setItem(app.cacheVar, JSON.stringify(app.cache));
            },
            /**
             * Drop all cache
             * @returns {undefined}
             */
            dropAll: function () {
                if (!app.ls)
                    return false;

                localStorage.removeItem(app.cacheVar);
                localStorage.removeItem(app.cacheTimeVar);
            },
            /**
             * Clear Local Storage cache
             * @deprecated use dropAll
             * @returns {undefined}
             */
            clear: function () {
                if (!app.ls)
                    return false;

                localStorage.removeItem(app.cacheVar);
                this.dropAll();
            },
            /**
             * Check is localStorage available
             * @returns {Boolean}
             */
            isLocalStorage: function () {
                try {
                    var cacheIndex = app.cacheVar + '_check';
                    localStorage.setItem(cacheIndex, cacheIndex);
                    localStorage.removeItem(cacheIndex);
                    return true;
                } catch (e) {
                    return false;
                }
            }
        }
    };
    app.init();
    return app.publ;
}());

var FlHelper = (function () {
    var app = {
        init: function () {
            app.setUpListeners();
        },
        setUpListeners: function () {

        },
        publ: {
            /**
             * Get uri params
             * @returns {object}
             */
            GET: function () {
                var g = decodeURI(location.search),
                        result = {};
                if (g === '')
                    return result;
                // разделяем переменные
                var tmp = (g.substr(1)).split('&');
                for (var i = 0; i < tmp.length; i++) {
                    var t = tmp[i].split('=');
                    // пары ключ(имя переменной)->значение
                    var key = t[0];
                    var value = t[1];
                    // определяем массивы в запросе ( key[] )
                    // @todo определять индекс массива
                    if (key.match('\\[\\]')) {
                        // это не первый элемент массива
                        if (result[key] !== undefined) {
                            if (typeof result[key] !== 'object') {
                                var first = result[key];
                                result[key] = [first, value];
                            } else {
                                result[key].push(value);
                            }
                        } else {
                            result[key] = [value];
                        }
                    } else {
                        result[key] = value === undefined ? '' : value;
                    }

                }
                return result;
            },
            /**
             * @deprecated
             * @returns {String|Array}
             */
            Get: function () {
                // @todo 
//                var g = decodeURI(location.search),
                var g = decodeURIComponent(location.search),
                        result = {};

                if (g === '')
                    return result;
                // разделяем переменные
                var tmp = (g.substr(1)).split('&');
                for (var i = 0; i < tmp.length; i++) {

                    var eqPos = tmp[i].indexOf('=');
                    // пары ключ(имя переменной)->значение
                    var key = tmp[i].substring(0, eqPos);
                    var value = tmp[i].substring(eqPos + 1);
                    // определяем массивы в запросе ( key[] )
                    // @todo определять индекс массива
                    if (key.match('\\[\\]')) {
                        // это не первый элемент массива
                        if (result[key] !== undefined) {
                            if (typeof result[key] !== 'object') {
                                var first = result[key];
                                result[key] = [first, value];
                            } else {
                                result[key].push(value);
                            }
                        } else {
                            result[key] = [value];
                        }
                    } else {
                        result[key] = value === undefined ? '' : value;
                    }

                }
                return result;
            },
            arr: {
                get: function (arr, item, defaul) {
                    return  arr[item] !== undefined ? arr[item] : defaul;
                },
                /**
                 * Create uri from array/obj
                 * @param {array/obj} arr
                 * @returns {String}
                 */
                toUri: function (arr) {
                    var uri = new Array();
                    for (var key in arr) {
                        uri.push(key + '=' + arr[key]);
                    }
                    uri = uri.join('&');
                    return uri.length > 0 ? '?' + uri : '';
                },
                /**
                 * Array ecept
                 * @param {object} arr
                 * @param {object/array} arrExept
                 * @returns {object}
                 * 
                 * @example FlHelper.arr.except({name: 'test', val: 123}, ['val']); // Object {name: "test"}
                 */
                except: function (arr, arrExept) {
                    if (typeof arr !== 'object' || typeof arrExept !== 'object')
                        throw Error('Arguments must be objects or arrays!');

                    for (var k in arrExept) {
                        delete arr[arrExept[k]];
                    }
                    return arr;
                },
                /**
                 * Array sum 
                 * @param {array/object(simple)} arr
                 * @returns {Number}
                 */
                sum: function (arr) {
                    var sum = 0;
                    for (var i in arr) {
                        sum += arr[i];
                    }
                    return  sum;
                },
                /**
                 * unique array
                 * @param {array} a
                 * @returns {array}
                 */
                arrayUnique: function (a) {
                    return a.reduce(function (p, c) {
                        if (p.indexOf(c) < 0)
                            p.push(c);
                        return p;
                    }, []);
                },
                /**
                 * get length of array or object
                 * @param {object} a
                 * @returns {Number}
                 */
                count: function (a) {
                    a = typeof a !== 'object' ? {} : a;
                    return $.map(a, function (n, i) {
                        return i;
                    }).length;
                },
                /**
                 * Get key by value
                 * @param {object} obj
                 * @param {string} value
                 * @returns {string}
                 */
                getKeyByValue: function (obj, value) {
                    return Object.keys(obj).filter(function (key) {
                        return obj[key] === value;
                    })[0];
                }
            },
            /**
             * Parse date like php date()
             * @param {string} format - 'd.m.Y'
             * @param {string/object/undefined} date - '2015-03-25' || new Date() - default
             * @returns {string}
             * @todo more formats marks
             */
            date: function (format, date) {
                var d = date !== undefined ? new Date(date) : new Date();

                var f = {
                    d: d.getDate() < 10 ? "0" + d.getDate() : d.getDate(),
                    m: d.getMonth() + 1 < 10 ? "0" + (d.getMonth() + 1) : d.getMonth() + 1,
                    Y: d.getFullYear(),
                    H: d.getHours() < 10 ? "0" + d.getHours() : d.getHours(),
                    i: d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes(),
                    s: d.getSeconds() < 10 ? "0" + d.getSeconds() : d.getSeconds()
                };

                var parse = function () {
                    if (f[arguments[0]])
                        return f[arguments[0]];
                };

                return  format.replace(/(d+)|(m+)|(Y+)|(H+)|(i+)|(s+)/g, parse);
            },
            stripTags: function (str) {
                return str.replace(/<\/?[^>]+>/gi, '');
            },
            numberFormat: function (number, decimals, dec_point, thousands_sep) {

                number = (number + '')
                        .replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                        s = '',
                        toFixedFix = function (n, prec) {
                            var k = Math.pow(10, prec);
                            return '' + (Math.round(n * k) / k)
                                    .toFixed(prec);
                        };
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                        .split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '')
                        .length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1)
                            .join('0');
                }
                return s.join(dec);
            },
            num: {
                /**
                 * Number format
                 * @param {int/string} n - value
                 * @param {int} dl - length of decimal
                 * @param {string} sd - sections delimiter
                 * @param {string} dd - decimal delimiter
                 * @returns {string}
                 */
                numberFormat: function (n, dl, sd, dd) {
                    if (isNaN(Number(n)) || n === null)
                        return n;

                    dl = isNaN(Number(dl)) ? 0 : Number(dl);
                    sd = typeof sd === 'undefined' ? ' ' : sd;
                    dd = typeof dd === 'undefined' ? '.' : dd;
                    n = Number(n).toFixed(dl).replace(/./g, function (c, i, a) {
                        return i > 0 && c !== '.' && (a.length - i) % 3 === 0 ? sd + c : c;
                    });

                    return dd !== '.' ? n.replace('.', dd) : n;
                },
                /**
                 * decorate file size
                 * @see http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable
                 * @param {int} bytes
                 * @param {bool} si
                 * @returns {String}
                 */
                humanFileSize: function (bytes, si) {
                    var thresh = si ? 1000 : 1024;
                    if (Math.abs(bytes) < thresh) {
                        return bytes + ' B';
                    }
                    var units = si
                            ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
                            : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
                    var u = -1;
                    do {
                        bytes /= thresh;
                        ++u;
                    } while (Math.abs(bytes) >= thresh && u < units.length - 1);
                    return bytes.toFixed(1) + ' ' + units[u];
                }
            },
            str: {
                /**
                 * truncate string by limit with ...
                 * @param {string} str
                 * @param {num} limit
                 * @returns {String}
                 */
                truncate: function (str, limit) {
                    return str.length >= limit ? str.substring(0, limit) + '...' : str;
                }
            },
            /**
             * @see https://learn.javascript.ru/keyboard-events
             * @param {object} event
             * @returns {String}
             */
            getKeypressChar: function (event) {
                if (event.which == null) { // IE
                    if (event.keyCode < 32)
                        return null; // спец. символ
                    return String.fromCharCode(event.keyCode);
                }

                if (event.which != 0 && event.charCode != 0) { // все кроме IE
                    if (event.which < 32)
                        return null; // спец. символ
                    return String.fromCharCode(event.which); // остальные
                }

                return null; // спец. символ
            }
        }
    };
    app.init();
    return app.publ;
}());

var FlRegister = (function () {
    var app = {
        _register: {
        },
        publ: {
            get: function (varName) {
                if (app._register[varName] === undefined)
                    return false;
                return app._register[varName];
            },
            set: function (varName, content) {
                app._register[varName] = content;
            },
            getAll: function () {
                return app._register;
            }
        }
    };
    return app.publ;
}());

/*!
 * jQuery Tiny Pub/Sub - v0.3 - 11/4/2010
 * http://benalman.com/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function ($) {

    var o = $({});

    $.subscribe = function () {
        o.bind.apply(o, arguments);
    };

    $.unsubscribe = function () {
        o.unbind.apply(o, arguments);
    };

    $.publish = function () {
        o.trigger.apply(o, arguments);
    };

})(jQuery);

/**
 * Form builder
 * @type Function|@exp;app@pro;publ
 */
var FlForm = (function () {
    var app = {
        init: function () {
            app.setUpListeners();
        },
        setUpListeners: function () {
        },
        publ: {
            /**
             * Отобразить ошибки формы
             * @param {object} fieldErrors - объект полей с ошибками {field_name: "error text"}
             * @param {string} target - selector
             * @returns {undefined}
             */
            errors: function (fieldErrors, target) {
                target = target === undefined ? $('body') : $(target);
                if (typeof fieldErrors === 'object') {
                    for (var k in fieldErrors) {
                        var field = target.find('[name="' + k + '"]');
                        field.parents('.form-group').addClass('has-error');

                        var message = fieldErrors[k];

                        if (typeof message === 'object' && message.error)
                            message = message.error;

                        if (field.parent('div').hasClass('input-group'))
                            field = field.parent('.input-group');

                        field.after($('<small>', {
                            'class': 'text-danger form__errors_field',
                            text: message
                        }));
                    }
                }
            },
            /**
             * Сбросить все ошибки
             * @returns {undefined}
             */
            dropErrors: function () {
                $('.form__errors_field').parents('.form-group').removeClass('has-error');
                $('.form__errors_field').remove();
            },
            /**
             * Получить данные формы
             * @param {string / $} selector - $
             * @returns {object}
             */
            getFormData: function (selector) {
                var arr = $(selector).serializeArray(), result = {}, tmp;
                for (var k in arr) {
                    if (result[arr[k].name]) {
                        if (typeof result[arr[k].name] === 'object') {
                            result[arr[k].name].push(arr[k].value);
                        } else {
                            tmp = result[arr[k].name];
                            result[arr[k].name] = [result[arr[k].name], arr[k].value];
                        }

                    } else {
                        result[arr[k].name] = arr[k].value;
                    }

                }
                return result;
            },
            /**
             * Toggle global errors
             * @param {object} errors
             * @param {string/$} target - target form selector - parent .error_place
             * @returns {undefined}
             */
            toggleGlobalErrors: function (errors, target) {
                target = target === undefined ? $('body') : $(target);
                var place = target.find('.error_place'), message;
                place.find('ul').empty();
                place.hide();
                // if not empty errors - render
                if (!$.isEmptyObject(errors)) {
                    for (var k in errors) {
                        message = errors[k];
                        if (typeof message === 'object' && message.error)
                            message = message.error;
                        place.find('ul').append($('<li>', {text: message}));
                    }

                    place.show();
                }
            },
            /**
             * 
             * @param {type} fields = {fieldName: {value: "", error: ''}}
             * @param {type} target
             * @returns {undefined}
             */
            fillForm: function (fields, target) {
                target = target === undefined ? $('body') : $(target);
                var el, tag;
                for (var k in fields) {
                    el = target.find('[name^="' + k + '"]');
                    if (el.length && fields[k] !== null) {
                        tag = el.prop("tagName");
                        switch (tag) {
                            case 'SELECT':
                                if (el[0].multiple) {
                                    // multiple select
                                    if (fields[k].value === undefined && typeof fields[k] === 'object') {
                                        for (var it in fields[k]) {
                                            if (fields[k][it].value !== undefined)
                                                el.find('[value="' + fields[k][it].value + '"]').attr('selected', 'selected');
                                        }
                                    } else {
                                        // @todo 
                                    }
                                } else {
                                    if (typeof fields[k] === 'object' && typeof fields[k].value !== 'undefined')
                                        el.find('[value="' + fields[k].value + '"]').attr('selected', 'selected');
                                    else
                                        el.find('[value="' + fields[k] + '"]').attr('selected', 'selected');
                                }
                                break;
                            case 'INPUT':
                                switch (el.attr('type')) {
                                    case 'text':
                                    case 'email':
                                        if (typeof fields[k] === 'object' && typeof fields[k].value !== 'undefined')
                                            el.val(fields[k].value);
                                        else
                                            el.val(fields[k]);
                                        break;
                                    case 'checkbox':
                                        if (Number(fields[k].value) !== 0)
                                            el.prop('checked', true);
                                        break;
                                }
                                break;
                            case 'TEXTAREA':
                                if (typeof fields[k] === 'object' && typeof fields[k].value !== 'undefined')
                                    el.val(fields[k].value);
                                else
                                    el.val(fields[k]);
                                break;
                        }


                    }
                }
            }

        }
    };
    app.init();
    return app.publ;
}());