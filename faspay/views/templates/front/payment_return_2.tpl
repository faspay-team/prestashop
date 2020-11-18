{extends "$layout"}
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{block name="content"}
  <link rel="stylesheet" type="text/css" href="{$tpl_dir}/modules/faspay/css/stylepaymentpage.css?v=125">
  <div class="box">
        <div class="header-tpl">
            <p>Transaction Order Detail</p>
        </div>

        <div class="payment">Payment Via:</div>
        <img src="{$tpl_dir}/modules/faspay/{{$bank_name}}.png" class="bank">

            <li class="list a">
            <div class="title">VA Number / Kode Bayar</div>
                <div class="content a">
                    <div class="VA">{$va}</div>
                </div>
            </li>


            <li class="list b">
                <div class="title">Total Payment</div>
                <div class="content">
                    <div class="Price">
                    <p>RP. {$finalprice}</p>
                    </div>
                </div>  
            </li>


    <div class="row">
        <div class="col-md-12">
            <table class="table mgr-t-20">
                <tr class="border">
                    <td class="custom_color_left" colspan="2">Expired at: {$exp}</td>
                 </tr>
             </table>
        </div>
    </div>
        <br>
        {include file=$guide inline}
            <div class="footer">
                <p class="pwb">Powered by</p>
                <img src="{$tpl_dir}/modules/faspay/faspay.jpg" class="default">
                <p class="copyright">All Rights Reserved © 2019 Faspay</p>
            </div>
    </div>
</body>
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
    #myBtn{
        font-family: "Open Sans", Helvetica, Arial, sans-serif;
        border-radius: 4px;
        background-color: #FC8410;
        border: none;
        color: #FFFFFF;
        text-align: center;
        font-size: 15px;
        padding: 10px;
        width: 200px;
        transition: all 0.5s;
        cursor: pointer;
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