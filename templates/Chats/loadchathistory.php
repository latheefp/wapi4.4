
  <?php
  if(is_array(($messages))){
    $messages = array_reverse($messages);
  }
   
    foreach ($messages as $key => $val) {
   //     debug($val);
        switch ($val->type) {

            case "receive":
    ?>
              <div class="row message-body" "id"=<?= $val->id ?>>
                  <div class="col-sm-12 message-main-receiver">
                      <div class="receiver">
                          <div class="message-text" id="<?$val->messageid ?>">
                              <?php
                                $data['json'] = $val->recievearray;
                                $data['stream_id'] = $val->id;
                                echo $this->RcvDataformat->format($data)
                                ?>
                          </div>
                          <span  class="message-time pull-right" title="<?= $this->Dformat->format(['data' => $val->created, 'format' => 'DT2DT']) ?>">
                              <?= $this->Time->timeAgoInWords($val->created) . "Rcv"?>
                          </span>
                      </div>
                  </div>
              </div>
          <?php
                break;
            default:

            ?>
              <div class="row message-body" "id"=<?= $val->id ?> >
                  <div class="col-sm-12 message-main-sender">
                      <div class="sender">
                          <div class="message-text" id="<?$val->messageid ?>">
                              <?php
                                $data['json'] = $val->sendarray;
                                $data['stream_id'] = $val->id;
                                echo $this->SendDataformat->format($data)
                                ?>
                          </div>
                          <span class="message-time pull-right" title="<?= $this->Dformat->format(['data' => $val->created, 'format' => 'DT2DT']) ?>">
                              <?= $this->Time->timeAgoInWords($val->created) ."Send" ?>

                          </span>
                      </div>
                  </div>
              </div>

  <?php
                break;
        }
    }
