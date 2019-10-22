<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form method="post" action="" id="object-form">
            <table class="table table-no-border table-head-b-line-yellow">
                <thead>
                    <tr>
                        <th>Квартиры</th>
                        <th>Площадь м<sup>2</sup></th>
                        <th>Цена за м<sup>2</sup></th>
                        <th>Цена за квартиру</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flats as $flat): ?>
                        <?php $room_id = element('room_id', $flat, 0); ?>
                        <tr>
                            <td>
                                <?= element('name', $flat, '') ?>
                                <input name="flat[<?= $room_id ?>][room_id]" value="<?= $room_id ?>" type="hidden"/> 
                            </td>
                            <td>
                                <div class="input-group">
                                    <input style="width: 80px;" class="is_selected form-control js-input-number" data-decimal="2" name="flat[<?= $room_id ?>][space_min]" value="<?= number_format(element('space_min', $flat, 0), 2, '.', ' ') ?>" type="text" placeholder="от"> 
                                    <input style="width: 80px;" class="is_selected form-control js-input-number" data-decimal="2" name="flat[<?= $room_id ?>][space_max]" value="<?= number_format(element('space_max', $flat, 0), 2, '.', ' ') ?>" type="text" placeholder="до">
                                </div>

                            </td>
                            <td>
                                <div class="input-group">
                                    <input style="width: 90px;" class="is_selected form-control js-input-number" name="flat[<?= $room_id ?>][cost_m_min]" value="<?= number_format(element('cost_m_min', $flat, 0), 0, '.', ' ') ?>" type="text" placeholder="от"> 
                                    <input style="width: 90px;" class="is_selected form-control js-input-number" name="flat[<?= $room_id ?>][cost_m_max]" value="<?= number_format(element('cost_m_max', $flat, 0), 0, '.', ' ') ?>" type="text" placeholder="до">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input style="width: 120px;" class="is_selected form-control js-input-number" name="flat[<?= $room_id ?>][cost_min]" value="<?= number_format(element('cost_min', $flat, 0), 0, '.', ' ') ?>" type="text" placeholder="от"> 
                                    <input style="width: 120px;" class="is_selected form-control js-input-number" name="flat[<?= $room_id ?>][cost_max]" value="<?= number_format(element('cost_max', $flat, 0), 0, '.', ' ') ?>" type="text" placeholder="до">
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php foreach ($registry_handbks as $hb): ?>
                <div class="form-group">
                    <label class="control-label"><?= array_get($hb, 'name') ?></label>
                    <div>
                        <?php if (array_get($hb, 'list')): ?> 
                            <?php foreach ($hb['list'] as $it): ?>
                                <label class="checkbox-inline">
                                    <div class="icheckbox_square-green">
                                        <input type="checkbox" class="i-checks" name="registry_id[]" value="<?= array_get($it, 'registry_id') ?>">
                                        <ins class="iCheck-helper"></ins>
                                    </div>
                                    <?= array_get($it, 'name') ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-group">
                <label for="anons" class="control-label">Анонс</label>
                <textarea class="form-control ckeditor" name="cost_anons" placeholder="Анонс для раздела Цены"><?= array_get($object_meta, 'cost_anons') ?></textarea>
            </div>
        </form>
        <script>
            (function(){
                var current = <?= json_encode($object) ?>, i;
                    FlRegister.set('fields', current);                 
                    FlForm.fillForm(current, $('#object-form'));
            }());
        </script>
    </div>
</div>