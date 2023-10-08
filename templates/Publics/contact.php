<div class="content-header col-md-12 col-sm-12">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-9">
                <h1 class="m-0">Contact Us.<small></small></h1>
            </div>
            <div class="col-sm-3">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Utils</a></li>
                    <li class="breadcrumb-item active">Contacts</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="card" id="ContacFormDiv">
        <div class="card-header card bg-info text-white">
            <h5>Contact Form</h5>
        </div>
        <div class="card-body">
            <form id="contactForm" novalidate id="contactForm" class="border p-4">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="name">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="name" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="name">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="name" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">Company</label>
                        <input type="email" class="form-control" name="company" id="company" placeholder="Company" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="contactNumber">Contact Number</label>
                        <input type="tel" class="form-control" name="contact_number" id="contactNumber" placeholder="Enter your contact number" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="expectedMsgPerMonth">Approximate Messages/Month</label>
                        <input type="number" class="form-control" name="msg_per_month" id="expectedMsgPerMonth" placeholder="Enter the expected number of messages per month" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="expectedMsgPerDay">Approximate Unique Messages/Day</label>
                        <input type="number" class="form-control" name="msg_per_day" id="expectedMsgPerDay" placeholder="Enter the expected number of unique messages per day" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="targetCountry">Primary Target Country</label>
                        <select class="form-control" name="target_country" id="targetCountry" required>
                            <option value="">Select a country</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="otherDetails">Other Details</label>
                    <textarea class="form-control" name="details" id="otherDetails" rows="3" placeholder="Enter any additional details"></textarea>
                </div>
                <button type="button" class="btn btn-success" onclick="submitForm()">Submit</button>
            </form>
        </div>
    </div>



    <div class="card" id="ContacFormSuccessDiv" style="display: none;">
        <div class="card-header card bg-info text-white">
            <h5>Contact Form</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <strong>Thank you for contacting us!</strong> Our team will contact you soon to discuss further on this.
            </div>

        </div>
    </div>


</div>




<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>

    // Validate the contact form using Bootstrap's native form validation
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            // Fetch all the forms to apply custom Bootstrap validation styles
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);


        $('#targetCountry').select2({
            placeholder: 'Select a country',
            allowClear: true,
            ajax: {

                url: 'https://restcountries.com/v3.1/all',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {

                    data.sort(function (a, b) {
                        var textA = a.name.common.toUpperCase();
                        var textB = b.name.common.toUpperCase();
                        return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                    });
                    var countries = data.map(function (country) {
                        return {
                            id: country.name.common,
                            text: country.name.common
                        };
                    });
                    return {
                        results: countries
                    };
                },
                cache: true
            }
        });




    })();



    function submitForm() {
        var resultDiv = document.getElementById('result');
        var form = $("#contactForm")
        var url = '/ajaxes/contact';
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
                    toastr.success(msg);
                    var div = document.getElementById("ContacFormDiv");
                    div.style.display = "none";
                    var div = document.getElementById("ContacFormSuccessDiv");
                    div.style.display = "block";
                } else {
                    toastr.error(msg);

                }
            }
        });

    }



    <?php $this->Html->scriptEnd(); ?>
