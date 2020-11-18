<form action="{$link->getModuleLink('faspay', 'payment?')} method="post">
<table class="table">
    <thead>
    <tr>
        <th>Product Name</th>
        <th>Product Quantity</th>
        <th>Payment Type</th>
    </tr>
    </thead>
    {foreach from=$cartProd item=prod name=prod}
        <tr>
            <td>{$prod.name}</td>
            <td>{$prod.quantity}</td>
            <td>
                <input type="hidden" id="prod_id_{$smarty.foreach.prod.index}" name="prod_id_{$smarty.foreach.prod.index}" value="{$prod.id_product}" />
                <select id="klikpay_option[]" name="klikpay_option[]">
                    <option value="00">Full Payment</option>
                    {if $status_3 == 'active' and $prod.price >= $min_3 and $status_mix == 'active'}
                        <option value="03">Cicilan Periode 3 Bulan</option>
                    {/if}
                    {if $status_6 == 'active' and $prod.price >= $min_6 and $status_mix == 'active'}
                        <option value="06">Cicilan Periode 6 Bulan</option>
                    {/if}
                    {if $status_12 == 'active'and $prod.price >= $min_12 and $status_mix == 'active'}
                        <option value="12">Cicilan Periode 12 Bulan</option>
                    {/if}
                    {if $status_24 == 'active'and $prod.price >= $min_24 and $status_mix == 'active'}
                        <option value="24">Cicilan Periode 24 Bulan</option>
                    {/if}
                </select>
            </td>
        </tr>

    {/foreach}

    <br>
    <br>
</table>
<p><input type="submit" name="pg" value="bca_klikpay" class="button" /></p>

</form>
