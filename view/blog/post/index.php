<?php

/**
 * @var \App\Blog\Entity\Post $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<h1><?=_h($post['title'])?></h1>
<div>
    <span class="text-muted"><?=$post['date_published_at']?> by</span>
    <a href="/user/<?=$post['login']?>"><?=$post['login']?></a></div>
<article class="text-justify"><?=_h($post['content'])?></article><?php
if (!empty($tags)) {
?><div class="mt-3"><?php
}
foreach ($tags as $v) {
    ?><a class="btn btn-outline-secondary btn-sm m-1" href="/blog/tag/<?=$v['label']?>"><?=_h($v['label'])?></a><?php
}
if (!empty($tags)) {
?></div><?php
}
?>
<h2 class="mt-4 text-muted">Comments</h2><div class="mt-3"><?php
    foreach ($comments as $v) {
?>
        <div class="media mt-4 shadow p-3 rounded">
            <div class="media-body">
                <div>
                    <a href="/user/<?=$v['login']?>"><?=$v['login']?></a>                    <span class="text-muted">
                        <i>created at</i> <?=$v['date_created_at'] ?>                    </span>
                    <?php
                    if ($v['public']) { ?>
                        <span class="text-muted">
                            <i>published at</i> <?=$v['date_published_at'] ?>
                        </span>
                    <?php
                    }
?>
                    <span><?php
                    if (!$v['public']) { 
                        ?><span class="border border-info rounded px-2 text-muted">hidden</span>
<?php
                    }
                    ?></span>
                </div>
                <div class="mt-1 text-justify">
                    <?=_h($v['content']) ?>
                </div>
            </div>
        </div>
        <?php
    }
    if(empty($comments)){
        ?><p class="lead">No comments</p><?php
    }
?></div>