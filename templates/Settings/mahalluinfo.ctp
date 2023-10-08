 <section class="content-header">
      <h1>
        <?= __("Mahallu information"); ?>
        <small>Preview</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active"><?= __("Mahallu information"); ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
          <div class="box box-info">
                <?php
                echo $this->Form->create(null, ['inline' => true]);
                ?>
              <div class="box-body">
                <div class="col-md-6">
                    <?php
                    echo $this->Form->input('mahallu_name', ['type' => 'text','value'=>$info['mahallu_name'],'label'=>__("Name of Mahallu")]) ;
                    echo $this->Form->input('mahallu_place', ['type' => 'text', 'value'=>$info['mahallu_place'],'label'=>__("Place")]) ;
                    echo $this->Form->input('mahallu_post', ['type' => 'text','value'=>$info['mahallu_post'],'label'=>__("Post")]) ;
                    echo $this->Form->input('mahallu_pincode', ['type' => 'number','value'=>$info['mahallu_pincode'],'label'=>__("Pincode")]) ;
                    echo $this->Form->input('mahallu_district', ['type' => 'text','value'=>$info['mahallu_district'],'label'=>__("District")]) ;
                    echo $this->Form->input('mahallu_state', ['type' => 'text','value'=>$info['mahallu_state'],'label'=>__("State")]) ;
                    echo $this->Form->input('mahallu_phone', ['type' => 'text','value'=>$info['mahallu_phone'],'label'=>__("Phone Number")]) ;
                    ?>
                </div>
                <div class="col-md-6">
                    <?php    
                    echo $this->Form->input('mahallu_email', ['type' => 'email','value'=>$info['mahallu_email'],'label'=>__("Email")]) ;
                    echo $this->Form->input('mahallu_fax', ['type' => 'text','value'=>$info['mahallu_fax'],'label'=>__("Fax")]) ;
                    echo $this->Form->input('mahallu_wakf_reg_no', ['type' => 'text','value'=>$info['mahallu_wakf_reg_no'],'label'=>__("Wakf Reg Number")]) ;
                    echo $this->Form->input('mahallu_established', ['type' => 'text', 'class'=>' datepicker-mh datepicker-inline','value'=>$info['mahallu_established'],'label'=>__("Establised on")]) ;
                    echo $this->Form->input('mahallu_area', ['type' => 'text','value'=>$info['mahallu_area'],'label'=>__("Area")]) ;
                    echo $this->Form->input('mahallu_description', ['type' => 'textarea','value'=>$info['mahallu_description'],'label'=>__("Description")]) ;
                    ?>
                </div>
              </div>
              <?php 
              echo $this->Form->submit('Submit') ;
              echo $this->Form->end() ;
              ?>
          </div>
          <!-- /.box -->
      <div class="box box-default">
      </div>
    </section>
    
    <?php// echo debug ($info); ?>