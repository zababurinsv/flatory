<div class="form-group">
    <label for="name_like" class="control-label">Поиск по названию или id</label>
    <input type="text" name="name_like" class="form-control" placeholder="Поиск по названию или id"<?= isset($autocomplete) && $autocomplete ? ' data-autocomplete="'. $autocomplete .'" autocomplete="off"' : '' ?>>
</div>

