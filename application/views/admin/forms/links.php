<div class="form_links" data-id="<?= element('link_id', $link, '') ?>">
    <div class="form-group">
        <label class="sr-only">Название</label>
        <input type="text" name="links[<?= element('link_id', $link, '') ?>][name]" class="form-control" value="<?= element('name', $link, '') ?>" placeholder="Название">
    </div>
    <div class="form-group" style="width: 45%;">
        <label class="sr-only">Ссылка</label>
        <input type="text" name="links[<?= element('link_id', $link, '') ?>][link]" class="form-control" value="<?= element('link', $link, '') ?>" placeholder="Ссылка">
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-default add_item" style="display: none;"><span class="glyphicon glyphicon-plus"></span></button>
        <button type="button" class="btn btn-default delete_item"><span class="glyphicon glyphicon-minus"></span></button>
    </div>
    <div class="clearfix space_bottom"></div>
</div>