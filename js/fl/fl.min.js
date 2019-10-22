/**
 * Cache LS
 * @returns {app.publ|CacheLs.app.publ}
 */
var FlCache = (function () {
    var app = {
        cacheVar: 'FlCache',
        cacheTimeVar: 'FlCacheLive',
        cache: {},
        cacheLive: {},
        liveTime: {
            start: 0,
            end: 0
        },
        init: function () {
            this._updateCache();
            // drop old cache
            this._dropOld();
        },
        // get data from LS
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
        publ: {
            /**
             * Get cache var
             * @param {string} varName - cache index
             * @param {boolean} isCheckLive - is need check live of cache var
             * @returns {Boolean|app.cache}
             */
            get: function (varName, isCheckLive) {
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
                localStorage.removeItem(app.cacheVar);
                localStorage.removeItem(app.cacheTimeVar);
            }
        }
    };
    app.init();
    return app.publ;
}());

/**
 * Helper
 * @type Function|@exp;app@pro;publ
 */
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
             * @param {string} param - get param from GET
             * @returns {mixed} - object (without arg param), string (with param)
             */
            GET: function (param) {
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
                return typeof param === 'string' && param ? result[param] : result;
            },
            arr: {
                /**
                 * 
                 * @param {type} arr
                 * @param {type} item
                 * @param {type} defaul
                 * @returns {_L221.app.publ.arr.get.arr}
                 */
                get: function (arr, item, defaul) {
                    if (typeof arr !== 'object')
                        return false;
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
                },
                /**
                 * clone object
                 * @param {mixed} obj
                 * @returns {unresolved}
                 */
                clone: function (obj) {
                    if (obj === null || typeof obj !== 'object') {
                        return obj;
                    }

                    // give temp the original obj's constructor
                    var temp = typeof obj.constructor === 'function' ? obj.constructor() : {};
                    for (var key in obj) {
                        temp[key] = this.clone(obj[key]);
                    }

                    return temp;
                },
                /**
                 * Exchanges all keys with their associated values in an array
                 * @param {object} arr
                 * @returns {object}
                 */
                flip: function (arr) {
                    var key, tmp_ar = {};
                    for (key in arr) {
                        tmp_ar[arr[key]] = key;
                    }
                    return tmp_ar;
                },
                /**
                 * equal php array_values
                 * @param {object} arr
                 * @returns {Array}
                 */
                values: function (arr) {
                    var result = [];
                    for (var k in arr) {
                        result.push(arr[k]);
                    }
                    return result;
                },
                dynamicSort: function (property) {
                    var sortOrder = 1;
                    if (property[0] === "-") {
                        sortOrder = -1;
                        property = property.substr(1);
                    }
                    return function (a, b) {
                        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
                        return result * sortOrder;
                    };
                }
            },
            /**
             * Parse date like php date()
             * @param {string} format - 'd.m.Y'
             * @param {string/object/undefined} date - '2015-03-25' || new Date() - default
             * @param {bool} isStrict - default = false
             * @returns {string}
             * @todo more formats marks
             */
            date: function (format, date, isStrict) {

                if (isStrict === true && !date)
                    return '';

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
            /**
             * add script in head
             * @param {string} fileUrl - url 
             * @param {string} scriptType - 'script' or 'link', default - 'script'
             * @returns {undefined}
             */
            headAddScript: function (fileUrl, scriptType) {
                scriptType = $.inArray(scriptType, ['script', 'link']) === -1 ? 'script' : scriptType;
                var node = document.createElement(scriptType);
                node.async = false;
                if (scriptType === 'script')
                    node.src = fileUrl;
                else
                    node.href = fileUrl;
                $('head')[0].appendChild(node);
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
                    if (typeof str !== 'string')
                        return str;
                    return str.length >= limit ? str.substring(0, limit) + '...' : str;
                }
            }
        }
    };
    app.init();
    return app.publ;
}());

/**
 * Register
 * @type Function|@exp;app@pro;publ
 */
var FlRegister = (function () {
    var app = {
        init: function () {
            // define hostname & pathname
            var _suffix = '/' + location.pathname.split('/')[1];
            var _location = {};

            if (typeof Object === 'function' && typeof Object.defineProperties === 'function') {
                Object.defineProperties(_location, {
                    hostname: {
                        value: location.hostname + _suffix,
                        writable: false
                    },
                    pathname: {
                        value: location.pathname.split(_suffix).join(''),
                        writable: false
                    },
                    origin: {
                        value: location.protocol + '//' + location.hostname + _suffix,
                        writable: false
                    },
                    suffix: {
                        value: _suffix,
                        writable: false
                    },
                    assetsPath: {
                        value: '/assets',
                        writable: false
                    }
                });
                Object.defineProperties(this.publ, {
                    location: {
                        value: _location,
                        writable: false
                    }
                });
            } else {
                app.publ.location = {
                    hostname: location.hostname + _suffix,
                    pathname: location.pathname.split(_suffix).join(''),
                    origin: location.protocol + '//' + location.hostname + _suffix
                };
            }
        },
        _register: {},
        publ: {
            get: function (varName, defaultValue) {
                if (app._register[varName] === undefined)
                    return defaultValue || false;
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
    app.init();
    return app.publ;
}());

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
                            class: 'text-danger form__errors_field',
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
                                            if (typeof fields[k][it].value !== 'undefined')
                                                el.find('[value="' + fields[k][it].value + '"]').attr('selected', 'selected');
                                            else
                                                el.find('[value="' + fields[k][it] + '"]').attr('selected', 'selected');
                                        }
                                    } else {
                                        
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
                                    case 'number':
                                    case 'email':
                                        if (typeof fields[k] === 'object' && typeof fields[k].value !== 'undefined')
                                            el.val(fields[k].value);
                                        else
                                            el.val(fields[k]);
                                        break;
                                    case 'checkbox':
                                           
                                        // list of values
                                        if($.isArray(fields[k])) {
                                            for(var kk in fields[k]){                                                
                                                el.filter('[value="'+ fields[k][kk] +'"]').prop('checked', true);
                                            }
                                        } else {
                                            // ???
                                            if (Number(fields[k].value) !== 0)
                                                el.prop('checked', true);
                                        }
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
                        
                        $(el).trigger('change');

                    }
                }
            }

        }
    };
    app.init();
    return app.publ;
}());

/**
 * Form validation
 * @type @exp;app@pro;publ|Function
 */
var FlValidation = (function () {   
    var app = {
        init: function () {
            this.setUpListeners();
        },
        setUpListeners: function () {

        },
        _validateByRule: function (data, rule) {
            if (typeof data === 'undefined')
                return false;
            // @todo
            if (typeof this.publ[rule] !== 'function') {
                alert('Error!');
                throw new Error('Validation rule "' + rule + '" not found!');
            }

            return this.publ[rule](data);
        },
        publ: {
            /**
             * Validate object
             * @todo callback or bool in rules
             * 
             * @param {obj} data - object to validate
             * @param {obj} rules - rules 
             * {fieldName: 'rule - method name'},
             * {fieldName: function(data){//validation}},
             * {fieldName: ['rule1 - method name', 'rule2 - method name']},
             * @param {bool} returnOnlyErrors - return only error array
             * @returns {obj} - returns only false fields {fieldName: false}
             * 
             * @example FlValidation.validate({field: 'http://url.com'}, {field: 'url'});
             */
            validate: function (data, rules, returnOnlyErrors) {
                returnOnlyErrors = returnOnlyErrors === undefined ? true : returnOnlyErrors;
                var rule, validated = {};
                for (var field in rules) {

                    rule = rules[field];

                    switch (typeof rule) {
                        // methods FlValidation
                        case 'string':
                            validated[field] = app._validateByRule(data[field], rule);
                            break;
                        case 'function':
                            validated[field] = rule(data[field]);
                            break;
                        case 'object':
                            for (var k in rule) {
                                if (!app._validateByRule(data[field], rule[k]))
                                    validated[field] = false;
                            }
                            break;
                    }
                }
                // filter validate obj
                if (returnOnlyErrors) {
                    for (var k in validated) {
                        if (validated[k] === true)
                            delete(validated[k]);
                    }
                }

                return validated;
            },
            /** ***************
             * Rules
             *  ***************
             */

            /**
             * Validate phone
             * @param {string/int} phone
             * @returns {Boolean}
             */
            phone: function (phone) {
                var reg = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{10,}$/;
                return reg.test(phone);
            },
            /**
             * Validate email
             * @param {string} email
             * @returns {Boolean}
             */
            email: function (email) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            },
            /**
             * Validate url
             * @param {string} url
             * @returns {Boolean}
             */
            url: function (url) {
                var reg = /^(ht|f)tps?:\/\/[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/;
                return reg.test(url);
            },
            /**
             * Validate required field
             * @param {string / int} val
             * @returns {Boolean}
             */
            notNull: function (val) {
                if (val !== undefined)
                    return Boolean(val);
                return false;
            },
            /**
             * Alias for notNull
             * @param {string / int} val
             * @returns {Boolean}
             */
            required: function (val) {
                return app.publ.notNull(val);
            },
            /**
             * is numeric
             * @param {mixed} val
             * @returns {Boolean}
             */
            is_numeric: function (val) {
                var reg = /(^\d+$)|(^\d+\.\d+$)/;
                return reg.test(val);
            },
            /**
             * is purse webmoney
             * @param {mixed} val
             * @returns {Boolean}
             */
            purse_webmoney: function (val) {
                // @todo
                return !!val;
            },
            /**
             * is purse yandex
             * @param {mixed} val
             * @returns {Boolean}
             */
            purse_yandex: function (val) {
                // @todo
                return !!val;
            },
            /**
             * is credit card number
             * @param {mixed} val
             * @returns {Boolean}
             */
            credit_card: function (val) {
                // @todo
                return !!val;
            },
            /**
             * is percent 0 - 100
             * @param {mixed} val
             * @returns {Boolean}
             */
            percent: function (val) {
                if (!this.is_numeric(val))
                    return false;

                return val >= 0 && val <= 100;
            }
        }
    };
    app.init();
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

