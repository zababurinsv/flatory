<?php if ($read_more && $categories): ?>
    <b>Читайте также:</b>
    <ul class="list-unstyled">
        <?php foreach ($read_more as $it): $cat = element(element('file_category_id', $it), $categories, array()); ?>
            <li class="news_page_item">
                <div class="news_page_img">
                    <img src="<?= element('image', $it) ?>" />
                </div>
                <div class="news_page_anons">
                    <h4><a href="/<?= element('prefix', $cat) ?>/<?= urlencode(element('alias', $it)) ?>" title="<?= htmlspecialchars(element('name', $it), ENT_QUOTES) ?>"><?= element('name', $it) ?></a></h4>
                    <span class="news_date"><?= get_full_date_ru(element('created', $val)) ?></span>
                    <p class="news_preview">
                        <?= element('anons', $it) ?>... 
                        <a href="/<?= element('prefix', $cat) ?>/<?= htmlspecialchars(element('alias', $it), ENT_QUOTES) ?>" title="<?= htmlspecialchars(element('name', $it), ENT_QUOTES) ?>">Читать дальше</a>
                    </p>
                    <b class="pull-right"><?= element('name', $cat) ?></b>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>