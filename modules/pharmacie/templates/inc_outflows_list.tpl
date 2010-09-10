
<form name="newOutflow" method="post" action="?m={{$m}}&amp;tab={{$tab}}" onsubmit="return checkOutflow(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="date_dispensation" value="now" />
  <input type="hidden" name="manual" value="1" />
  <input type="hidden" name="cancelled" value="0" />
  
  <table class="table tbl">
    <tr>
      <th rowspan="2" style="width: 16px;"></th>
      <th colspan="3" style="width: 0.1%;">{{mb_title class=CProductStockGroup field=product_id}}</th>
      <th rowspan="2">{{mb_title class=CProductDelivery field=comments}}</th>
      <th rowspan="2" style="width: 0.1%;"></th>
      <th rowspan="2" style="width: 8em;">{{tr}}CProductStockService{{/tr}}</th>
    </tr>
    
    <tr>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=quantity}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=service_id}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=date_delivery}}</th>
    </tr>
    
    <tbody class="hoverable">
      <tr>
        <td rowspan="2"></td>
        <td colspan="3">
          {{mb_field class=CProductStockGroup field=product_id form="newOutflow" autocomplete="true,1,100,false,true" style="width: 35em;"}}
        </td>
        <td rowspan="2">{{mb_field object=$delivrance field=comments rows=2}}</td>
        <td rowspan="2"><button class="tick" type="submit">Délivrer</button></td>
        <td rowspan="2"></td>
      </tr>
      
      <tr>
        <td style="text-align: center;">{{mb_field object=$delivrance field=quantity increment=true form="newOutflow" size=2}}</td>
        <td style="text-align: center;">
          <select name="service_id">
            <option value=""> &ndash; {{tr}}CService{{/tr}}</option>
            {{foreach from=$list_services item=_service}}
              <option value="{{$_service->_id}}">{{$_service}}</option>
            {{/foreach}}
          </select>
        </td>
        <td style="text-align: center;">{{mb_field object=$delivrance field=date_delivery form="newOutflow" register=1}}</td>
      </tr>
    </tbody>
    
    <tr>
      <td colspan="7">
        {{mb_include module=system template=inc_pagination total=$total_outflows change_page=changePage current=$start}}
      </td>
    </tr>
    
    {{foreach from=$list_outflows item=_delivery}}
      <tbody class="hoverable" style="border-top: 2px solid #ccc;">
        <tr>
          <td rowspan="2" {{if $_delivery->_ref_stock->_ref_product->_in_order}}class="ok"{{/if}}>
            {{mb_include module=dPstock template=inc_product_in_order product=$_delivery->_ref_stock->_ref_product}}
          </td>
          <td colspan="3">
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$_delivery->_ref_stock->_guid}}')">
              {{mb_value object=$_delivery->_ref_stock field=product_id}}
            </strong>
          </td>
          <td rowspan="2">{{mb_value object=$_delivery field=comments}}</td>
          <td rowspan="2">
            <button type="button" class="cancel notext" onclick="removeOutflow('{{$_delivery->_id}}', '{{$_delivery->_ref_stock}}')">{{tr}}Supprimer{{/tr}}</button>
          </td>
          <td rowspan="2">
            {{include file="../../dPstock/templates/inc_bargraph.tpl" stock=$_delivery->_ref_stock}}
          </td>
        </tr>
        
        <tr>
          <td style="text-align: center;">{{mb_value object=$_delivery field=quantity}}</td>
          <td style="text-align: center;">{{mb_value object=$_delivery field=date_delivery}}</td>
          <td style="text-align: center;">
            {{if $_delivery->service_id}}
              {{mb_value object=$_delivery field=service_id}}
            {{/if}}
          </td>
        </tr>
      </tbody>
    {{foreachelse}}
      <tr>
        <td colspan="6">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>



