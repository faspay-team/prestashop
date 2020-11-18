{extends "$layout"}
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
{block name="content"}
<section>

    <center>
        <br>
        <br>
        <h3>
            Your Payment Code will be expired after: {$exp}
        </h3>
        <br>
        <h3>
            please conduct the payment before the following expired time
        </h3>
        <br>
        <br>
        <br>
        <ul>
            <div class="edge">
                <center>Payment will be conduct via : <img src="{$tpl_dir}/modules/faspay/{{$bank_name}}.png" width="230px" height="60px"></center>
                <br>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Product Quantity</th>
                        <th>Product ID</th>
                        <th>
                            Cart ID : {$prod_id}
                            <br>
                        </th>
                    </tr>
                    </thead>
                    {foreach from=$cartProd item=prod name=prod}
                        <tr>
                            <td>{$prod.name}</td>
                            <td>{$prod.price}</td>
                            <td>{$prod.quantity}</td>
                            <td>{$prod.id_product}</td>
                        </tr>
                    {/foreach}
                </table>
                <br>
                <br>
                <table class="table">
                    <h3><b>Payment Details</b></h3>
                        <tr>
                            <td>
                                <center>Virtual Account Number : {$va}</center>
                                <br>
                                <center>Bill Number : {$bill_no}</center>
                                <br>
                                <center>Merchant Id : {$merchant}</center>
                            </td>
                            <td>
                                <br>
                                <br>
                                <center>Merchant Name : {$merchant_name}</center>
                                <br>
                            </td>
                            <td>
                                <br>
                                <br>
                                <center>The total amount of your order is :</center>
                                <center>IDR <b>{$total}</b>  + Tax IDR <b>{$tax_display}</b></center>
                                <center>Final Price : IDR <b>{$finalprice}</b></center>
                                <br>
                            </td>
                        </tr>
                </table>
                <br>
                {include file=$guide inline}
                <a href="{$tpl_dir}">
                    <button class="button"><span>Continue Shopping</span></button>
                </a>
            </div>
        </ul>
    </center>
    <br>
</section>

<style>
    .accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
    }

    .active, .accordion:hover {
        background-color: #ccc;
    }

    .panel {
        padding: 0 18px;
        display: none;
        background-color: white;
        overflow: hidden;
    }
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }
    .modal-header {
        padding: 25px;
        background-color: #f15b29;
        color: white;
    }
    .edge {
        width: 1000px;
        border: 8px solid #F0E68C;
        padding: 10px;
        background-color: white;
    }
    .button {
        font-family: "Open Sans", Helvetica, Arial, sans-serif;
        border-radius: 4px;
        background-color: #00aff0;
        border: none;
        color: #FFFFFF;
        text-align: center;
        font-size: 15px;
        padding: 10px;
        width: 200px;
        transition: all 0.5s;
        cursor: pointer;
        margin: 5px;
    }

    .button span {
        cursor: pointer;
        display: inline-block;
        position: relative;
        transition: 0.5s;
    }

    .button span:after {
        content: '\00bb';
        position: absolute;
        opacity: 0;
        top: 0;
        right: -20px;
        transition: 0.5s;
    }

    .button:hover span {
        padding-right: 25px;
    }

    .button:hover span:after {
        opacity: 1;
        right: 0;
    }
</style>
<script>
    var modal = document.getElementById('myModal');
    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");
    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }

</script>
{/block}
