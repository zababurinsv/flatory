var FlFormControls = (function () {
    var app = {
        init: function () {
            this.setUpListeners();
        },
        setUpListeners: function () {
            $('[data-action]').off('click').on('click', app.doAction);
        },
        /**
         * do some action
         * @param {object} e - event
         * @returns {undefined}
         */
        doAction: function (e) {
            var action = $(this).data('action');
            if (typeof app.actions[action] === 'function')
                app.actions[action].call(this, e);
            else
                console.log('Action: ' + action + ' not found!');
        },
        actions: {
            copy_parent: function () {
                var cp, listParents,
                        parent = $(this).data('parent') ? $(this).parents($(this).data('parent')) : null;
                if (!parent) {
                    console.log('parent not defined');
                    return;
                }
                cp = parent.clone();
                cp.find('[selected="selected"]').removeAttr('selected');
                parent.after(cp);
                listParents = parent.siblings($(this).data('parent'));

                // hide add
                parent.find('[data-action="copy_parent"]').hide();
                listParents.find('[data-action="copy_parent"]').not(':last').hide();

                parent.find('[data-action="rm_parent"]').show();
                listParents.find('[data-action="rm_parent"]').not(':last').show();

                app.setUpListeners();
            },
            rm_parent: function () {
                var parent = $(this).data('parent') ? $(this).parents($(this).data('parent')) : null;

                if (!parent) {
                    console.log('parent not defined');
                    return;
                }

                parent.remove();
            }
        },
        publ: {
        }
    };
    app.init();
    return app.publ;
}());