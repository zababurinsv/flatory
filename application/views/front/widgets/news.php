<section class="left_news">
    <h3><a href="/news/" class="news-block-title">Новости</a></h3>
    <ul class="list-unstyled">
        <?php foreach ($news as $item): ?>
            <li>
                <article>
                    <h4><a href="/news/<?= htmlspecialchars($item['alias'], ENT_QUOTES) ?>" title="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" class="news-title-link"><?= $item['name'] ?></a></h4>
                    <span class="news_blog_preview" ><?= $item['anons'] ?></span>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
</section>