<?php if($organizations) : ?>
<table>
    <tbody>
        <tr>
            <td class="item_data">
                <h3 class="space_bottom_l"><?= $organization_type ?></h3>
            </td>
            <td class="item_control">
                <span class="fl_arrow fl_next"></span>
            </td>
        </tr>
    </tbody>
</table>
<ul class="item_meta">
    <?php foreach ($organizations as $o): ?>
        <li><a href="/kartoteka/organizations/<?= element('alias', $o) ?>"><?= element('name', $o) ?></a></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>