{if $pgexist}
	<br><h3 style="font-weight:bold">Pembayaran via Kartu Kredit:</h3><br>
	<div class="container">
		{foreach from=$midlist item=pg}
			{if $pg.status == 1}
				<div class="col-xs-12 col-md-4" style="min-height:200px">
					<p class="payment_module">
						<a href="{$link->getModuleLink('faspaycc', 'payment')}?channel={$pg.name}" title="{$pg.name}" style="min-height:150px">
							<img src="{$this_path}logo/icon_{$pg.name}.png" style="max-height:150px;">
							{* not needed    <span style="font-weight:bold">Pay with {$pg.name}</span>  *}
						</a>
					</p>
				</div>
			{/if}
		{/foreach}
	</div>
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

.payment_faspay img {
    max-height: 25px !important;
}	
</style>