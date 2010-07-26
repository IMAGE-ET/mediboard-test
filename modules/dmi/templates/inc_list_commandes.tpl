{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CPrescriptionLineDMI field=quantity}}</th>
    <th>Patient</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=praticien_id}}</th>
    <th style="width: 1%">{{mb_title class=CPrescriptionLineDMI field=date}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=type}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=septic}}</th>
  </tr>
  <tr>
    <th colspan="2">{{mb_title class=CPrescriptionLineDMI field=product_id}}</th>
    <th>{{mb_title class=CProduct field=code}}</th>
    <th>Stock</th>
    <th style="width: 1%">Commander</th>
    <th>Déjà comm.</th>
  </tr>
  {{foreach from=$lines_by_context key=_context_guid item=_lines}}
    <tr>
      <th colspan="20" class="title">
        {{assign var=context value=$contexts.$_context_guid}}
        <button type="button" class="print notext" style="float: left" onclick="printOrdonnance('{{$contexts.$_context_guid->prescription_id}}', '{{$context->praticien_id}}')"></button>
        
        {{$contexts.$_context_guid}}
      </th>
    </tr>
    {{foreach from=$_lines item=_line_dmi}}
    <tbody class="hoverable">
      <tr>
        <td>{{mb_value object=$_line_dmi field=quantity}}</td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_prescription->_guid}}')">
            {{$_line_dmi->_ref_prescription->_ref_patient}}
          </span>
        </td>
        <td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_line_dmi->_ref_praticien}}
        </td>
        <td>{{mb_value object=$_line_dmi field=date}}</td>
        <td>{{mb_value object=$_line_dmi field=type}}</td>
        <td>{{mb_value object=$_line_dmi field=septic}}</td>
      </tr>
      <tr>
        <td colspan="2">
          <strong onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_product_order_item_reception->_guid}}')">
            {{$_line_dmi->_ref_product}}
          </strong>
        </td>
        <td>{{mb_value object=$_line_dmi->_ref_product field=code}}</td>
        <td>
          {{mb_include module=dPstock template=inc_bargraph stock=$_line_dmi->_ref_product->_ref_stock_group}}
        </td>
        <td>
          {{if $_line_dmi->type != "purchase"}}
          <form name="product-reference-{{$_line_dmi->_id}}" action="?m=dmi&amp;tab=vw_commandes" method="post" onsubmit="return orderProduct(this)">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="_create_order" value="1" />
            <input type="hidden" name="_context_guid" value="COperation-{{$_line_dmi->operation_id}}" />
            <input type="hidden" name="reception_id" value="" />
            <input type="hidden" name="lot_id" value="{{$_line_dmi->_ref_product_order_item_reception->_id}}" />
            
            <table class="main form layout">
              <tr>
                <th>
                  {{assign var=line_dmi_id value=$_line_dmi->_id}}
                  {{mb_field object=$_line_dmi field=quantity size=1 increment=true form="product-reference-$line_dmi_id"}}
                </th>
                <td>
                  <select name="reference_id" style="width: 12em;">
                    {{foreach from=$_line_dmi->_ref_product->_back.references item=_reference}}
                      <option value="{{$_reference->_id}}" 
                              {{if $_line_dmi->_ref_product_order_item_reception->_ref_order_item->reference_id == $_reference->_id}}selected="selected"{{/if}}>
                        {{$_reference->_ref_societe}} (x{{$_reference->quantity}})
                      </option>
                    {{/foreach}}
                  </select>
                </td>
                <td>
                  <button class="add notext" type="submit">Commander</button>
                </td>
              </tr>
              <tr>
                <td style="text-align: right;">Num. fact.:</td>
                <td colspan="2">
                  <label style="float: right;">
                    {{tr}}CProductOrderItem-renewal-court{{/tr}}
                    {{mb_field object=$_line_dmi->_new_order_item field=renewal typeEnum=checkbox}}
                  </label>
                  
                  {{assign var=_first_order value=$_line_dmi->_orders|@reset}}
                  {{if $_first_order && $_first_order->bill_number}}
                    {{$_first_order->bill_number}}
                  {{else}}
                    <input type="text" name="_bill_number" value="" size="12" />
                  {{/if}}
                </td>
              </tr>
            </table>
          </form>
          {{else}}
          
          {{/if}}
        </td>
        <td>
          {{foreach from=$_line_dmi->_orders item=_order}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_order->_guid}}')">
              {{$_order->order_number}}
            </span><br />
          {{/foreach}}
        </td>
      </tr>
    </tbody>
    {{foreachelse}}
      <tr>
        <td colspan="20">{{tr}}CPrescriptionLineDMI.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="20">{{tr}}CPrescriptionLineDMI.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>