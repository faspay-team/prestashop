{if $pgexist}
	<br><h3 style="font-weight:bold">Pembayaran via Internet Banking atau ATM:</h3><br>
	<div class="container">
        {foreach from=$pglist item=pg}
            {if $pg.active}
				<div class="col-xs-12 col-md-4" style="min-height:200px">
                    {if strpos($link->getModuleLink('faspay', 'payment'),"index.php") !== false}
						<p class="payment_module">
							<a class='faspay-item' href="{$link->getModuleLink('faspay', 'payment')}&pg={$pg.cd}" title="{$pg.desc}" style="min-height:150px">
								<img src="{$this_path}icon_{$pg.id}.png" style="max-height:150px;"><br><br>
								<!-- <b>{$pg.desc}</b> -->
							</a>
						</p>
                    {else}
						<p class="payment_module">
							<a class='faspay-item' href="{$link->getModuleLink('faspay', 'payment')}?pg={$pg.cd}" title="{$pg.desc}" style="min-height:150px">
								<img src="{$this_path}icon_{$pg.id}.png" style="max-height:150px;">
                                {* not needed     <b>{$pg.desc}</b>  *}
							</a>
						</p>
                    {/if}
				</div>
            {/if}
        {/foreach}
	</div>
    {* delete text  Pilih salah satu Kanal Pembayaran yang Anda miliki
    <p style="padding-left:150px;">{l s='Pick the one of above payment channels that suite you' mod='faspay'}</p>
    *}
{else}
	<h2>{l s='Faspay not fully configured' mod='faspay'}</h2>
{/if}

<style type="text/css">
	.payment_faspay {
		border-top: 1px dotted black;
		display: block;
		padding-bottom: 5px;
		padding-top: 15px;
		width: 590px;
	}

	p.payment_module a {
		color: #334433;
		padding: 0px;
	}

	.payment_faspay img {
		max-height: 45px !important;
	}
</style>