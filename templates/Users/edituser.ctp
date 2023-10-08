<?php $this->Html->script('users/edituser', ['block' => true]); ?>
    <section class="content-header">
        <h1>
        <?= __("New Member"); ?>
        <small>Preview</small>
      </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="/users/listusers/"> Users</a></li>
            <li class="/users/edituser">
                <?= __(" Edit User"); ?>
            </li>
        </ol>
    </section>
    <section class="content">
        <?php
     //   echo debug ($user);
//$user=$user[0];

?>

            <?php echo $this->Form->create('members',
                [
                    'type'=>'post',
                    'class'=>'form-horizontal',
                    'url'=>'/users/edituser'
                    ]
                ); ?>
                <div class="box box-info">
                    <fieldset class="scheduler-border col-md-12">
                        <legend class="scheduler-border"><?= __("Edit User");?></legend>
                        <input id="id" type="hidden" name="id" value="<?= $user['id'];  ?>">
                        <div class="row form-group  form-group-sm">
                                    <div class="col-md-3">
                                        <label for="first_name">First Name</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" name="first_name" value="<?= $user->first_name;  ?>" id="first_name" placeholder="First Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="last_name">Last Name</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control" type="text" name="last_name" value="<?= $user->last_name;  ?>" id="last_name" placeholder="Last name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="email">Email</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control" type="email" name="email" value="<?= $user->email;  ?>" id="email" placeholder="email">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="phone">Mobile No.</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                            <input type="text" class="form-control" name="phone" value="<?= $user->phone;  ?>" id="phone" placeholder="Phone">
                                        </div>
                                    </div>
                         </div>
                         <div class="row form-group   form-group-sm">
                                    <div class="col-md-3 input-group-sm">
                                        <label for="group_id">Group</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-anchor"></i></span>
                                            <select id="group_id" name="group_id[]" class="form-control" multiple="multiple">
                                                <?php
                                                foreach ($groups as $key =>$val){
                                                    print "<option value=$key>$val</option>";
                                                }
                                               ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type='submit' class='button primary pull-right'>Submit</button>
                    </fieldset>
                </div>
                <?php echo $this->Form->end() ?>
                  
    </section>