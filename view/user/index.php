<?php

?>
<h1>Users</h1><p class="text-muted">Total users: <?= $total?></p><a class="btn btn-link" href="/api/info/v1">API v1 Info</a><br><a class="btn btn-link" href="/api/info/v2">API v2 Info</a><br><a class="btn btn-link" href="/api/user">API Users List Data</a><br><table class="table table-hover">
    <thead>
    <tr>
        <th scope="col">Name</th>
        <th scope="col">Created at</th>
    </tr>
    </thead>
    <tbody>
<?php
foreach ($data as $v) {
    $time=date('D, d M Y H:i:s +0000',strtotime(($v['created_at']))); // +0000 => O ?
?>
<tr><?php ?>
<td><a class="btn btn-link" href="/user/<?=$v['login']?>"><?=$v['login']?></a><a class="btn btn-link" href="/api/user/<?=$v['login']?>">API User Data</a></td><?php ?>
<td><?=$time?></td><?php ?>
</tr><?php
}
?>
    </tbody>
</table>
<?= $pager;