<?php

/**
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator;
 * @var \App\Blog\Entity\Tag $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?><h1><?=_h($item['label'])?></h1><ul><?php
    foreach ($list as $v) {
        ?><li class="text-muted"><a href="/blog/page/<?=$v['slug']?>"><?=_h($v['title'])?></a> by <a href="/user/<?=$v['login']?>"><?=_h($v['login'])?></a> at <span><?=$v['date_published_at']?></span></li><?php
    }
?></ul>