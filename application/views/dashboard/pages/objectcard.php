<?php
//vdump($current_section);
?>
<aside id="objectcard-nav-place">
    <ul class="nav nav-pills nav-stacked">
        <?php foreach ($sections as $it): if (!(int) array_get($it, 'is_has_object')) continue; ?>
        <li<?= array_get($it, 'alias') === $current_section  ? ' class="active"' : '' ?>><a href="/admin/objects/<?= array_get($it, 'alias') ?>/<?= $object_id ?>"><?= array_get($it, 'name') ?></a></li>
        <?php endforeach; ?>
    </ul>
</aside>
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
                        <a href="/catalog/<?= $object['alias'] ?>/#<?= $action ?>" target="_blank" class="btn btn-default glyphicon glyphicon-globe"></a>
                        <?php if ($object): ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?= array_get(array_get($sections, $current_section, []), 'name', 'Неизвестный раздел') ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php foreach ($sections as $it): if (!(int) array_get($it, 'is_has_object')) continue; ?>
                                        <li><a href="/admin/objects/<?= array_get($it, 'alias') ?>/<?= $object_id ?>"><?= array_get($it, 'name') ?></a></li>
                                    <?php endforeach; ?>

                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0)" data-object-action="open_modal_sections">Добавить раздел</a></li>

                                </ul>
                            </div>
                        <?php endif; ?>
                        <button type="button" name="save_page_form" data-object-action="save_page_form" class="btn btn-success">Сохранить</button>
                    </nav>
                </div>
            </div>
        </header>
    </div>
    <?= $content ?>
</div>
<?php if ($object): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-object-sections">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Разделы</h4>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="form-objecct-sections">
                        <?php foreach ($sections as $it): ?>
                            <div class="checkbox">
                                <label>
                                    <input name="object_section_id[]" value="<?= array_get($it, 'object_section_id') ?>" type="checkbox"<?= !(int) array_get($it, 'is_has_object') ? '' : ' checked="checked"'; ?><?= !(int) array_get($it, 'is_default') ? '' : ' disabled="disabled"'; ?>> <?= array_get($it, 'name') ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <input type="hidden" name="object_id" value="<?= $object_id ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" data-object-action="save_modal_form">Сохранить</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

