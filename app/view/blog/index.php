<?php
use My\Base\Helper\ViewHelper as V;

?>
<h1>Blog</h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
        <?php
    if ($total>0) {
    ?><p class="text-muted">Showing <?=count($list);?> out of <?=$total?> posts</p><?php
    } else {
        ?><p>No records</p><?php
    }
    foreach ($list as $item) {
        V::ShowBlock('layout/PostCard',['v'=>$item]);
    }
    echo $pagehtml;
?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
        <?php V::ShowBlock('blog/_topTags', ['tags' => $tags]) ?>
        <?php V::ShowBlock('blog/_archive', ['archive' => $archive]) ?>
    </div>
</div>
