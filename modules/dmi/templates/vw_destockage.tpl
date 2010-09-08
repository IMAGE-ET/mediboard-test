<table class="main tbl">
  <tr>
    <th>{{mb_title class=CProductReference field=product_id}}</th>
    <th>{{mb_title class=CDMI field=type}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    <th style="width: 0.1%">Qté<br /> totale</th>
    <th style="width: 0.1%">Qté<br /> utilisée</th>
    <th style="width: 0.1%">Qté<br /> restante</th>
    <th>Recommande</th>
    <th>Commandes<br /> existantes</th>
  </tr>
  
  {{foreach from=$receptions item=_lot}}
    {{assign var=product value=$_lot->_ref_order_item->_ref_reference->_ref_product}}
  
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_lot->_guid}}')">
          {{mb_value object=$_lot->_ref_order_item->_ref_reference field=product_id}}
        </span>
      </td>
      <td>
        {{if $_lot->_ref_dmi->_id}}
          {{mb_value object=$_lot->_ref_dmi field=type}}
        {{/if}}
      </td>
      <td>{{mb_value object=$_lot field=code}}</td>
      <td {{if $_lot->lapsing_date|strtotime < $smarty.now}}class="error"{{/if}} style="text-align: center;">
        {{mb_value object=$_lot field=lapsing_date}}
      </td>
      <td style="text-align: center;">{{$_lot->_total_quantity}}</td>
      <td style="text-align: center;">{{$_lot->_used_quantity}}</td>
      <td style="text-align: center;">{{$_lot->_remaining_quantity}}</td>
      <td style="width: 1%;">
        {{assign var=lot_id value=$_lot->_id}}
        
        {{if $_lot->_ref_dmi->type != "purchase"}}
          <form name="product-reference-{{$_lot->_id}}" action="?m=dmi&amp;tab=vw_destockage" 
                method="post" onsubmit="return orderProduct(this)">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="_create_order" value="1" />
            <input type="hidden" name="reception_id" value="" />
            <input type="hidden" name="lot_id" value="{{$_lot->_id}}" />
            
            <table class="main form layout">
              <tr>
                <th>
                  {{mb_field object=$_lot field=quantity size=1 increment=true form="product-reference-$lot_id"}}
                </th>
                <td>
                  <select name="reference_id" style="width: 12em;">
                    {{foreach from=$product->_back.references item=_reference}}
                      <option value="{{$_reference->_id}}" 
                              {{if $_lot->_id == $_reference->_id}}selected="selected"{{/if}}>
                        {{$_reference->_ref_societe}} (x{{$_reference->quantity}})
                      </option>
                    {{/foreach}}
                  </select>
                </td>
                <td>
                  <label style="float: right;">
                    {{tr}}CProductOrderItem-renewal-court{{/tr}}
                    {{mb_field object=$_lot->_new_order_item field=renewal typeEnum=checkbox}}
                  </label>
                </td>
                <td>
                  <button class="tick notext" type="submit">Commander</button>
                </td>
              </tr>
            </table>
          </form>
          {{/if}}
      </td>
      <td>
        {{foreach from=$_lot->_back.order_items item=_item}}
          <strong>{{$_item->quantity}}</strong> - 
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_item->_ref_order->_guid}}')">
            {{$_item->_ref_order->order_number}}
          </span>
          <br />
        {{/foreach}}
      </td>
    </tr>
  {{/foreach}}
</table>