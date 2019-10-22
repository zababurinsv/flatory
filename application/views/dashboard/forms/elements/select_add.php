<div class="input-group js-select-add">
    <select name="<?= $name ?>" class="form-control">
        <?php foreach ($options as $it): ?>
            <option value="<?= array_get($it, $value, '') ?>" <?php if (array_get($it, $value) === $current_value): ?>selected="selected"<?php endif; ?>><?= array_get($it, $text, '') ?></option>
        <?php endforeach; ?>
    </select>
    <span class="input-group-btn">
        <button type="button" class="btn btn-default" data-select-add-action="<?= !!$minus ? 'rm' : 'add' ?>"><span class="glyphicon glyphicon-<?= !!$minus ? 'minus' : 'plus' ?>"></span></button>
    </span>
</div>
<script>
    (function() {
        var app = {
            init: function() {
                this.setUpListeners();
            },
            setUpListeners: function() {
                $('[data-select-add-action]').off('click').on('click', app.action);
            },
            action: function(e) {
                var cl, p;
                if ($(this).data('select-add-action') === 'rm') {
                    // rm
                    if (confirm('Вы уверены?'))
                        $(this).parents('.js-select-add').remove();
                } else {
                    // add
                    p = $(this).parents('.js-select-add');
                    cl = p.clone();
                    cl.find('select [value=""]').attr('selected', 'selected');
                    $(this).parents('.js-select-add').after(cl);
                    app.toggleAction(p);
                    app.setUpListeners();
                }
            },
            toggleAction: function(elParent) {
                var t = $(elParent).find('[data-select-add-action]'), s = t.find('.glyphicon');

                if (t.data('select-add-action') === 'rm') {
                    t.data('select-add-action', 'add');
                    s.removeClass('glyphicon-minus').addClass('glyphicon-plus');
                } else {
                    t.data('select-add-action', 'rm');
                    s.removeClass('glyphicon-plus').addClass('glyphicon-minus');
                }
            }
        };
        app.init();
    }());
</script>