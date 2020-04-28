<?php

?>
<h1>Archive <small class="text-muted">Year <?php echo $year ?></small></h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
<?php
    if(true){ 
?>
        <p class="text-muted">Total 12 posts</p><?php
    }else{
?>
        <p>No records</p><?php
    }
?>
<?php
    $currentMonth = null;
    foreach ($items as $v) {
        if ($currentMonth !== $v['month']) {
            $currentMonth = $v['month'];
        ?><div class="lead"><?=$year?> <?=$v['monthName']?></div><?php
        }
        ?><div><a href="/blog/page/<?=$v['slug']?>"><?=_h($v['title'])?></a> by <a href="/user/<?=$v['login']?>"><?=_h($v['login']);?></a></div><?php
}
?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3"></div>
</div>
