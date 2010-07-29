{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $type=="waiting"}}
<!-- Orders not validated yet -->
<table class="tbl">
  <tr>
    <th style="width: 0.1%;">{{mb_title class=CProductOrder field=order_number}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-items_count{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
	{{foreach from=$orders item=curr_order}}
	  <tr>
	    <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_guid}}')">
          {{$curr_order->order_number}}
        </span>
      </td>
	    <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_societe->_guid}}')">
          {{$curr_order->_ref_societe->_view}}
        </span>
      </td>
	    <td>{{$curr_order->_ref_order_items|@count}}</td>
	    <td class="currency" style="text-align: right;">{{mb_value object=$curr_order field=_total}}</td>
	    <td>
        <button type="button" class="edit notext" onclick="popupOrder({{$curr_order->_id}});">{{tr}}Modify{{/tr}}</button>
        
        {{if $curr_order->_ref_order_items|@count > 0}}
        <form name="order-lock-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="locked" value="1" />
          <button type="button" class="tick" onclick="submitOrder(this.form, {refreshLists: true, confirm: true});">{{tr}}CProductOrder-_validate{{/tr}}</button>
        </form>
        {{/if}}
        
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="1" />
          <button type="button" class="cancel notext" onclick="submitOrder(this.form, {refreshLists: true, confirm: true})">{{tr}}Cancel{{/tr}}</button>
        </form>
        
        {{if $can->admin}}
        <form name="order-purge-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="_purge" value="0" />
          <button type="button" class="cancel" onclick="confirmPurge(this, '{{$curr_order->_view|smarty:nodefaults|JSAttribute}}', '{{$type}}')">
            {{tr}}Purge{{/tr}}
          </button>
        </form>
        {{/if}}
	   </td>
	  </tr>
	{{foreachelse}}
	  <tr>
	    <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
	  </tr>
	{{/foreach}}
  </tbody>
</table>


{{elseif $type=="locked"}}
<!-- Orders locked -->
<table class="tbl">
  <tr>
    <th style="width: 0.1%;">{{mb_title class=CProductOrder field=order_number}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-object_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-items_count{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_guid}}')">
          {{$curr_order->order_number}}
        </span>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_societe->_guid}}')">
          {{$curr_order->_ref_societe->_view}}
        </span>
      </td>
      <td class="text">
        {{if $curr_order->_ref_object}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_object->_guid}}')">
          {{$curr_order->_ref_object->_view}}
        </span>
        {{/if}}
      </td>
      <td>{{$curr_order->_ref_order_items|@count}}</td>
      <td class="currency" style="text-align: right;">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <button type="button" class="print" onclick="popupOrderForm({{$curr_order->_id}})">Bon de com.</button>
        <form name="order-order-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="_order" value="1" />
          <button type="button" class="tick" onclick="submitOrder(this.form, {refreshLists: true, confirm: true})">{{tr}}CProductOrder-_order{{/tr}}</button>
        </form>
        <form name="order-reset-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="_reset" value="1" />
          <button type="button" class="left notext" onclick="submitOrder(this.form, {refreshLists: true, confirm: true})">Remettre dans "A valider"</button>
        </form>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="1" />
          <button type="button" class="cancel notext" onclick="submitOrder(this.form, {refreshLists: true, confirm: true})">{{tr}}Cancel{{/tr}}</button>
        </form>
        
        {{if $can->admin}}
        <form name="order-purge-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="_purge" value="0" />
          <button type="button" class="cancel" onclick="confirmPurge(this, '{{$curr_order->_view|smarty:nodefaults|JSAttribute}}', '{{$type}}')">
            {{tr}}Purge{{/tr}}
          </button>
        </form>
        {{/if}}
     </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>



{{elseif $type=="pending"}}
<!-- Orders not received yet -->
<table class="tbl">
  <tr>
    <th style="width: 0.1%;">{{mb_title class=CProductOrder field=order_number}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-object_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-items_count{{/tr}} /<br /> {{tr}}CProductOrder-_count_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_guid}}')">
          {{$curr_order->order_number}}
        </span>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_societe->_guid}}')">
          {{$curr_order->_ref_societe->_view}}
        </span>
      </td>
      <td class="text">
        {{if $curr_order->_ref_object}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_object->_guid}}')">
          {{$curr_order->_ref_object->_view}}
        </span>
        {{/if}}
      </td>
      <td>{{$curr_order->_count_renewed}}/{{$curr_order->_count_received}}</td>
      <td>{{mb_value object=$curr_order field=date_ordered}}</td>
      <td class="currency" style="text-align: right;">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <button type="button" class="tick" onclick="popupReception({{$curr_order->_id}});">{{tr}}Recevoir{{/tr}}</button>
        
        <form name="order-reset-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="date_ordered" value="" />
          <button type="button" class="left notext" onclick="submitOrder(this.form, {refreshLists: true, confirm: true})">Remettre à "A passer"</button>
        </form>
        
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="1" />
          <button type="button" class="cancel notext" onclick="submitOrder(this.form, {refreshLists: true, confirm: true})">{{tr}}Cancel{{/tr}}</button>
        </form>
        
        {{if $can->admin}}
        <form name="order-purge-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="_purge" value="0" />
          <button type="button" class="cancel" onclick="confirmPurge(this, '{{$curr_order->_view|smarty:nodefaults|JSAttribute}}', '{{$type}}')">
            {{tr}}Purge{{/tr}}
          </button>
        </form>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>



{{elseif $type=="received"}}

<div style="text-align: right;">
  <label>
    <input type="checkbox" onclick="$$('tr.bill').invoke('setVisible', this.checked)" /> Afficher les facturées
  </label>
</div>

<!-- Received orders -->
<table class="tbl">
  <tr>
    <th style="width: 0.1%;">{{mb_title class=CProductOrder field=order_number}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-object_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-items_count{{/tr}}</th>
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_date_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;">{{tr}}CProductOrder-bill_number{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr {{if $curr_order->bill_number}}style="display: none;" class="bill"{{/if}}>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_guid}}')">
          {{$curr_order->order_number}}
        </span>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_societe->_guid}}')">
          {{$curr_order->_ref_societe->_view}}
        </span>
      </td>
      <td class="text">
        {{if $curr_order->_ref_object}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_object->_guid}}')">
          {{$curr_order->_ref_object->_view}}
        </span>
        {{/if}}
      </td>
      <td>{{$curr_order->_count_renewed}}</td>
      <td>{{mb_value object=$curr_order field=date_ordered}}</td>
      <td>{{mb_value object=$curr_order field=_date_received}}</td>
      <td class="currency" style="text-align: right;">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <form name="order-billnumber-{{$curr_order->_id}}" action="?" method="post" onsubmit="return submitOrder(this, {refreshLists: true})">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          {{mb_field object=$curr_order field=bill_number size=12}}
          <button type="submit" class="save notext">{{tr}}CProductOrder-bill_number{{/tr}}</button>
        </form>
      </td>
      <td>
        <!--
      	<button type="button" class="barcode" onclick="printBarcodeGrid('{{$curr_order->_id}}')">Imprimer les codes barres</button>
        -->
        
        <form name="order-redo-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="_redo" value="1" />
          <button type="button" class="change notext" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}CProductOrder-_redo{{/tr}}</button>
        </form>
        
        {{if $can->admin}}
        <form name="order-purge-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="_purge" value="0" />
          <button type="button" class="cancel" onclick="confirmPurge(this, '{{$curr_order->_view|smarty:nodefaults|JSAttribute}}', '{{$type}}')">
            {{tr}}Purge{{/tr}}
          </button>
        </form>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>



{{else}}
<!-- Cancelled orders -->
<table class="tbl">
  <tr>
    <th style="width: 0.1%;">{{mb_title class=CProductOrder field=order_number}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-object_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-items_count{{/tr}}</th>
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_date_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>
         <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_guid}}')">
         {{$curr_order->order_number}}
        </span>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_societe->_guid}}')">
          {{$curr_order->_ref_societe->_view}}
        </span>
      </td>
      <td class="text">
        {{if $curr_order->_ref_object}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_ref_object->_guid}}')">
          {{$curr_order->_ref_object->_view}}
        </span>
        {{/if}}
      </td>
      <td>{{$curr_order->_ref_order_items|@count}}</td>
      <td>{{mb_value object=$curr_order field=date_ordered}}</td>
      <td>{{mb_value object=$curr_order field=_date_received}}</td>
      <td class="currency" style="text-align: right;">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="0" />
          <button type="button" class="tick" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}Restore{{/tr}}</button>
        </form>
        <form name="order-delete-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="deleted" value="1" />
          <button type="button" class="trash notext" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}Delete{{/tr}}</button>
        </form>
        
        {{if $can->admin}}
        <form name="order-purge-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="_purge" value="0" />
          <button type="button" class="cancel" onclick="confirmPurge(this, '{{$curr_order->_view|smarty:nodefaults|JSAttribute}}', '{{$type}}')">
            {{tr}}Purge{{/tr}}
          </button>
        </form>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>
{{/if}}

<!-- The orders count -->
<script type="text/javascript">
  tab = $$('a[href="#list-orders-{{$type}}"]')[0];
  counter = tab.down("small");
  count = {{$orders|@count}};
  
  if (count > 0)
    tab.removeClassName("empty");
  else
    tab.addClassName("empty");
    
  counter.update("("+count+")");
</script>