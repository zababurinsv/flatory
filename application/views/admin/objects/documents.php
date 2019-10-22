<form action="" method="post" class="form-inline">
    <h4>Файлы</h4>
    <?php if (empty($files)): ?>
        <div class="alert alert-info">Документов нет.</div>
    <?php endif; ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th></th>
                <th>Название</th>
                <th>Источник</th>
                <th>Описание</th>
                <th>Дата создания</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="cart_image_added">
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><i class="icon_ext_<?= element('ext', $file, '') ?>"></i></td>
                    <td><a href="/admin/storage/card/<?= element('name', $file, '') ?>" target="_blank"><?= element('original_name', $file, '') ?></a></td>
                    <td><?= element('description', $file, '') ?></td>
                    <td><?= element('alt', $file, '') ?></td>
                    <td><?= date("d.m.Y H:i:s", strtotime(element('created', $file, ''))) ?></td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-xs btn-danger delete_item pull-right" data-warning="1">
                            <input type="hidden" name="files[]" value="<?= element('file_id', $file, '') ?>" />
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h4>Ссылки</h4>
    <?php if (empty($links)): ?>
        <div class="alert alert-info">Ссылок нет.</div>
    <?php else: ?>
        <?php foreach($links as $link): ?>
         <?= $this->load->view($this->template_dir . 'forms/links', array('link' => $link), TRUE); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?= $this->load->view($this->template_dir . 'forms/links', array('link' => array('link_id' => 'add_0')), TRUE); ?>


    <input type="hidden" name="documents" value="1"/>
    <button type="submit" class="btn btn-success">Сохранить</button>
    <div class="space_bottom"></div>
</form>
<script type="text/template" id="card__item_view__tile">
    <tr>
    <td><i class="icon_ext_{{=it.ext}}"></i></td>
    <td><a href="/admin/storage/card/{{=it.name}}" target="_blank">{{=it.original_name}}</a></td>
    <td>{{=it.description || ''}}</td>
    <td>{{= it.alt || ''}}</td>
    <td>{{= FlHelper.date('d.m.Y H:i:s', it.created) }}</td>
    <td>
    <a href="javascript:void(0)" class="btn btn-xs btn-danger delete_item pull-right">
    <input type="hidden" name="files[]" value="{{=it.file_id}}" />
    <span class="glyphicon glyphicon-trash"></span>
    </a>
    </td>
    </tr>
</script>
<script type="text/javascript">
    $(document).on('ready', function() {
        // remove added & removing files
        FlUpload.extends.uploadAbortSuccess(function(fileId) {
            $('.cart_image_added').find('[name="files[]"][value="' + fileId + '"]').parents('tr').remove();
        });
    });
</script>