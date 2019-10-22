<?php if (!empty($objects)): ?>
    <!--result list-->
    <?php if (!isset($hide_list_wrapper)): ?>
        <ul class="objects_list">
        <?php endif; ?>
        <?php
        foreach ($objects as $object):
            // prepare object
            $object = $this->Object_Model->prepare_object($object, [
                'truncate_list' => ['address' => 100, 'name' => 50],
                'image' => 'image_1',
//                'cost' => ['cost_min'],
                'delivery' => TRUE,
                'space' => TRUE,
            ]);
            $name = array_get($object, 'name');
            $full_name = array_get($object, 'full_name', $name);
            $image = array_get($object, 'image');
            $address = array_get($object, 'address');
            $full_address = array_get($object, 'full_address', $address);
            $alias = array_get($object, 'alias');
            $space_min = array_get($object, 'space_min');
            $space_max = array_get($object, 'space_max');
            ?>
            <li>
                <div class="f-obj-preview">
                    <a href="/catalog/<?= $alias ?>" class="fb-left" title="<?= $full_name ?>">
                        <img src="<?= $image ?>" alt="<?= $full_name ?>">
                    </a>
                    <div class="object_list_container">
                        <h3 class="f-obj-preview__name"><a href="/catalog/<?= $alias ?>"  title="<?= $full_name ?>"><?= $name ?></a></h3>
                        <table class="f-obj-preview__address_block">
                            <tr>
                                <td class="address_block__icon"><span class="fb-icon-xs fb-icon-address"></span></td>
                                <td class="address_block__content" title="<?= $full_address ?>"><?= $address ?></td>
                            </tr>
                        </table>
                        <div class="fb-icon fb-icon-rub">
                            <b>Цена: </b>
                            <?php if (!($cost_min = array_get($object, 'cost_min'))): ?>
                                <span>−</span>
                            <?php else: ?>
                                от <h2><?= big_ru_money_format($cost_min) ?></h2>
                            <?php endif; ?>
                        </div>
                        <div class="fb-icon fb-icon-space">
                            <b>Площадь: </b> 
                            <?php if ($space_min && $space_max): ?>
                                от <h2><?= $space_min ?></h2> до <h2><?= $space_max ?></h2> м<sup>2</sup>
                            <?php elseif ($space_min): ?>
                                от <h2><?= $space_min ?></h2> м<sup>2</sup>
                            <?php elseif ($space_max): ?>
                                до <h2><?= $space_max ?></h2> м<sup>2</sup>
                            <?php else: ?>
                                <span>−</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!!($delivery = array_get($object, 'delivery'))): ?>
                            <div class="fb-gray"><strong>Срок сдачи: </strong><span><?= $delivery ?></span></div>
                        <?php endif; ?>
                        <?php if (element('organization_types', $object)): ?>
                            <span class="fb-gray" style="position: absolute; right: 0; bottom: 0;"><?= element('organization_types', $object) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
        <?php if (!isset($hide_list_wrapper)): ?>
        </ul>
    <?php endif; ?>
    <!--/result list-->
    <!--pagination-->
    <?php if (isset($pagination)): ?> 
        <div class="space_top"></div>
        <div class="flatory_pagination__center">
            <?= $pagination ?>
        </div>
    <?php endif; ?>
    <!--/pagination-->
<?php endif; ?>