<div class="content_space">
    <h1 class="page-name"><?= element('name', $category) ?></h1>
    <?php foreach ($list as $key => $val): ?>
        <article class="news_page_item">
            <div class="news_page_img">
                <img src="<?= $val['image'] ?>" />
            </div>
            <div class="news_page_anons">
                <a href="/<?= element('prefix', $category) ?>/<?= urlencode($val['alias']); ?>" title="<?= htmlspecialchars($val['name'], ENT_QUOTES) ?>"><?= $val['name'] ?></a><br/>
                <span class="news_date"><?= get_full_date_ru(element('created', $val)) ?></span>
                <p class="news_preview"><?= $val['anons'] ?>... <a href="/<?= element('prefix', $category) ?>/<?= htmlspecialchars($val['alias'], ENT_QUOTES) ?>" title="<?= htmlspecialchars($val['name'], ENT_QUOTES) ?>">Читать дальше</a></p>
            </div>
        </article>
    <?php endforeach; ?>
    <div class="clearfix"></div>
    <div class="border_line"></div>
    <!--pagination-->
    <?php if (isset($pagination)): ?> 
        <div class="clearfix space_bottom"></div>
        <div class="flatory_pagination__center">
            <?= $pagination ?>
        </div>
    <?php endif; ?>
    <index-news></index-news>
    <script type="module">
        window.onload = function(){
            class news extends HTMLElement {
                constructor() {
                    super();
                }
                connectedCallback() {
                    (async ()=>{
                        let itemsObject = {}
                        // console.assert(false, window.location['pathname'])
                        switch (window.location['pathname']) {
                            case '/reviews/':
                                break
                            case '/news/':
                                itemsObject =    await  fetch(`${window.location['origin']}/api/item`,{ method: 'GET' })
                                itemsObject =    await itemsObject.json()

                                // console.assert(false, content)
                                for(let i =0; i < itemsObject.length;i++){
                                    let div = document.createElement('div')

                                    this.insertAdjacentHTML('beforeend', await  template(itemsObject[i]))

                                    // console.assert(false, this)

                                }
                                break
                            default:
                                break
                        }
                        function template(obj) {
                            return new Promise(function dispatchHttpRequest(resolve, reject) {
                                resolve( `
                                        <div class="content_space">
                                            <article>
                                                <div>
                                                    <div style="float:left">
                                                        <i class="news_date"></i>
                                                    </div>
                                                <div style="float:right;height: 20px;">
                                            <!--<a class="news_read_more" style="position: relative;top:10px;font-size: 14px;" href=""><img style="margin-top: -2px;" src="/images/arrow.png" alt=""/></a>-->
                                        </div>
                                        <div class="clearfix"></div>
                                        </div>
                                            <h1 class="news_name"></h1>
                                                    <div class="news_content"></div>
                                                        <div class="clearfix"></div>
                                            </article>
                                                <nav>
                                                    <ul class="list-inline space_bottom">
                                                        <li><b class="text-2">Теги:</b></li>
                                                        <li><a href="/tags" class="content-limk text-green"></a></li>
                                                    </ul>
                                                </nav>
                                                    <b class="text-2">Читайте также:</b>
                                        <ul class="list-unstyled">
                                                 <li class="news_page_item">
                                            <div class="news_page_img">
                                                <img src="/upload/3c1e8ee81702425119a5be122fb96c8b.jpeg" />
                                            </div>
                                            <div class="news_page_anons">
                                                <h4>${obj['name']}</h4>
                                                    <span class="news_date"></span>
                                                <p class="news_preview">
                                                    <a href="/news/preview/${obj['id']}" title="">Читать дальше</a>
                                                </p>
                                                    <b class="pull-right"></b>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    `)
                            })
                        }
                    })()
                }
                disconnectedCallback() {}
                componentWillMount() {}
                componentDidMount() {}
                componentWillUnmount() {}
                componentDidUnmount() {}
            }
            window.customElements.define('index-news', news);

        }
    </script>
</div>
