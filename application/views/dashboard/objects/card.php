<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" id="object-form" method="post">
            <div class="form-group">
                <?php foreach ($images_simple_upload as $it): ?>
                    <?= $it ?>
                <?php endforeach; ?>
            </div>
        </form>
        <script type="text/javascript">
            (function () {

                var oName = '<?= $object['name'] ?>';

                $('.upload_place .text_image__val').each(function () {
                    if (!$(this).val()) {
                        $(this).val(oName);
                        $(this).siblings('.text_image').text(oName);
                    }
                });

                $('#object-form').on('submit', function () {
                    $('.text_image').each(function () {
                        $(this).siblings('.text_image__val').val($(this).text());
                        $(this).siblings('.file_image__val').val($(this).siblings('[name="file_id"]').val());
                    });
//                    $(this).submit();
                });
            }());
        </script>
    </div>
</div>

<?= $widget_storage ?>