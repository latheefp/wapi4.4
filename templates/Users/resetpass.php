<?php
//debug ($user);
//debug ($result);
?>
<div class="login-box">
  <div class="login-logo">
      <?php use Cake\Core\Configure; ?>
    <a href="/"><b><?= Configure::read('app.name')?></b><?= Configure::read('app.version') ?></a>
  </div>
  <?php
  if(isset($result['status'])){
      if($result['status']=="failed"){?>
  
      <div class="card-body login-card-body">
           <p class="login-box-msg"><?= $result['msg'] ?></p> <br>
             <div class="col-6">
              <a href="/admin" class="btn btn-primary btn-block">Login</a>
          </div>
      </div>
  
          
  <?php
  }?>
          
          
          <?php
  }else{
      ?>
    <div class="card" id="resetcard">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Password reset of <?= $user['email'] ?> </p>
      <?= $this->Form->create(null,[
          'url'=>'#',
          'id'=>"PasswordForm"
      ]); ?>
      <input type="hidden" name="id" value="<?= $user['id'] ?>">
       <input type="hidden" name="token" value="<?= $user['token'] ?>">
        <input type="hidden" name="email" value="<?= $user['email'] ?>">
        <div class="input-group mb-3">
            <input type="password" class="form-control" required name="password" id="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
            <input type="password" class="form-control" required name="password1" id="password1" placeholder="Confirm password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
            <div class="col-12"><h6 id="errormsg"></span></h6></div>
          <div class="col-6">
              <button type="button" disabled id="submitBtn" onclick="submitpass();" class="btn btn-primary btn-block">Submit</button>
          </div>
            <div class="col-6">
              <a href="/admin" class="btn btn-primary btn-block">Cancel</a>
          </div>
          <!-- /.col -->
          
          <!-- /.col -->
        </div>
      </form>

  </div>
</div>  
          <?php
      
  }
  
  ?>
    
    <div class="card" id="successcard" >
        <div class="card-body login-card-body">
            <div class="row">
                <div class="col-12"><h6 id="errormsg">Password has been reset, please login.</span></h6></div>
                <div class="col-6">
                  <a href="/admin" class="btn btn-primary btn-block">Login</a>
              </div>
            </div>
       </div>
    </div>  
    
    
    
</div>

<!--<script>-->

<?php $this->start('script'); ?>
<!--<script>-->
document.addEventListener("DOMContentLoaded", function(){
  <?php
  if(isset($msg)){?>
      toastr.<?= $msg['type'] ?>(' <?= $msg['msg'] ?>'); 
  <?php
  
  }
  ?>
});

$(document).ready(function () {
 $('#successcard').hide();
  $("#password, #password1").on("keyup", function () {
 //     alert("You are typing");
    // store the `newPassword` and `confirmPassword` in two variables
    var newPasswordValue = $("#password").val();
    var confirmPasswordValue = $("#password1").val();

    if (newPasswordValue.length > 5 && confirmPasswordValue.length > 5) {
      if (confirmPasswordValue !== newPasswordValue) {
        $("#password-does-not-match-text").removeAttr("hidden");
        $("#submitBtn").attr("disabled", true);
         $('#errormsg').html("Password should match");
      }
      if (confirmPasswordValue === newPasswordValue) {
        $("#submitBtn").removeAttr("disabled");
        $("#password-does-not-match-text").attr("hidden", true);
      }
    }else{
        $('#errormsg').html("Password length should be 6");
    }
  });
});

function submitpass(){
    var postData = $('#PasswordForm').serializeArray();
            $.ajax({
                url: '/users/pubpasswordsetajax',
                type: "POST",
                data: postData,
                 beforeSend: function (xhr) { // Add this line
                   xhr.setRequestHeader('X-CSRF-Token', csrfToken );
               },
                success: function(data) {
                    var obj = JSON.parse(data);
                    var status=obj.status;
                    var msg=obj.msg;
                    if( status == "Success"){
                        toastr.success(msg);
                        $('#password-modal').modal('hide')
                        $('#resetcard').hide();
                        $('#successcard').show();
                    }else{
                        toastr.error(msg);
                    }
                },
                error: function(jqXHR, status, error) {
                    console.log(status + ": " + error);
                }
            });
}

<?php $this->end(); 
?>