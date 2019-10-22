var inf = [], map = !1, mapAjaxUrl = "/maps/ajax.php", curCat = null, placeMarks = {}, clusterer = !1, mapInitFlg = !1, mapLoadFlg = !1, redIconUrl = "/i/map/marker_red.png", yandexFirmsKey = "92aedec7-3aff-4e0c-ab86-5b3317ad36bc", redIcon = {preset: "islands#redDotIcon", iconLayout: "default#image", iconImageHref: redIconUrl, iconImageSize: [47, 47], iconImageOffset: [-23, -43], zIndex: 750}, customIcon = {preset: "islands#blueDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_blue_1.png", iconImageSize: [47, 47], iconImageOffset: [-23,
        -43]}, roomsIcon = {0: {preset: "islands#grayDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_gray_0.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}, 1: customIcon, 2: {preset: "islands#orangeDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_orange_2.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}, 3: {preset: "islands#greenDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_green_3.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}, 4: {preset: "islands#pinkDotIcon",
        iconLayout: "default#image", iconImageHref: "/i/map/marker_pink_4.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}}, infrastructureIcon = {1: {preset: "islands#grayDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_gray_detsad.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}, 2: {preset: "islands#grayDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_gray_school.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}, 3: {preset: "islands#grayDotIcon", iconLayout: "default#image",
        iconImageHref: "/i/map/marker_gray_health.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}, 4: {preset: "islands#grayDotIcon", iconLayout: "default#image", iconImageHref: "/i/map/marker_gray_cafe.png", iconImageSize: [47, 47], iconImageOffset: [-23, -43]}};
ymaps.modules.define("PieChartClusterer.icon.colors", [], function(a) {
    a({blue: "#1E98FF", red: "#ED4543", darkOrange: "#E6761B", night: "#0E4779", darkBlue: "#177BC9", pink: "#F371D1", gray: "#B3B3B3", brown: "#793D0E", darkGreen: "#1BAD03", violet: "#B51EFF", black: "#595959", yellow: "#FFD21E", green: "#56DB40", orange: "#FF931E", lightBlue: "#82CDFF", olive: "#97A100"})
});
ymaps.modules.define("PieChartClusterer.icon.params", ["shape.Circle", "geometry.pixel.Circle"], function(a, b, d) {
    a({icons: {small: {size: [46, 46], offset: [-23, -23], shape: new b(new d([0, 2], 21.5))}, medium: {size: [58, 58], offset: [-29, -29], shape: new b(new d([0, 2], 27.5))}, large: {size: [71, 71], offset: [-35.5, -35.5], shape: new b(new d([0, 2], 34))}}, numbers: [10, 100]})
});
ymaps.modules.define("PieChartClusterer.component.Canvas", ["option.Manager", "PieChartClusterer.icon.colors"], function(a, b, d) {
    var c = function(a) {
        this._canvas = document.createElement("canvas");
        this._canvas.width = a[0];
        this._canvas.height = a[1];
        this._context = this._canvas.getContext("2d");
        this.options = new b({})
    };
    c.prototype.generateIconDataURL = function(a, b) {
        this._drawIcon(a, b);
        return this._canvas.toDataURL()
    };
    c.prototype._drawIcon = function(a, b) {
        var d = 0, c = 360, k = this._context, g = this._canvas.width / 2, n = this._canvas.height /
                2, l = this.options.get("canvasIconLineWidth", 2), p = this.options.get("canvasIconStrokeStyle", "white"), q = Math.floor((g + n - l) / 2);
        k.strokeStyle = p;
        k.lineWidth = l;
        Object.keys(a).forEach(function(l) {
            var p = a[l];
            c = d + 360 * p / b;
            k.fillStyle = this._getStyleColor(l);
            b > p ? d = this._drawSector(g, n, q, d, c) : this._drawCircle(g, n, q)
        }, this);
        this._drawCore(g, n)
    };
    c.prototype._drawCore = function(a, b) {
        var d = this._context, c = this.options.get("canvasIconCoreFillStyle", "white"), k = this.options.get("canvasIconCoreRadius", 23);
        d.fillStyle = c;
        this._drawCircle(a, b, k)
    };
    c.prototype._drawCircle = function(a, b, d) {
        var c = this._context;
        c.beginPath();
        c.arc(a, b, d, 0, 2 * Math.PI);
        c.fill();
        c.stroke()
    };
    c.prototype._drawSector = function(a, b, d, c, k) {
        var g = this._context;
        g.beginPath();
        g.moveTo(a, b);
        g.arc(a, b, d, this._toRadians(c), this._toRadians(k));
        g.lineTo(a, b);
        g.closePath();
        g.fill();
        g.stroke();
        return k
    };
    c.prototype._toRadians = function(a) {
        return a * Math.PI / 180
    };
    c.prototype._getStyleColor = function(a) {
        return d[a]
    };
    a(c)
});
ymaps.modules.define("PieChartClusterer", ["Clusterer", "util.defineClass", "util.extend", "PieChartClusterer.icon.params", "PieChartClusterer.component.Canvas"], function(a, b, d, c, e, f) {
    var m = /#(.+?)(?=Icon|DotIcon|StretchyIcon|CircleIcon|CircleDotIcon)/, h = d(function(a) {
        h.superclass.constructor.call(this, a);
        this._canvas = new f(e.icons.large.size);
        this._canvas.options.setParent(this.options)
    }, b, {createCluster: function(a, b) {
            var d = h.superclass.createCluster.call(this, a, b), f = b.reduce(function(a, b) {
                var d;
                d = b.options.get("preset",
                        "islands#blueIcon");
                $.isArray(d) && (d = d[0]);
                d = d.match(m)[1];
                a[d] = ++a[d] || 1;
                return a
            }, {}), f = this._canvas.generateIconDataURL(f, b.length), f = {clusterIcons: [c({href: f}, e.icons.small), c({href: f}, e.icons.medium), c({href: f}, e.icons.large)], clusterNumbers: e.numbers};
            d.options.set(f);
            return d
        }});
    a(h)
});
function renderMap(a, b) {
    var d = {suppressMapOpenBlock: !0};
    a = $.extend({center: [55.75494, 37.62062], zoom: 7, controls: ["searchControl", "zoomControl", "typeSelector", "fullscreenControl"], geo: ""}, a);
    $.isFunction(b) && onMapInit(b);
    ymaps.ready(function() {
        var b = function() {
            ymaps.modules.require(["PieChartClusterer"], function(a) {
                clusterer = new a;
                clusterer.createCluster = function(b, d) {
                    var c = a.prototype.createCluster.call(this, b, d);
                    c.events.add("click", function() {
                        map.getZoom() === map.zoomRange.getCurrent()[1] && $.each(c.getGeoObjects(),
                                function(a, b) {
                                    getLotInfo(b)
                                })
                    });
                    return c
                };
                map.geoObjects.add(clusterer);
                mapInitFlg = !0
            })
        };
        a.geo ? ymaps.geocode(a.geo).then(function(e) {
            geoCoord = e.geoObjects.get(0).geometry.getCoordinates();
            $("#map").empty();
            a.center = geoCoord;
            map = new ymaps.Map("map", a, d);
            b()
        }) : ($("#map").empty(), map = new ymaps.Map("map", a, d), b())
    })
}
function getLotInfo(a) {
    if (!a.properties.get("ballonGetted")) {
        var b = a.properties.get("objId");
        $.get(mapAjaxUrl, {id: b}, function(b) {
            a.properties.set({ballonGetted: !0, clusterCaption: b.clusterCaption, balloonContentHeader: b.balloonHeader, balloonContentBody: b.balloonBody, balloonContentFooter: b.balloonFooter});
            clusterer.getObjectState(a).isClustered || a.balloon.open()
        }, "json")
    }
}
function onMapInit(a) {
    mapInitFlg ? a() : setTimeout(function() {
        onMapInit(a)
    }, 500)
}
function onMapLoad(a) {
    mapLoadFlg ? setTimeout(function() {
        onMapLoad(a)
    }, 500) : a()
}
function addToCluster(a) {
    var b = [], d;
    for (d in a) {
        var c, e = a[d], f = e.id.toString();
        icn = customIcon;
        placeMarks[f] || (50 == curCat ? icn = roomsIcon[4 < e.r ? "4" : e.r] : (51 == curCat || 44 == curCat) && 0 < e.tgb && (icn = redIcon), 0 < e.inf && (icn = infrastructureIcon[e.inf]), e.hasOwnProperty("text") ? (c = new ymaps.Placemark([parseFloat(e.lat), parseFloat(e.lng)], {id: e.id}, icn), c.properties.set({balloonContentBody: e.text, clusterCaption: e.name})) : (c = new ymaps.Placemark([parseFloat(e.lat), parseFloat(e.lng)], {objId: f, ballonGetted: !1}, icn),
                c.events.add("click", function(a) {
                    getLotInfo(a.originalEvent.target)
                })), placeMarks[f] = c, b.push(placeMarks[f]))
    }
    clusterer.add(b)
}
function loadObj(a, b) {
    "undefined" === typeof a ? $.isPlainObject(console) && console.log("Parameters not specified") : (mapLoadFlg = !0, $.get(mapAjaxUrl, a, function(a) {
        $.isArray(a.items) && a.items.length && addToCluster(a.items);
        mapLoadFlg = !1;
        $.isFunction(b) && b(a)
    }, "json").then(function() {
        (0 < a.inf || 1 < a.inf.length) && a.inf.split(",").forEach(function(a, c, e) {
            infRequest(map.getCenter()[0], map.getCenter()[1], a, 5).done(function(c) {
                if (0 < c.properties.ResponseMetaData.SearchResponse.found) {
                    var e = [];
                    $.each(c.features, function(b,
                            c) {
                        e.push({id: c.properties.CompanyMetaData.id, lat: c.geometry.coordinates[1], lng: c.geometry.coordinates[0], inf: a, text: createInfBaloon(c.properties.CompanyMetaData), name: c.properties.CompanyMetaData.Categories[0].name})
                    });
                    $.isArray(e) && e.length && addToCluster(e);
                    mapLoadFlg = !1;
                    $.isFunction(b) && b(c)
                }
            })
        })
    }))
}
function checkObjVisibility(a, b, d) {
    if (placeMarks[a]) {
        "undefined" === typeof d && (d = 0);
        var c = map.getZoom(), e = map.getCenter(), f = placeMarks[a].geometry.getCoordinates();
        e[0].toFixed(9) != parseFloat(f[0]).toFixed(9) || e[1].toFixed(9) != parseFloat(f[1]).toFixed(9) ? map.setCenter(f, c, {checkZoomRange: !0, callback: function() {
                checkObjVisibility(a, b)
            }}) : clusterer.getObjectState(placeMarks[a]).isClustered && d < c && c + 1 <= map.options.get("maxZoom") ? map.setZoom(c + 1, {checkZoomRange: !0, callback: function() {
                checkObjVisibility(a,
                        b, c)
            }}) : $.isFunction(b) && b()
    }
}
function showMeInf(a, b, d, c, e, f) {
    f = [];
    var m = "", h = new ymaps.ObjectManager({clusterize: !0});
    map.geoObjects.removeAll();
    showMe(a);
    "number" === typeof e ? f[0] = e : f = e.split(",");
    f.forEach(function(a, e, f) {
        "undefined" !== typeof inf[parseInt(a)] ? h.add(inf[a]) : ($("#info_div_loading").show(), infRequest(d, c, a, b).done(function(c) {
            if (0 < c.properties.ResponseMetaData.SearchResponse.found) {
                var d = [];
                $.each(c.features, function(b, c) {
                    d.push({type: "Feature", id: c.properties.CompanyMetaData.id, geometry: {type: "Point", coordinates: [c.geometry.coordinates[1],
                                c.geometry.coordinates[0]]}, properties: {balloonContent: createInfBaloon(c.properties.CompanyMetaData), clusterCaption: c.properties.CompanyMetaData.Categories[0].name}, options: {iconImageHref: infrastructureIcon[parseInt(a)].iconImageHref}})
                });
                1 < c.properties.ResponseMetaData.SearchResponse.found && (m = "о");
                c = "Найден" + m + " " + c.properties.ResponseMetaData.SearchResponse.found + " " + declOfNum(c.properties.ResponseMetaData.SearchResponse.found, ["объект", "объекта", "объектов"]) + " в радиусе " + b + " км";
                $("#info_div_warning").html(c);
                $("#info_div_warning").show().delay(3E3).fadeOut();
                inf[parseInt(a)] = d;
                h.add(d)
            } else
                $("#info_div_warning").html("В радиусе " + b + " км " + getInfRequest(parseInt(a)) + " не найдены"), $("#info_div_warning").show().delay(3E3).fadeOut()
        }), $("#info_div_loading").hide())
    });
    h.clusters.options.set("preset", "islands#grayClusterIcons");
    h.objects.options.set("preset", infrastructureIcon[2]);
    map.geoObjects.add(h)
}
function declOfNum(a, b) {
    cases = [2, 0, 1, 1, 1, 2];
    return b[4 < a % 100 && 20 > a % 100 ? 2 : cases[5 > a % 10 ? a % 10 : 5]]
}
function infRequest(a, b, d, c) {
    c = [.03 * c, .0132 * c];
    return $.ajax({type: "GET", url: "https://search-maps.yandex.ru/v1/", async: !0, data: {apikey: yandexFirmsKey, lang: "ru_RU", ll: b + "," + a, results: 1E3, rspn: 1, text: getInfRequest(parseInt(d)), spn: c.join(","), type: "biz"}})
}
function createInfBaloon(a) {
    var b;
    b = "" + ('<div class="map-balloon__infra-cat">' + a.Categories[0].name + "</div>");
    b += '<div class="map-balloon__infra-name">' + a.name + "</div>";
    if (a.hasOwnProperty("Phones") || a.hasOwnProperty("url"))
        b += '<div class="map-balloon__infra-detail"><ul class="list-inline">', a.hasOwnProperty("Phones") && (b += "<li>тел. " + a.Phones[0].formatted + "</li>"), a.hasOwnProperty("url") && (b += '<a target="_blank" href="' + a.url + '" rel="nofollow">Веб-сайт</a>'), b += "</ul></div>";
    return b
}
function getInfRequest(a) {
    switch (a) {
        case 1:
            return"детские сады";
        case 2:
            return"школы";
        case 3:
            return"больницы, поликлиники";
        case 4:
            return"кафе,бары,рестораны"
        }
}
function showMe(a, b) {
    a = a.toString();
    placeMarks[a] ? (placeMarks[a].options.set("iconImageHref", redIconUrl), clusterer.remove(placeMarks[a]), map.geoObjects.add(placeMarks[a]), checkObjVisibility(a, b)) : $.get(mapAjaxUrl, {id: a}, function(d) {
        1 > d.lat || 1 > d.lng || (d = new ymaps.Placemark([d.lat, d.lng], {objId: a, iconContent: d.price1, ballonGetted: !0, clusterCaption: d.clusterCaption, balloonContentHeader: d.balloonHeader, balloonContentBody: d.balloonBody, balloonContentFooter: d.balloonFooter}, redIcon), placeMarks[a] = d, map.geoObjects.add(placeMarks[a]),
                checkObjVisibility(a, b))
    }, "json")
}
function clearMap() {
    clusterer.removeAll();
    placeMarks = {}
}
;