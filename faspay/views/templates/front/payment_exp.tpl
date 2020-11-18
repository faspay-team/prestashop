{extends "$layout"}
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{block name="content"}
  <section>

    <center>
      <img src="{$tpl_dir}/modules/faspay/faspay.jpg" width="300px" height="100px">
      <br>
      <br>
      <br>
      <img src="{$tpl_dir}/modules/faspay/sorry.gif" width="150px" height="150px">
      <h3>YOUR PAYMENT TIME IS EXPIRED</h3>
        <br>
      <p>{l s='Hey,'} {$custname}{$custlname} {'you just passed the payment time limit'}</p>
      <br>
          <a href="{$tpl_dir}">
          <button class="button"><span>Re-pay</span></button>
          </a>

      <br>
      <br>
    </center>
  </section>


  <style>
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
{/block}
