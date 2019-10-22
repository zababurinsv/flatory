<?php if(!isset($message)): ?>
<h3 class="space_bottom search-message"><?php if(isset($message_no_found) && $message_no_found): echo $message_no_found; else: ?>Увы, но по Вашему запросу ничего не найдено. Попробуйте поискать по другим параметрам.<?php endif;?></h3>
<center><img src="/images/sad_little_house.png" alt="Я грустный маленький домик"></center>
<?php else: ?>
<h3 class="space_bottom search-message"><?= $message; ?></h3>
<?php endif; ?>

