<div class="form-group">
    <div class="col-sm-3">
        <div class="text-right">
            <label for="tags" class="control-label">Теги</label>
        </div>
        <div class="pull-right">
            <label class="radio-inline"> <input type="radio" name="search_type[]" value="and" checked> И</label>
            <label class="radio-inline"> <input type="radio" name="search_type[]" value="or"> ИЛИ</label>
        </div>
    </div>
    <div class="col-sm-9">
        <ul class="methodTags"></ul>
        <input type="hidden" name="tags" class="mySingleFieldNode" value="">
        <?php if ($tags): ?>
            <!--set tags-->
            <script>FlRegister.set('tags', <?= $tags ?>);</script>
            <!--/set tags-->
        <?php endif; ?>
    </div>
</div>