;
var FlPostList = (function () {
    var app = {
        init: function () {
            this.setUpListeners();
        },
        setUpListeners: function () {
            // autocomplete object names
            $('[name="name_like"]').off('keypress').on('keypress', app.autocompleteObjectNames);
        },
        /**
         * autocomplete object names
         * @param {object} e - event keypress
         * @returns {undefined}
         */
        autocompleteObjectNames: function (e) {
            var autoCompelteElement = this;

            $(autoCompelteElement).autocomplete({
                source: function (request, response) {
                    // ajax load
                    var url = location.protocol + '//' + location.host + '/admin/ajax/post_names';
                    $.getJSON(url, {name: request.term}, function (data) {
                        // check success
                        if (data.success === false) {
                            return false;
                        }

                        response($.map(data.data, function (item) {
                            return{
                                label: item.label,
                                value: item.value
                            };
                        }));
                    });
                },
                // hide value on focus
                focus: function (event, ui) {
                    return false;
                },
                select: function (event, ui) {
                    var selectedObj = ui.item;
                    $(autoCompelteElement).val(selectedObj.label);
                    return false;
                }
            });
        },
        publ: {
        }
    };
    app.init();
    return app.publ;
}());