<div class="form-group">
    <div>
        <label for="tags" class="control-label space_right">Теги</label>
        <label class="radio-inline" style="margin-top: -5px;"> <input type="radio" name="search_type[]" value="and" checked> И</label>
        <label class="radio-inline" style="margin-top: -5px;"> <input type="radio" name="search_type[]" value="or"> ИЛИ</label>
    </div>
    <div>
        <ul class="methodTags"></ul>
        <input type="hidden" name="tags" class="mySingleFieldNode" value="">
        <?php if ($tags): ?>
            <!--set tags-->
            <script>FlRegister.set('tags', <?= $tags ?>);</script>
            <!--/set tags-->
        <?php endif; ?>
    </div>
</div>