<?php 
///debug($contacts);
foreach ($contacts as $key => $val) { 
    
    if (!empty($val->name)){
        $name=$val->name;
    }elseif(!empty($val->profile_name)){
        $name=$val->contact_number."<br>". $val->profile_name;
    }else{
        $name=$val->contact_number;
    }
  //  debug($name);
    ?>

    

    
    <div class="row sideBar-body" id="sidebar-body-<?= $val->contact_stream_id ?>">
        <div class="col-sm-3 col-xs-3 sideBar-avatar">
            <div class="avatar-icon">
                <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
            </div>
        </div>
        <div class="col-sm-9 col-xs-9 sideBar-main" onclick="loadchat(<?= $val->contact_stream_id ?>,'<?= $name ?>')">
            <div class="row">
                <div class="col-sm-6 col-xs-6 sideBar-name">
                    <span class="name-meta" title="<?= $val->contact_number ?>"><?= $name ?></span>
                </div>
                <div class="col-sm-6 col-xs-6 pull-right sideBar-time">
                    <span class="time-meta pull-right" title="<?= $this->Dformat->format(['data'=>$val->created,'format'=>'DT2DT']) ?>"><?= $this->Time->timeAgoInWords($val->created) ?>
                    </span>  
                </div>
            </div>
        </div>
    </div>
<?php } ?>


