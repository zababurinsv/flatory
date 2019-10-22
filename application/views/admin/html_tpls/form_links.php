<script type="text/template" id="tpl__form_links">
    <div class="form_links" data-id="{{=it.link_id}}">
        <div class="form-group">
            <label class="sr-only">Название</label>
            <input type="text" name="links[{{=it.link_id}}][name]" class="form-control" value="{{=it.name || ''}}" placeholder="Название">
        </div>
        <div class="form-group" style="width: 45%;">
            <label class="sr-only">Ссылка</label>
            <input type="text" name="links[{{=it.link_id}}][link]" class="form-control" value="{{=it.link || ''}}" placeholder="Ссылка">
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-default add_item"><span class="glyphicon glyphicon-plus"></span></button>
            <button type="button" class="btn btn-default delete_item"><span class="glyphicon glyphicon-minus"></span></button>
        </div>
        <div class="clearfix space_bottom"></div>
    </div>
</script>
