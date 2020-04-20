<?php

use Yiisoft\Html\Html;

/**
 * @var $this \Yiisoft\View\View
 * @var $urlGenerator \Yiisoft\Router\UrlGeneratorInterface
 * @var $csrf string
 */
$url_login = $urlGenerator->generate('site/login');
$error = $error ?? null;
?>

<?php if ($error !== null): ?>
<div class="alert alert-danger" role="alert">
  <?= Html::encode($error) ?>
</div>
<?php endif ?>

<form id="loginForm" method="POST" action="<?= $url_login ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="form-group">
    <label for="subject">Login</label>
      <input type="text" class="form-control" name="login" value="" required>  </div>
  <div class="form-group">
    <label for="email">Password</label>
      <input type="password" class="form-control" name="password" value="" required>  </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

