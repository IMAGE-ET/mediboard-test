<table class="tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>Stock précédent</th>
    <th>Articles théoriquement utilisés</th>
    <th>Nouveau stock</th>
  </tr>
  {{foreach from=$destockages item=curr_destockage}}
  {{assign var=stock value=$curr_destockage.stock}}
  <tr>
    <td>
      <div id="tooltip-content-{{$stock->_id}}" style="display: none;">
      {{foreach from=$stock->_ref_logs item=log}}
        {{mb_value object=$log field=date}} par <b>{{$log->_ref_user->_view}}</b><br />
      {{/foreach}}
      </div>
      <div class="tooltip-trigger" 
           onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$stock->_id}}'} })">
        {{$stock->_view}}
      </div>
    </td>
    <td>{{$stock->quantity+$curr_destockage.nb_produit}}</td>
    <td>{{$curr_destockage.nb_produit}}</td>
    <td>
      <form name="destockage-{{$stock->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDestockagesList})">
        <input type="hidden" name="m" value="dPstock" /> 
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_stock_service_aed" />
        <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
        {{assign var=id value=$stock->_id}}
        {{mb_field object=$stock field=quantity form="destockage-$id" increment=1}}
        <button type="submit" class="tick">Valider</button>
        {{if $stock->_ref_logs|@count>0}}<br />Destockage déjà réalisé durant la période indiquée{{/if}}
      </form>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProduct.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
