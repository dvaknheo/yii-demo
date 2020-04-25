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
?>
<tr><td><a class="btn btn-link" href="<?=$url_xx?>"><?=$xx?></a><a class="btn btn-link" href="<?=$url_xx?>">API User Data</a></td><td>abc</td></tr><?php
}
?>
    </tbody>
</table>
<?php
echo $pagination;
