<?php echo $this->Form->create(); ?>
<!--<form action="/users/add" method="post">-->
  <div class="form-group has-feedback">
    <input name="name" type="text" class="form-control" placeholder="Full name">
    <span class="glyphicon glyphicon-user form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <input name="username" type="text" class="form-control" placeholder="User Name">
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <input name="password" type="password" class="form-control" placeholder="Password">
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <input name="password1" type="password" class="form-control" placeholder="Retype password">
    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
  </div>
  <div class="row">
    <div class="col-xs-8">
      <div class="checkbox icheck">
        <label>
          <input type="checkbox"> I agree to the <a href="#">terms</a>
        </label>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-4">
      <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
    </div>
    <!-- /.col -->
  </div>
<?= $this->Form->end(); ?>
</form>