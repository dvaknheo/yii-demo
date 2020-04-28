<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<h4 class="text-muted mb-3">Archive</h4>
<ul class="list-group mb-3">
    <?php
        $currentYear = null;    
        foreach ($archive as $v) {

            if ($currentYear !== $v['year']) {
                // print Year
                ?><li class="list-group-item d-flex flex-column justify-content-between lh-condensed">No records</li><h6 class="my-0"><?=$v['year']?></h6><?php
            }
            $currentYear = $v['year'];
            Date('F', mktime(0, 0, 0, (int)$month, 1, (int)$year));
            ?>
            <div class="d-flex justify-content-between align-items-center"><a class="text-muted" href="/blog/archive/2020-3">March</a> <span class="badge badge-secondary badge-pill">1</span></div><?php
            
        }
        if(empty($archive)){
        ?><a class="mt-2" href="/blog/archive">Open archive</a>
        </li>
        <?php }
    if(empty($archive)){
?>
    <li class="list-group-item d-flex flex-column justify-content-between lh-condensed">No records</li><?php
    }

    ?>
</ul>
