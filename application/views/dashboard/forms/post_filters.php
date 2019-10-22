<div class="hpanel panel-body-cover">
    <div class="panel-heading hbuilt">
        Фильтр
    </div>
    <div class="panel-body">
        <form action="" class="fl-filter">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name_like" class="control-label">Поиск по названию или id</label>
                        <input type="text" name="name_like" class="form-control" placeholder="Поиск по названию или id">
                    </div>
                    <div class="form-group">
                        <label for="data_range" class="control-label">Диапазон дат</label>
                        <div>
                            <div class="input-group" style="width: 48%;float: left; margin-right: 2%;">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" name="date_begin" class="form-control" placeholder="от">
                            </div>
                            <div class="input-group" style="width: 50%;">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" name="date_end" class="form-control" placeholder="до">
                            </div>
                        </div>
                    </div>
                    <?php if ($status): ?>
                        <div class="form-group">
                            <div>
                                <label for="status" class="control-label">Статус</label>
                            </div>
                            <div class="space_left" style="min-height: 34px;">
                                <?php foreach ($status as $val => $it): ?>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="status[]" value="<?= $val ?>"> 
                                        <span class="status status-<?= array_get($it, 'alias', 'danger') ?>" title="<?= $_title = array_get($it, 'title', 'Неизвестный статус') ?>"></span> 
                                        <span><?= $_title ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <?php if (isset($categories) && $categories): ?>
                        <div class="form-group">
                            <label for="file_category_id" class="control-label">Категория</label>
                            <select name="file_category_id" class="form-control">
                                <option value="">Все</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= array_get($cat, 'file_category_id') ?>"><?= array_get($cat, 'name') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
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
                </div>
            </div>
            <div class="row more_filters" style="display: none;">
                <div class="col-md-6">

                </div>
                <div class="col-md-6">

                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button>
                        <a href="javascript:void(0)" class="btn btn-default pull-right space_right drop-form">Сброс</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

