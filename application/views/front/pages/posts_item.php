<index-news>
<div class="content_space">
    <article>
        <div>
            <div style="float:left">
                <i class="news_date"><?= get_full_date_ru(element('created', $item)) ?></i>
            </div>
            <div style="float:right;height: 20px;">
<!--                <a class="news_read_more" style="position: relative;top:10px;font-size: 14px;" href="/--><?//= element('prefix', $category) ?><!--/"><img style="margin-top: -2px;" src="/images/arrow.png" alt=""/>--><?//= element('name', $category) ?><!--</a>-->
            </div>
            <div class="clearfix"></div>
        </div>
        <h1 class="news_name"><?= element('name', $item, '') ?></h1>
        <div class="news_content"><?= element('content', $item, '') ?></div>
        <div class="clearfix"></div>
    </article>
    <?php if ($tags): ?>
        <nav>
            <ul class="list-inline space_bottom">
                <li><b class="text-2">Теги:</b></li>
                <?php foreach ($tags as $it): ?>
                    <li><a href="/tags/<?= element('alias', $it, '') ?>" class="content-limk text-green"><?= element('name', $it, '') ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>
    <?php if ($read_more): ?>
        <b class="text-2">Читайте также:</b>
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
</div>
</index-news>

<script type="module">
        class news extends HTMLElement {
            constructor() {
                super();
            }
            connectedCallback() {
                (async ()=>{
                        let itemsObject = {}
                        let item = window.location.href.split('/')
                        itemsObject =    await  fetch(`${window.location['origin']}/api/item/${item[item.length-1]}`,{ method: 'GET' })
                        itemsObject =    await itemsObject.json()
                        this.querySelector('.news_content').insertAdjacentHTML('beforeend', itemsObject['description'])

                })()
            }
        }
        window.customElements.define('index-news', news);
</script>
