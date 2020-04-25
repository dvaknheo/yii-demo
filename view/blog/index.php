<?php


?>
<h1>Blog</h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
        <?php
$pageSize = $paginator->getCurrentPageSize();
if ($pageSize > 0) {
    echo Html::tag(
        'p',
        sprintf('Showing %s out of %s posts', $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
} else {
    echo Html::tag('p', 'No records');
}
?>
<?php
/** @var Post $item */
foreach ($paginator->read() as $item) {
    echo PostCard::widget()->post($item);
}
echo $pager
        ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
        <?php echo $this->render('_topTags', ['tags' => $tags]) ?>
        <?php echo $this->render('_archive', ['archive' => $archive]) ?>
    </div>
</div>
