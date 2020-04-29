<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;
?>
<h1>Archive</h1>
<div class="row">
    <div class="col-sm-12">
        <?php
    $currentYear = null;
    foreach ($archive as $v) {
        $monthName=Date('F', mktime(0, 0, 0, (int)$v['month'], 1, (int)$v['year']));
        if ($currentYear !== $v['year']) {
        ?><li class="list-group-item d-flex flex-column justify-content-between lh-condensed"><?php
            ?><a class="h5" href="/blog/archive/<?=$v['year']?>"><?=$v['year']?></a><?php
            ?><div class="d-flex flex-wrap"><?php
        }
        $currentYear = $v['year'];
                ?><div class="mx-2 my-1"><a class="text-muted" href="/blog/archive/<?=$v['year']?>-<?=$v['month']?>"><?=$monthName?></a> <sup class=""><?=$v['count']?></sup></div><?php
    }
            ?></div><?php
        ?></li><?php
    if(empty($archive)){
        ?><li class="list-group-item d-flex flex-column justify-content-between lh-condensed">No records</li><?php
    }
    ?>
    </div>
</div>
