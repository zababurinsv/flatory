;
/**
 * object organizations
 * @param {object} FlRegister - register
 * @type Function|@exp;app@pro;publ
 */
var FlObjectOrganizations = (function (FlRegister){
    var app = {
        objectOrganizations: [],
        targets: {
            item: '[data-name="organization"]',
            add: '.add_item',
            del: '.del_item'
        },
        init: function(){
            this.objectOrganizations = FlRegister.get('object_organizations') || [];
            
            // add current organizations
            for (var k in this.objectOrganizations) {                
                if (k > 0)
                    this.add(this.objectOrganizations[k].organization_id);
                else
                    $(this.targets.item).first().find('select [value="'+ this.objectOrganizations[k].organization_id +'"]').attr('selected', 'selected');
            }
            this.refreshButtons();
            this.setUpListeners();
        },
        setUpListeners: function() {
            $(this.targets.add).off('click').on('click', app.clickAdd);
            $(this.targets.del).off('click').on('click', app.clickDel);
        },
        clickAdd: function(e) {
            app.add();
        },
        clickDel: function(e) {
            var el = $(this).parents(app.targets.item);
            var val = el.find('select').val();
            if (!val) {
                el.remove();
                return;
            }
            if (confirm('Вы уверены что хотите удалить ' + el.find('select [value="'+ val +'"]').text().trim() + '?'))
                el.remove();
        },
        add: function(val) {
            var last = $(this.targets.item).last();
            var el = last.clone();
            val = val || '';
            el.find('select [value="'+ val +'"]').attr('selected', 'selected');
            last.after(el);
            this.refreshButtons();
            this.setUpListeners();
        },
        refreshButtons: function() {
            var last = $(this.targets.item).last();
            $(this.targets.del).show();
            $(this.targets.add).hide();
            last.find(this.targets.del).hide();
            last.find(this.targets.add).show();
        },
        publ: {
            
        }
    };
    app.init();
    return app.publ;
}(FlRegister));