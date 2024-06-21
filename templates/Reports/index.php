<div class="card">
    <div class="card-header">
        <h3>Select Options</h3>
    </div>
    <div class="card-body">
        <form id="costform" class="form-inline">
            <div class="form-group mb-2">
                <label for="account" class="mr-2">Account</label>
                <select class="form-control mr-2" id="account_id" name="account_id">
                    <?php
                    foreach ($accounts as $key => $val) {
                        echo "<option value=\"$val->id\">$val->company_name</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="start-date" class="mr-2">Start Date</label>
                <input type="text" class="form-control datepicker mr-2" id="start-date" name="start-date">
            </div>
            <div class="form-group mb-2">
                <label for="end-date" class="mr-2">End Date</label>
                <input type="text" class="form-control datepicker mr-2" id="end-date" name="end-date">
            </div>
            <div class="form-group mb-2">
                <label for="type" class="mr-2">Type</label>
                <select class="form-control mr-2" id="type" name="type">
                    <option value="monthly">Monthly</option>
                    <option value="hourly">Hourly</option>
                    <option value="DAILY">Daily</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary mb-2" onclick="costreport()">Submit</button>
        </form>
    </div>
</div>

<div class="mt-5">
    <table class="table table-bordered" id="result-table">
        <thead>
            <tr>
                <th>Start</th>
                <th>End</th>
                <th>Conversation</th>
                <th>Phone Number</th>
                <th>Country</th>
                <th>Conversation Type</th>
                <th>Conversation Category</th>
                <th>Cost</th>
            </tr>
        </thead>
        <tbody>
            <!-- Results will be appended here -->
        </tbody>
    </table>
</div>


<?php $this->start('script'); ?>
//<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });


        $('.datepicker input').datepicker({
            format: "yyyy-mm-dd",
            weekStart: 0,
            todayBtn: true,
            clearBtn: true
        });

    });

    // $('#costreport').on('submit', function(e) {
    //         e.preventDefault();
    function costreport() {
        var formData = $('#costform').serialize();
        // console.log(formData);
        $.ajax({
            beforeSend: function(xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            url: '/reports/conversation_analytics.json', // Replace with your API endpoint
            type: 'POST',
            data: formData,


            success: function(response) {
                var tableBody = $('#result-table tbody');
                tableBody.empty(); // Clear any previous results
                //  console.log(response.conversation_analytics.data[0].data_points)
                Dpoints = response.conversation_analytics.data[0].data_points;
                console.log(Dpoints)
                var totalCost = 0;
                Dpoints.forEach(function(item) {
                    totalCost += parseFloat(item.cost); // Accumulate the total cost
                    var row = '<tr>' +
                        '<td>' + new Date(item.start * 1000).toLocaleString() + '</td>' +
                        '<td>' + new Date(item.end * 1000).toLocaleString() + '</td>' +
                        '<td>' + item.conversation + '</td>' +
                        '<td>' + item.phone_number + '</td>' +
                        '<td>' + item.country + '</td>' +
                        '<td>' + item.conversation_type + '</td>' +
                        '<td>' + item.conversation_category + '</td>' +
                        '<td>' + item.cost + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });

                var totalCostRow = '<tr>' +
                    '<td colspan="7" style="text-align: right; font-weight: bold;">Total Cost</td>' +
                    '<td style="font-weight: bold;">' + totalCost.toFixed(2) + '</td>' +
                    '</tr>';
                tableBody.append(totalCostRow);

            }
        });
    };
    // 
</script>
<?php $this->end(); ?>