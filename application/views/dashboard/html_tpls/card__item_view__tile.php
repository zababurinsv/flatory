<script type="text/template" id="card__item_view__tile">
    <li>
        <img src="/images/thumbs/{{=it.file_name}}" class="thumbnail">
        <div class="item_control">
            <span>{{=it.file_id}}</span>
            <i role="button" class="glyphicon glyphicon-remove text-danger tile_galery__remove_item pull-right"></i>
        </div>
        <input type="hidden" name="files[]" value="{{=it.file_id}}">
        <div class="item_settings">
            <label>Сортировка</label>
            <input type="text" name="sort[{{=it.file_id}}]" value="99">
        </div>
        <a href="/admin/storage/card/{{=it.name}}" class="tile_galery__label" target="_blank">{{=it.original_name}}</a>
    </li>
</script>

