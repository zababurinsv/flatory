<div class="container-fluid">
    <div class="objectcard-header-wrapp">
        <header id="objectcard-header">
            <div class="row">
                <?php
                if (isset($breadcrumbs) && is_array($breadcrumbs) && $breadcrumbs): $_b_i = 0;
                    $_b_count = count($breadcrumbs);
                    ?>
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbs as $it): $_b_i++; ?>
                            <?php if ($_b_i !== $_b_count): ?>
                                <li><a href="<?= array_get($it, 'url') ?>"><?= array_get($it, 'title') ?></a></li>
                            <?php else: ?>
                                <li class="active"><?= array_get($it, 'title') ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
                <div class="col-md-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-md-6">
                    <nav>  
                        <button type="button" name="save_page_form" data-object-action="save_page_form" class="btn btn-success">Сохранить</button>
                    </nav>
                </div>
            </div>
        </header>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="hpanel">
                <div class="panel-body">
                    <form action="post" id="storage_card">
                        <div class="form-group">
                            <label>Название</label>
                            <input type="text" name="original_name" class="form-control" value="<?= $file['original_name'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Описание</label>
                            <textarea name="alt" class="form-control" ><?= $file['alt'] ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Источник</label>
                            <textarea name="description" class="form-control ckeditor" ><?= $file['description'] ?></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="file_id"  value="<?= $file['file_id'] ?>">
                        </div>

                        <div class="form-group">
                            <label>Теги</label>
                            <ul id="methodTags"></ul>
                            <input type="hidden" name="tags" id="mySingleFieldNode" value="<?= $image_tags ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="hpanel">
                <div class="panel-heading hbuilt">Информация о файле</div>
                <div class="panel-body">
                    <h5>Используется в:</h5>
                    <?php if (!empty($file_involves)): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($file_involves as $involves): ?>
                                <li>
                                    <a href="<?= $involves['uri'] ?>" target="_blank" class="space_right_xs" title="<?= $involves['name'] ?>"><span class="glyphicon glyphicon-globe"></span></a>
                                    <a href="<?= $involves['uri_adm'] ?>" target="_blank"><?= $involves['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">Файл нигде не используется.</div>
                        <a href="javascript:void(0)" class="btn btn-danger center-block delete_item space_top_xs" data-file_id="<?= $file['file_id'] ?>">Удалить безвозвратно</a>
                    <?php endif; ?>
                    <h5 class="space_top">Параметры:</h5>
                    <ul class="list-unstyled">
                        <li>Формат: <?= $file['ext'] ?></li>
                        <li>Вес: <?= byte_format($file['size']) ?></li>
                        <li><hr class="space_top_xl"></li>
                        <li>Дата создания: <?= date("d.m.Y H:i", strtotime($file['created'])) ?></li>
                        <li>Дата обновления: <?= date("d.m.Y H:i", strtotime($file['updated'])) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!--set tags-->
<script>
<?php if ($tags): ?>
        FlRegister.set('tags', <?= $tags ?>);
<?php endif; ?>
</script>
<!--/set tags-->