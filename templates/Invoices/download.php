
<?php
 debug($selecteInvoice);
// debug($account);
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 body-main">
            <div class="col-md-12">
               <div class="row">
                    <div class="col-md-4">
                        <img class="img" alt="Invoce Template" src="/img/wajunction.png" />
                    </div>
                    <div class="col-md-6 text-right">
                        <h4 style="color: #F81D2D;"><strong>WAJunction</strong></h4>
                        <h6 style="color: #F81D2D;">Massaing Gateway</h6>
                    </div>
                    <div class="col-md-6 text-left">
                    <br>
                        <h5>To: </h5>
                        <p>
                            <?= $account->primary_contact_person ?>t<br>
                            <?= $account->company_name ?>t<br>
                            <?= $account->Address ?>t<br>
                            <?= $account->primary_number ?>t<br>
                          
                        </p>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>INVOICE</h2>
                        <h5><?=  $selecteInvoice->invoice_number ?></h5>
                    </div>
                </div>
                <br />
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><h5>Description</h5></th>
                                <th><h5>Amount</h5></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-md-9">Monthly Bill of  <?=   $selecteInvoice->month . " of " .  $selecteInvoice->year ?></td>
                                <td class="col-md-3"><i class="fas fa-rupee-sign" area-hidden="true"></i> <?=   $selecteInvoice->total_amount ?> </td>
                            </tr>
                       
                            <tr>
                                <td class="text-right">
                                <!-- <p>
                                    <strong>Shipment and Taxes:</strong>
                                </p> -->
                                <p>
                                    <strong>Total Amount: </strong>
                                </p>
							    <p>
                                    <strong>Outstanding: </strong>
                                </p>
							    <p>
                                    <strong>Payable Amount: </strong>
                                </p>
							    </td>
                                <td>
                                <p>
                                    <strong><i class="fas fa-rupee-sign" area-hidden="true"></i> 500 </strong>
                                </p>
                                <p>
                                    <strong><i class="fas fa-rupee-sign" area-hidden="true"></i> 82,900</strong>
                                </p>
							    <p>
                                    <strong><i class="fas fa-rupee-sign" area-hidden="true"></i> 3,000 </strong>
                                </p>
							    <p>
                                    <strong><i class="fas fa-rupee-sign" area-hidden="true"></i> 79,900</strong>
                                </p>
							    </td>
                            </tr>
                            <tr style="color: #F81D2D;">
                                <td class="text-right"><h4><strong>Total:</strong></h4></td>
                                <td class="text-left"><h4><strong><i class="fas fa-rupee-sign" area-hidden="true"></i> 79,900 </strong></h4></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <div class="col-md-12">
                        <p><b>Date :</b> 6 June 2019</p>
                        <br />
                        <p><b>Signature</b></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<style>
       .body-main {
        background: #ffffff;
        border-bottom: 15px solid #1E1F23;
        border-top: 15px solid #1E1F23;
        margin-top: 30px;
        margin-bottom: 30px;
        padding: 40px 30px !important;
        position: relative ;
        box-shadow: 0 1px 21px #808080;
        font-size:10px;
        
        
        
    }

    .main thead {
		background: #1E1F23;
        color: #fff;
		}
    .img{
        height: 100px;}
    h1{
       text-align: center;
    }

    
</style>

<!-- https://bbbootstrap.com/snippets/business-invoice-snippet-65049549 -->