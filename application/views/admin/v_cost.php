<div class="tab-pane active" id="tab4">
    <br/>
    <form method="POST" action="">
        <div class="form-group">
            <label for="anons" class="control-label">Анонс</label>
            <textarea class="form-control ckeditor" name="cost_anons" rows="3" placeholder="Анонс для раздела Цены"><?= array_get($object_meta, 'cost_anons')?></textarea>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Квартиры</th>
                    <th>Площадь</th>
                    <th>Цена за м<sup>2</sup>/руб</th>
                    <th>Цена за квартиру м<sup>2</sup>/руб</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($flats as $flat): 
                    $room_id = element('room_id', $flat, 0); ?>
                <tr>
                    <td>
                        <?= element('name', $flat, '') ?>
                        <input name="flat[<?= $room_id ?>][room_id]" value="<?= $room_id ?>" type="hidden"/> 
                    </td>
                    <td>
                        от <input style="width: 80px;" class="is_selected" name="flat[<?= $room_id ?>][space_min]" value="<?= element('space_min', $flat, 0) ?>" type="text"/> 
                        до <input style="width: 80px;" class="is_selected" name="flat[<?= $room_id ?>][space_max]" value="<?= element('space_max', $flat, 0) ?>" type="text"/>
                    </td>
                    <td>
                        от <input style="width: 80px;" class="is_selected" name="flat[<?= $room_id ?>][cost_m_min]" value="<?= element('cost_m_min', $flat, 0) ?>" type="text"/> 
                        до <input style="width: 80px;" class="is_selected" name="flat[<?= $room_id ?>][cost_m_max]" value="<?= element('cost_m_max', $flat, 0) ?>" type="text"/>
                    </td>
                    <td>
                        от <input style="width: 80px;" class="is_selected" name="flat[<?= $room_id ?>][cost_min]" value="<?= element('cost_min', $flat, 0) ?>" type="text"/> 
                        до <input style="width: 80px;" class="is_selected" name="flat[<?= $room_id ?>][cost_max]" value="<?= element('cost_max', $flat, 0) ?>" type="text"/>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
        <div class="form-group">
            <div class="col-sm-offset-13 col-sm-10">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </form>
</div>
<script src="/js/dashboard.js"></script>