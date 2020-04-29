<?php
?>
<div id="w0-post-card" class="card mb-4" data-post-slug="<?=$v['slug']?>">
<div class="card-body d-flex flex-column align-items-start">
<a class="mb-0 h4 text-decoration-none" href="/blog/page/<?=$v['slug']?>"><?=_h($v['title'])?></a>
<div class="mb-1 text-muted"><?=$v['month_published_at']?> by <a href="/user/<?=$v['user']['login']?>"><?=$v['user']['login']?></a></div><p class="card-text mb-auto"><?=_h($v['content_short'])?></p>
<div class="mt-3"><?php
    foreach($v['tags'] as $tag){
?>
<a class="btn btn-outline-secondary btn-sm mx-1 mt-1" href="/blog/tag/<?=$tag['label']?>"><?=$tag['label']?></a><?php
    }
?>
</div>
</div>
</div>