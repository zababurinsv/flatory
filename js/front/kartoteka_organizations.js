;
var FlKartotekaOrganizations = (function() {
    var app = {
        limit: 5,
        offset: 0,
        init: function() {
            app.setUpListeners();
        },
        setUpListeners: function() {
            $('#show_more a').off('click').on('click', app.showMoreClick);
        },
        showMoreClick: function(e) {
            var thos = this;
            $('#show_more a').hide();
            $('#show_more img').show();

            app.offset += app.limit;

            $.getJSON(location.href, {offset: app.offset, limit: app.limit}, function(response) {
                if (response.success) {
                    $('#show_more').siblings('.objects_list').append(response.view);
                } else {
                    
                }
                $('#show_more img').hide();
                if (response.has_more)
                    $('#show_more a').show();
            });
        },
        publ: {
        }
    };
    app.init();
    return app.publ;
}());