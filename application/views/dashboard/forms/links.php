<div class="on-hover-box form_links" data-id="<?= array_get($link, 'link_id') ?>">
    <div class="col-50">
        <input type="text" name="links[<?= array_get($link, 'link_id') ?>][name]" class="form-control" value="<?= array_get($link, 'name') ?>" placeholder="Название">
    </div>
    <div class="col-50">
        <input type="text" name="links[<?= array_get($link, 'link_id') ?>][link]" class="form-control" value="<?= array_get($link, 'link') ?>" placeholder="Ссылка">
    </div>
    <a href="javascript:void(0)" title="Удалить" class="glyphicon glyphicon-trash only-on-hover doc-link-rm delete_item"></a>
    <div class="clearfix"></div>
</div>