<table class="tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>Stock précédent</th>
    <th>Stock actuel probable</th>
    <th>Ecart</th>
    <th></th>
  </tr>
  {{foreach from=$list_destockags item=curr_destockage}}
  <tr>
    <td>
      <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_view}}</div>
      <div class="tooltip-trigger" 
           onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$curr_delivery->_id}}'} })">
        {{$curr_delivery->_ref_stock->_view}}
      </div>
    </td>
    <td>{{mb_value object=$curr_delivery field=date_dispensation}}</td>
    <td>{{mb_value object=$curr_delivery field=quantity}}</td>
    <td>{{mb_value object=$curr_delivery field=code}}</td>
    <td>{{mb_value object=$curr_delivery->_ref_service field=_view}}</td>
    <td>
    <form name="delivery-{{$curr_delivery->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDeliveriesList})">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      {{if $curr_delivery->date_delivery}}
      <input type="hidden" name="_undeliver" value="1" />
      <button type="submit" class="cancel">Annuler la délivrance</button>
      {{else}}
      <input type="hidden" name="_deliver" value="1" />
      <button type="submit" class="tick">Effectuer</button>
      {{/if}}
    </form>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
