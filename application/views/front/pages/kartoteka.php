<?php 
//vdump($list);
?>
<div class="content_space">
    <h1 class="page-name space_bottom_xxl"><?= element('name', $category) ?></h1>
    <ul class="glossary_list">
        <?php if ($list): ?>
            <?php foreach ($list as $item): ?>
            <?php if(!element('name', $item)) continue; ?>
                <li>
                    <table>
                        <tbody>
                            <tr>
                                <td class="item_data">
                                    <article>
                                        <h3 class="space_bottom_l"><?= element('name', $item) ?></h3>
                                        <?= element('description', $item) ?>
                                    </article>
                                </td>
                                <td class="item_control">
                                    <span class="fl_arrow fl_next"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <ul class="item_meta">
                        <?php if (element('childs', $item)): ?>
                            <?php foreach ($item['childs'] as $child): ?>
                                <li><a href="/kartoteka/<?= element('alias', $child) ?>"><?= element('name', $child) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="clearfix"></div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($builders): ?>
            <li><?= $builders ?></li>
        <?php endif; ?>
        <?php if ($sellers): ?>
            <li><?= $sellers ?></li>
        <?php endif; ?>
    </ul>
</div>
<script>
    (function() {
        $('.glossary_list .item_control').on('click', function(e) {
            var isOpenedThis = $(this).hasClass('opened');
            $('.glossary_list .opened').removeClass('opened');
            $('.glossary_list .item_meta').hide(300);

            if (!isOpenedThis) {
                $(this).addClass('opened');
                $(this).parents('li').find('.item_meta').show(300);
            }

        });
    }());
</script>
