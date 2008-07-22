<table class="tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th style="width: 1%">{{tr}}CProductDelivery-code{{/tr}}</th>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th></th>
  </tr>
  {{foreach from=$list_deliveries item=curr_delivery}}
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
    <td>
    {{assign var=id value=$curr_delivery->_id}}
    {{if !$curr_delivery->date_delivery}}
      {{mb_field object=$curr_delivery field=code id="code-$id"}}
    {{else}}
      {{mb_value object=$curr_delivery field=code}}
    {{/if}}
    </td>
    <td>{{mb_value object=$curr_delivery->_ref_service field=_view}}</td>
    <td>
    <form name="delivery-{{$curr_delivery->_id}}" action="?" method="post" onsubmit="$V(this.code, $V($('code-{{$id}}'))); return onSubmitFormAjax(this, {onComplete: refreshDeliveriesList})">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      {{if $curr_delivery->date_delivery}}
      <input type="hidden" name="_undeliver" value="1" />
      <button type="submit" class="cancel">Annuler la délivrance</button>
      {{else}}
      <input type="hidden" name="code" value="" />
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
