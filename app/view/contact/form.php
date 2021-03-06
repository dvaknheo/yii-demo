<?php
use Yiisoft\Yii\Bootstrap4\Alert;
//var_dump(array_keys(get_defined_vars());
$url_contact='/contact';
if (isset($sent)) {
    echo Alert::widget()
              ->options(['class' => $sent ? 'alert-success' : 'alert-danger'])
              ->body(
                  $sent
                      ? 'Thanks to contact us, we\'ll get in touch with you as soon as possible.'
                      : _h($error)
              );
}
?>
<form id="contactForm"
      method="POST"
      action="<?= $url_contact ?>"
      enctype="multipart/form-data"
>
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject"
               value="<?= _h($body['subject'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Email Address"
               value="<?= _h($body['email'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Name"
               value="<?= _h($body['name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="content">Content</label>
        <textarea class="form-control" name="content" id="content"
                  placeholder="Content" required><?= _h($body['content'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>File</label>
        <input type="file" class="form-control-file" name="file">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
