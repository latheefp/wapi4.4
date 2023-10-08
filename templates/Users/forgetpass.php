<div class="login-box">
  <div class="login-logo">
      <?php use Cake\Core\Configure; ?>
    <a href="/"><b><?= Configure::read('app.name')?></b><?= Configure::read('app.version') ?></a>
  </div>
  <!-- /.login-logo -->
  <div class="card" id="resetform">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Reset  Password</p>
      <!--<form action="/login" method="post">-->
      <?= $this->Form->create(); ?>
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="phone" placeholder="Phone">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="row">
            <div class="col-6">
              <button type="submit" class="btn btn-primary btn-block">Forget Password</button>
          </div>
          <!-- /.col -->
           <div class="col-6">
                   <a href="/login" class="btn btn-primary btn-block">Login</a>
          </div>
          <!-- /.col -->
        </div>
      </form>

  </div>
</div>
  
  <div class="card card-succes" id="msgcard" style="">
    <div class="card-body">
        <div id="successmsg" class="label label-success"></div>
    </div>
</div>
  
  <?php
 // debug($result);
  ?>
</div>


<?php 
 if(isset($result)){
    $this->start('script');
     ?>
document.addEventListener("DOMContentLoaded", function(){
    <?php if($result['status']=="success"){?>
        toastr.success(' <?= $result['msg'] ?>');   
        <!--temporary-->
        alert(' <?= $result['link'] ?>');  
        $('#successmsg').html('<?= $result['msg'] ?>');
        $('#resetform').hide();
        $('#msgcard').show();
      <?php }else{ ?>
        toastr.error(' <?= $result['msg'] ?>'); 
      <?php } ?>
         });
    <?php  
    
        $this->end(); 
      } ?>
