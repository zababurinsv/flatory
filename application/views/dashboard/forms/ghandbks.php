<form action="" class="form" method="post">
    <div class="form-group">
        <label for="" class="control-label">Название <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= isset($post) ? array_get($post, 'name') : '' ?>">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">Сохранить</button>
    </div>
</form>