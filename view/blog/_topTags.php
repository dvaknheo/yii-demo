<?php
?>
<h4 class="text-muted mb-3">
    Popular tags
</h4>
<ul class="list-group mb-3">
    <li class="list-group-item d-flex flex-column justify-content-between lh-condensed"><?php
    foreach ($tags as $v) {
?><div class="d-flex justify-content-between align-items-center"><a class="text-muted overflow-hidden" href="/blog/tag/<?=$v['label']?>"><?=_h($v['label']);?></a> <span class="badge badge-secondary badge-pill"><?=$v['count']?></span></div><?php
    }
    if (empty($tags)) {
        echo 'tags not found';
    }
    ?></li></ul>
