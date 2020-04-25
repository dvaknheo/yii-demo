<?php

use Yiisoft\Html\Html;
$url_signup = '/signup';
$error = $error ?? null;
?>

<?php if ($error !== null): ?>
<div class="alert alert-danger" role="alert">
  <?= Html::encode($error) ?>
</div>
<?php endif ?>

<form id="signupForm" method="POST" action="<?= $url_signup?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="form-group">
    <label for="subject">Login</label>
      <input type="text" class="form-control" name="login" value="" required>  </div>
  <div class="form-group">
    <label for="email">Password</label>
      <input type="password" class="form-control" name="password" value="" required>  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

