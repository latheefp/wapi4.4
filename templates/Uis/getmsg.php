  <div class="row message-previous">
      <div class="col-sm-12 previous">
          <a onclick="previous(this)" id="<?= $contact_stream_id ?>" name="20">
              Show Previous Message!
          </a>
      </div>
  </div>

  <?php
    $messages = array_reverse($messages);
    foreach ($messages as $key => $val) {
        switch ($val->type) {
            case "send":
            case "ISend":
    ?>
              <div class="row message-body">
                  <div class="col-sm-12 message-main-sender">
                      <div class="sender">
                          <div class="message-text">
                              <?= $val->msg ?>
                          </div>
                          <span class="message-time pull-right" title="<?= $this->Dformat->format(['data' => $val->created, 'format' => 'DT2DT']) ?>">
                              <?= $this->Time->timeAgoInWords($val->created) ?>
                          </span>
                      </div>
                  </div>
              </div>

          <?php
                break;
            case "receive":
              //  debug(json_decode($val->recievearray,true));
            ?>
              <div class="row message-body">
                  <div class="col-sm-12 message-main-receiver">
                      <div class="receiver">
                          <div class="message-text">
                              <?php
                              $data['json']=$val->recievearray;
                                $this->RcvDataformat->format($data) 
                                ?>
                          </div>
                          <span class="message-time pull-right" title="<?= $this->Dformat->format(['data' => $val->created, 'format' => 'DT2DT']) ?>">
                              <?= $this->Time->timeAgoInWords($val->created) ?>
                          </span>
                      </div>
                  </div>
              </div>
  <?php
                break;
            default:
                debug($val);
        }
    }
