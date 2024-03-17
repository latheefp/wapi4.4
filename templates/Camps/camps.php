
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <span class="badge"><?= $response['msg']['status'] ?></span>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="message-container">
                <p class="message"><?= $response['msg']['message'] ?></p>
            </div>
        </div>
    </div>
</div>


<style>
.badge {
    font-size: 38px; /* Adjust font size as needed */
    background-color: orange;
}

.message-container {
    background-color: #007bff; /* Blue background color */
    padding: 10px;
    border-radius: 5px;
}

.message {
    font-size: 30px; /* Adjust font size as needed */
    color: #fff; /* White font color */
}

</style>