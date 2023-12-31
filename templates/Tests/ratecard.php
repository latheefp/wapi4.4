<div class="content-header col-md-12 col-sm-12">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-9">
                <h1 class="m-0">Pricing Details<small></small></h1>
            </div>
            <div class="col-sm-3">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Utils</a></li>
                    <li class="breadcrumb-item active">Pricing</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="card border-primary mb-3">
        <div class="card-header">
            <h5> WhatsApp API Rate (up to June 1, 2023)</h5>
        </div>
        <div class="card-body">
            <form  id="costcalculation-form" action="/submit-form" method="post" >
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="select-country">Country</label>
                        <select class="form-control select2bs4" id="1select-country" name="country" onchange="submitFormcurrent()">
                            <?php
                            $template_id = null;
                            echo $this->Selectlist->buildlist([
                                'table' => 'PriceCards',
                                'where' => array(),
                                'selected' => $template_id,
                                'field' => 'country',
                                'placeholder' => 'Select Country'
                            ]);
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="select-message-type">Message Type
                            <span class="question-mark" data-toggle="tooltip" data-placement="top" title="Enter the type of message"></span>
                        </label>
                        <select class="form-control" id="1select-message-type" name="message_type" onchange="submitFormcurrent()">
                            <option value="business_Initiated_rate">User Initiated</option>
                            <option value="user_Initiated_rate">Business-initiated</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="select-message-type">Number of Messages
                        </label>
                        <select class="form-control" id="1input-number"  name="numbers" onchange="submitFormcurrent()">
                            <option value="100">100</option>
                            <option value="100">100 </option>
                            <option value="1000">1000</option>
                            <option value="5000">5000</option>
                            <option value="10000">10000</option>
                            <option value="100000">100000</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="select-message-type">Cost in INR</label>
                        <input class="form-control" readonly id="resultcurrent">
                    </div>
                </div>
                <!--                <button type="submit" class="btn btn-primary">Submit</button>-->
            </form>
        </div>
        <div class="card-footer">
            <a href="https://developers.facebook.com/docs/whatsapp/pricing" class="card-link">Conversation-Based Pricing</a>
        </div>
    </div>

    <div class="card border-primary mb-3">
        <div class="card-header">
            <h5> WhatsApp API Rate Calculator ( effective June 1, 2023) </h5>
        </div>
        <div class="card-body">
            <form  id="costcalculation-form" action="/submit-form" method="post" >
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="select-country">Country</label>

                        <select class="form-control select2bs4" id="select-country" name="country" onchange="submitForm()">
                            <?php
                            $template_id = null;
                            echo $this->Selectlist->buildlist([
                                'table' => 'PriceCards',
                                'where' => array(),
                                'selected' => $template_id,
                                'field' => 'country',
                                'placeholder' => 'Select Country'
                            ]);
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="select-message-type">Message Type
                            <span class="question-mark" data-toggle="tooltip" data-placement="top" title="Enter the type of message"></span>
                        </label>
                        <select class="form-control" id="select-message-type" name="message_type" onchange="submitForm()">
                            <option value="marketing">Marketing</option>
                            <option value="utility">Utility </option>
                            <option value="authentication">Authentication</option>
                            <option value="service">Service</option>
                            <option value="user_Initiated_rate">Customer Initiated</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="select-message-type">Number of Messages
                        </label>
                        <select class="form-control" id="input-number"  name="numbers" onchange="submitForm()">
                            <option value="100">100</option>
                            <option value="100">100 </option>
                            <option value="1000">1000</option>
                            <option value="5000">5000</option>
                            <option value="10000">10000</option>
                            <option value="100000">100000</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="select-message-type">Cost in INR</label>
                        <input class="form-control" readonly id="result">
                    </div>
                </div>
                <!--<button type="submit" class="btn btn-primary">Submit</button>-->
            </form>
        </div>
        <div class="card-footer">
            <a href="https://developers.facebook.com/docs/whatsapp/updates-to-pricing" class="card-link">Details About facebook  conversation categories  </a>
        </div>
    </div>
</div>





<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>



        function submitForm() {
            var resultDiv = document.getElementById('result');
            var form = $("#costcalculation-form")
            var url = '/ajaxes/getrate';
            $.ajax({
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function (data) {
                    var obj = JSON.parse(data);
                    var status = obj.status;
                    var msg = obj.msg;
                    if (status == "success") {
                        //    $('#result').html("The total Amount is Rs.<b>" + msg + ".</b>");
                        resultDiv.value = msg;
                    } else {

                        toastr.error(msg);
                    }
                }
            });

        }

        function submitFormcurrent() {
            var resultDiv = document.getElementById('resultcurrent');
            var form = $("#costcalculation-form")
            var url = '/ajaxes/getrate';
            $.ajax({
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function (data) {
                    var obj = JSON.parse(data);
                    var status = obj.status;
                    var msg = obj.msg;
                    if (status == "success") {
                        //    $('#result').html("The total Amount is Rs.<b>" + msg + ".</b>");
                        resultDiv.value = msg;
                    } else {

                        toastr.error(msg);
                    }
                }
            });

        }













    <?php $this->Html->scriptEnd(); ?>
