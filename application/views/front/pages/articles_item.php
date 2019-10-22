<?php
function get_month($en){
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
    return  strtr($en, $trans);
}
?>
<div class="content_space">
    <?php foreach($articles_item as $key => $val): ?>
        <div style="float:left"><span class="news_date" style="line-height: 2.364;font-family: OpenSans_LightItalic;"><?=date("d", strtotime($val->date)).' '.get_month(date("F", strtotime($val->date))).' '.date("Y", strtotime($val->date))?></span></div>
        <div style="float:right; height: 20px;"><a class="news_read_more" style="position: relative;top:10px;font-size: 14px;" href="/articles/"><img style="margin-top: -2px;" src="/images/arrow.png" alt=""/>СТРОЙ-БЛОГ</a></div>
        <div style="clear:both;"></div>
        <h1 class="news_name"><?=$val->name?></h1>
        <div class="news_content"><?=$val->content?></div>
    <?php endforeach; ?>
    <div style="clear:both;"></div>
</div>
