<?php
use My\Base\Helper\ViewHelper as V;

?>
<h1>Archive <small class="text-muted"><?php echo "$monthName $year" ?></small></h1>
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
        //echo PostCard::widget()->post($item);
    }
    echo $pagehtml;
?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
    </div>
</div>
