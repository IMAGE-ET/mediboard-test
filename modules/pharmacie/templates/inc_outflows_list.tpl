
<form name="newOutflow" method="post" action="?m={{$m}}&amp;tab={{$tab}}" onsubmit="return checkOutflow(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="date_dispensation" value="now" />
  <input type="hidden" name="manual" value="1" />
  <input type="hidden" name="cancelled" value="0" />
  
  <table class="table tbl">
    <tr>
      <th style="width: 16px;"></th>
      <th style="width: 0.1%;">{{mb_title class=CProductStockGroup field=product_id}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=quantity}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=service_id}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=date_delivery}}</th>
      <th>{{mb_title class=CProductDelivery field=comments}}</th>
      <th style="width: 0.1%;"></th>
      <th colspan="2" style="width: 6em; white-space: normal;">{{tr}}CProductStockService{{/tr}}</th>
    </tr>
    
    <tbody class="hoverable">
      <tr>
        <td></td>
        <td>
          {{mb_field class=CProductStockGroup field=product_id form="newOutflow" autocomplete="true,1,100,false,true" style="width: 32em;"}}
        </td>
        <td style="text-align: center;">{{mb_field object=$delivrance field=quantity increment=true form="newOutflow" size=2}}</td>
        <td style="text-align: center;">
          <select name="service_id" style="width: 8em;">
            <option value=""> &ndash; {{tr}}CService{{/tr}}</option>
            {{foreach from=$list_services item=_service}}
              <option value="{{$_service->_id}}">{{$_service}}</option>
            {{/foreach}}
          </select>
        </td>
        <td style="text-align: center;">{{mb_field object=$delivrance field=date_delivery form="newOutflow" register=1}}</td>
        <td>
          <script type="text/javascript">
            showComments = function(element, force){
              var selected = force || (element.options[element.selectedIndex].value == 'other');
              var container = $(element.form.comments).up('div.comm');
              container.setVisible(selected);
              if (force) {
                container.down("textarea").tryFocus();
              }
            }
          </script>
          {{mb_field object=$delivrance field=type emptyLabel="Type" onchange="showComments(this)" onkeyup="showComments(this)"}}
          <button class="down notext" type="button" onclick="showComments(this,true)" tabIndex="200">Ajouter un commentaire</button>
          <div class="comm" style="display: none;">
            {{mb_field object=$delivrance field=comments rows=2}}
          </div>
        </td>
        <td><button class="tick notext" style="margin: -1px;" type="submit">Délivrer</button></td>
        <td colspan="2"></td>
      </tr>
    </tbody>
    
    <tr>
      <td colspan="9">
        {{mb_include module=system template=inc_pagination total=$total_outflows change_page=changePage current=$start}}
      </td>
    </tr>
    
    {{foreach from=$list_outflows item=_delivery}}
      <tbody class="hoverable">
        <tr>
          <td {{if $_delivery->_ref_stock->_ref_product->_in_order}}class="ok"{{/if}}>
            {{mb_include module=dPstock template=inc_product_in_order product=$_delivery->_ref_stock->_ref_product}}
          </td>
          <td>
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$_delivery->_ref_stock->_guid}}')">
              {{mb_value object=$_delivery->_ref_stock field=product_id}}
            </strong>
          </td>
          <td style="text-align: center;">{{mb_value object=$_delivery field=quantity}}</td>
          <td style="text-align: center;">
            {{if $_delivery->service_id}}
              {{mb_value object=$_delivery field=service_id}}
            {{/if}}
          </td>
          <td style="text-align: center;">{{mb_value object=$_delivery field=date_delivery}}</td>
          <td>
            {{if $_delivery->type}}
              <strong>{{mb_value object=$_delivery field=type}}</strong> 
              {{if $_delivery->comments}}
                &ndash;
              {{/if}}
            {{/if}}
            {{$_delivery->comments}}
          </td>
          <td>
            <button type="button" style="margin: -1px;" class="cancel notext" onclick="removeOutflow('{{$_delivery->_id}}', '{{$_delivery->_ref_stock}}')">{{tr}}Supprimer{{/tr}}</button>
          </td>
          <td style="width: 0.1%;">
            {{$_delivery->_ref_stock_service->quantity}}
          </td>
          <td>
            {{include file="../../dPstock/templates/inc_bargraph.tpl" stock=$_delivery->_ref_stock_service}}
          </td>
        </tr>
      </tbody>
    {{foreachelse}}
      <tr>
        <td colspan="9">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>



