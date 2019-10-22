<script type="text/template" id="tpl__form_links">
    <div class="on-hover-box form_links" data-id="{{=it.link_id}}">
        <div class="col-50">
            <input type="text" name="links[{{=it.link_id}}][name]" class="form-control" value="{{=it.name || ''}}" placeholder="Название">
        </div>
        <div class="col-50">
            <input type="text" name="links[{{=it.link_id}}][link]" class="form-control" value="<?= array_get($link, 'link') ?>" placeholder="Ссылка">
        </div>
        <a href="javascript:void(0)" title="Удалить" class="glyphicon glyphicon-trash only-on-hover doc-link-rm delete_item"></a>
        <div class="clearfix"></div>
    </div>
</script>
