<?php
?>
<h4 class="text-muted mb-3">Archive</h4>
<ul class="list-group mb-3">
    <?php
        $currentYear = null;    
        foreach ($archive as $v) {
            $monthName=Date('F', mktime(0, 0, 0, (int)$v['month'], 1, (int)$v['year']));
            if ($currentYear !== $v['year']) {
                // print Year
                ?><li class="list-group-item d-flex flex-column justify-content-between lh-condensed"><h6 class="my-0"><?=$v['year']?></h6><?php
            }
            $currentYear = $v['year'];
            ?><div class="d-flex justify-content-between align-items-center"><a class="text-muted" href="/blog/archive/<?=$v['year']?>-<?=$v['month']?>"><?=$monthName?></a> <span class="badge badge-secondary badge-pill"><?=$v['count']?></span></div><?php
            
        }
        if(!empty($archive)){
        ?><a class="mt-2" href="/blog/archive">Open archive</a></li><?php 
        }
    if(empty($archive)){
?>
    <li class="list-group-item d-flex flex-column justify-content-between lh-condensed">No records</li><?php
    }

    ?>
</ul>
