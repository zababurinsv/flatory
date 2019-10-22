<div class="clear" ></div>
    <div class="banner_bottom"></div>
    <div class="block_menu">
        <div style="overflow: hidden;margin-top: 24px;height:30px; width: 129px;">
            <span style="font-family: OpenSans_Bold;font-size: 11px;color: rgb( 152, 152, 152 );line-height: 1.364;">© 2013 — <?=date('Y')?>  Flatory.ru</span><br />
            <span style="font-family: OpenSans_Regular;font-size: 11px;color: rgb( 152, 152, 152 );line-height: 1.364;">Каталог новостроек</span>
        </div>
        <ul class="nav-social">
            <li><a href="https://www.facebook.com/flatory.ru" class="social_gray-fb" target="_blank"></a></li>
            <li><a href="http://vk.com/flatory" class="social_gray-vk" target="_blank"></a></li>
            <li><a href="http://instagram.com/flatory_ru" class="social_gray-instagram" target="_blank"></a></li>
        </ul>
        <?= $nav ?>
        <?php if(ENVIRONMENT !== 'development'): ?>
        <div class="enter">
        </div>
        <?php endif; ?>
    </div>

