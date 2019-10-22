/**
 * Glossary controller
 * @param {object} $ - jQuery
 * @param {object} FlCache - FlCache
 * @param {object} FlRegister - FlRegister
 * @type @exp;app@pro;publ|Function
 */
var FlGlossary = (function($, FlCache, FlRegister) {
    var app = {
        targets: {
            handbk: '[name="handbk_id"]',
            handbkObjectLoader: '#handbk_object_loader',
            handbkObject: '[name="object_id"]',
            delete: '.d_it'            
        },
        handbk_id: false,
        object_id: false,
        init: function() {
            app.setUpListeners();
            
            this.handbk_id = FlRegister.get('handbk_id');
            this.object_id = FlRegister.get('object_id');
            
            console.log(this);
            
            // set current handbk_id
            if(this.handbk_id){
                $(this.targets.handbk).find('[value="'+ this.handbk_id +'"]').attr('selected', 'selected');
                $(this.targets.handbk).trigger('change');
            }
        },
        setUpListeners: function() {
            $(this.targets.handbk).off('change').on('change', app.changeHandbk);  
            $('.tab-content [name="name"]').off('change').on('change', app.changeName);
        },
        changeHandbk: function(e) {
            var handbkId = Number($(this).val());
            var url = location.protocol + '//' + location.hostname + '/admin/ajax/handbk/' + handbkId;
            var cacheData = FlCache.get(url, true);

            var _renderList = function(data, handbk) {
                handbk = typeof handbk === 'object' ? handbk : {};
                for (var k in data) {
                    $(app.targets.handbkObject).append($('<option>', {value: data[k][handbk.primary_key || 'id'], text: data[k].name}));
                }
                // set current object_id
                if(app.object_id){
                    $(app.targets.handbkObject).find('[value="'+ app.object_id +'"]').attr('selected', 'selected');
                    app.object_id = false;
                }
            };
            // delete all
            $(app.targets.handbkObject).find('option').not('[value=""]').remove();

            if (!handbkId || isNaN(handbkId))
                return false;

            $(app.targets.handbkObjectLoader).show(100);

            if (cacheData) {
                _renderList(cacheData.data, cacheData.handbk);
                $(app.targets.handbkObjectLoader).hide(100);
            } else {
                $.getJSON(url, {}, function(response) {
                    if (response.success) {
                        // set cache with ttl 10 min 
                        FlCache.set(url, response, 60 * 10);
                        _renderList(response.data, response.handbk);
                    } else {

                    }
                    $(app.targets.handbkObjectLoader).hide(100);
                });
            }


        },
        changeName: function (e){
            $('.tab-content [name="meta_title"]').val($(this).val());
        },
        publ: {
        }
    };
    app.init();
    return app.publ;
}(jQuery, FlCache, FlRegister));