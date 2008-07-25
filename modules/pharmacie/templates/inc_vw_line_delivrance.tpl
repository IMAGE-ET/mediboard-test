<tr>
  <td>
    {{if $curr_delivery->patient_id}}
      {{$curr_delivery->_ref_patient->_view}}
    {{else}}
      {{$curr_delivery->_ref_service->_view}}
    {{/if}}
  </td>
  <td>
    <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_view}}</div>
    <div class="tooltip-trigger" 
         onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$curr_delivery->_id}}'} })">
      {{$curr_delivery->_ref_stock->_view}}
    </div>
  </td>
  <td>{{mb_value object=$curr_delivery field=date_dispensation}}</td>
  <td>{{mb_value object=$curr_delivery->_ref_stock field=quantity}}</td>
  <td>{{mb_value object=$curr_delivery field=quantity}}</td>
  <td>
    {{assign var=id value=$curr_delivery->_id}}
    {{assign var=stock value=$stocks_service.$id}}
    {{$stock->quantity}}
  </td>
  <td>
  {{assign var=id value=$curr_delivery->_id}}
  {{if !$curr_delivery->date_delivery}}
    {{mb_field object=$curr_delivery field=code id="code-$id"}}
  {{else}}
    {{mb_value object=$curr_delivery field=code}}
  {{/if}}
  </td>
  <td>
  <form name="delivery-{{$curr_delivery->_id}}" action="?" method="post" onsubmit="$V(this.code, $V($('code-{{$id}}'))); return onSubmitFormAjax(this, {onComplete: refreshDeliveriesList})">
    <input type="hidden" name="m" value="dPstock" /> 
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_delivery_aed" />
    <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
    {{if $curr_delivery->date_delivery}}
    <input type="hidden" name="_undeliver" value="1" />
    <button type="submit" class="cancel">Annuler</button>
    {{else}}
    <input type="hidden" name="code" value="" />
    <input type="hidden" name="_deliver" value="1" />
    <button type="submit" class="tick">Délivrer</button>
    {{/if}}
  </form>
  </td>
</tr>