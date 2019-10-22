<?php

function get_month($en) {
    $trans = array("January" => "января",
        "February" => "февраля",
        "March" => "марта",
        "April" => "апреля",
        "May" => "мая",
        "June" => "июня",
        "July" => "июля",
        "August" => "августа",
        "September" => "сентября",
        "October" => "октября",
        "November" => "ноября",
        "December" => "декабря"
    );
    return strtr($en, $trans);
}
?>
<div class="content_space">
    <h1 class="page-name">Строй-блог</h1>
<?php foreach ($articles as $key => $item): ?>
        <div class="news_page_item">
            <div class="news_page_img"><img src="<?= element('image', $item, '') ?>" style="width: 100%;height: 100%;" /></div>
            <div class="news_page_anons">
                <a href="/articles/<?= htmlspecialchars(element('alias', $item, ''), ENT_QUOTES) ?>" title="<?= htmlspecialchars(element('name', $item, ''), ENT_QUOTES) ?>"><?= element('name', $item, '') ?></a><br/>
                <span class="news_date" style="font-style: normal;"><?= date("d", strtotime(element('date', $item, ''))) . ' ' . get_month(date("F", strtotime(element('date', $item, '')))) . ' ' . date("Y", strtotime(element('date', $item, ''))) ?></span>
                <p class="news_preview"><?= element('anons', $item, '') ?>... <a href="/articles/<?= htmlspecialchars(element('alias', $item, ''), ENT_QUOTES) ?>" title="<?= htmlspecialchars(element('name', $item, ''), ENT_QUOTES) ?>">Читать дальше</a></p>
            </div>
        </div>
<?php endforeach; ?>
    <div style="clear:both;"></div>
    <div class="border_line"></div>
</div>
<?php if (isset($pagination)): ?> 
    <div class="clearfix space_bottom"></div>
    <div class="flatory_pagination__center">
    <?= $pagination ?>
    </div>
<?php endif; ?>
<!--/pagination-->
