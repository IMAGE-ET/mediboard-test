<table class="tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>Stock précédent</th>
    <th>Articles théoriquement utilisés</th>
    <th>Nouveau stock</th>
    <th></th>
  </tr>
  {{foreach from=$destockages item=curr_destockage}}
  {{assign var=stock value=$curr_destockage.stock}}
  <tr>
    <td>
      <div id="tooltip-content-{{$stock->_id}}" style="display: none;">{{$stock->_view}}</div>
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
      </form>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProduct.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
