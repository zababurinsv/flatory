<div class="modal fade modal__storage js-ws" data-images-input-type="radio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Каталог файлов</h4>
            </div>
            <div class="modal-body">
                <div style="display: none;">
                    <ul class="nav nav-tabs space_none js-ws-nav" data-tab-group="storage">
                        <?php foreach ($sections as $alias => $it): ?>
                            <li<?php if ($settings['section_default'] === $alias) echo ' class="active"'; ?>><a href="javascript:void(0)" data-tab="<?= implode(',', $it['content']) ?>" data-section="<?= $alias ?>" title="<?= $it['title'] ?>"><?= $it['title'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="nav nav-pills ws-nav-pills" data-tab-group="storage" data-tab-content="modify_nav">
                        <li class="active"><a data-tab="filters">Фильтр</a></li>
                        <li><a data-tab="mass_editor">Редактор</a></li>
                    </ul>
                </div>

                <div class="modal_filters"></div>
                <div class="widget_storage__storage">
                    <div data-tab-group="storage" data-section-content="images" data-tab-content="filters"><?= $filters ?></div>   
                    <div data-tab-group="storage" data-section-content="images" data-tab-content="mass_editor"><?= $mass_editor ?></div>
                    <div data-tab-group="storage" data-section-content="docs" data-tab-content="filters"><?= $filters_docs ?></div>   
                    <div data-tab-group="storage" data-section-content="docs" data-tab-content="mass_editor"><?= $mass_editor_docs ?></div>
                    <div data-tab-group="storage" data-tab-content="upload"><?= $upload ?></div>
                    <div data-tab-group="storage" data-tab-content="images"><?= $images ?></div>
                    <div data-tab-group="storage" data-tab-content="docs"><?= $docs ?></div>
                    <img src="/images/loader.gif" class="center-block space_top space_bottom js-ws-loader" style="opacity: .5;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-sm btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>