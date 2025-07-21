<div class="login-box">
  <div class="login-logo">
    <?php

    use Cake\Core\Configure; ?>
    <a href="/"><b><?= Configure::read('app.name') ?></b> <?= Configure::read('app.version') ?></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>
      <!--<form action="" method="post">-->
      <?= $this->Form->create(); ?>
      <div class="input-group mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-envelope"></span>
          </div>
        </div>
      </div>
      <div class="input-group mb-3">
        <input type="password" class="form-control" name="password" placeholder="Password">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
      </div>
      <!-- <div class="row">
                 <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
                <div class="col-6">
                   <a href="/forgetpass" class="btn btn-primary btn-block">Forget Passowrd</a>
                </div>
            </div> -->



      <div class="row">
        <div class="col-6">
          <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </div>
        <div class="col-6">
          <a href="/forgetpass" class="btn btn-primary btn-block">Forget Password</a>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-12">
          <button class="btn btn-primary btn-block" onclick="launchWhatsAppSignup();">Login with Facebook</button>
        </div>
      </div>



      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<?php $this->start('script'); ?>
document.addEventListener("DOMContentLoaded", function(){
<?php
if (isset($msg)) { ?>
  toastr.<?= $msg['type'] ?>(' <?= $msg['msg'] ?>');
<?php

}
?>
});
<?php $this->end();

?>