<div class="panel panel-default space_top_xs panel-body-cover">
    <div class="panel-heading">Фильтр</div>
    <div class="panel-body" style="padding: 0;">
        <form action="" class="form-horizontal fl-filter">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Поиск</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" placeholder="Поиск"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="data_range" class="col-sm-3 control-label">Диапазон дат</label>
                        <div class="col-sm-9">
                            <div class="input-group" style="width: 48%;float: left;margin-right: 2%;">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" name="date_begin" class="form-control" placeholder="от">
                            </div>
                            <div class="input-group" style="width: 50%;">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" name="date_end" class="form-control" placeholder="до">
                            </div>
                        </div>
                    </div>
                    <?php if(isset($categories) && $categories): ?>
                    <div class="form-group">
                        <label for="file_category_id" class="col-sm-3 control-label">Категория</label>
                        <div class="col-sm-9">
                            <select name="file_category_id" class="form-control">
                                <option value="">Все</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= array_get($cat, 'file_category_id') ?>"><?= array_get($cat, 'name') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Статус</label>
                        <div class="col-sm-9">
                            <select name="status" id="status" class="form-control">
                                <option value="">Все</option>
                                <option value="<?= MY_Model::STATUS_ACTIVE ?>">Опубликовано</option>
                                <option value="<?= MY_Model::STATUS_NOT_PUBLISHED ?>">Черновик</option>
                            </select>
                        </div>
                    </div>
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
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary pull-right submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button>
                            <a href="javascript:void(0)" class="btn btn-default pull-right space_right drop-form">Сброс</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

