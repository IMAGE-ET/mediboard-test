{{* $Id: inc_restockages_service_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{*tr}}CProductDelivery-service_id{{/tr*}}Pour</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th><button type="button" onclick="receiveAll('list-reception')" class="tick">Tout recevoir</button></th>
  </tr>
  {{foreach from=$deliveries item=curr_delivery}}
    <tr>
      <td>
        {{if $curr_delivery->patient_id}}
          {{$curr_delivery->_ref_patient->_view}}
        {{else}}
          {{$curr_delivery->_ref_service->_view}}
        {{/if}}
      </td>
      <td>
        <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_ref_product->_quantity}}</div>
        <div class="tooltip-trigger" 
             onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')">
          <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_service_id={{$curr_delivery->_ref_stock->_id}}">
            {{$curr_delivery->_ref_stock->_view}}
          </a>
        </div>
      </td>
      <td>{{mb_value object=$curr_delivery field=date_dispensation}}</td>
      <td>{{mb_value object=$curr_delivery->_ref_stock->_ref_product field=_unit_title}}</td>
      <td>
        {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
          {{assign var=id value=$trace->_id}}
          <form name="delivery-trace-{{$id}}-receive" action="?" method="post" onsubmit="return receiveLine(this)">
            <input type="hidden" name="m" value="dPstock" /> 
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
            <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
            {{if !$trace->date_reception}}
              {{mb_field object=$trace field=quantity increment=1 form=delivery-trace-$id-receive size=3}}
              {{mb_field object=$trace field=code}}
              <input type="hidden" name="date_reception" value="now" />
              <button type="submit" class="tick">Recevoir</button>
            {{else}}
              <b>{{mb_value object=$trace field=quantity}} éléments</b>
              [{{mb_value object=$trace field=code}}]
              <input type="hidden" name="_unreceive" value="1" />
              <button type="submit" class="cancel">Annuler</button>
            {{/if}}
          </form>
          <br />
        {{foreachelse}}
        Ce produit n'est pas encore sorti de la pharmacie
        {{/foreach}}
      </td>
    </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $$('a[href=#list-reception] small').first().update('({{$deliveries|@count}})');
</script>