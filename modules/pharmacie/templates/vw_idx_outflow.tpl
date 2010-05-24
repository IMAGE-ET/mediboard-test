{{* $Id: vw_idx_delivrance.tpl 8542 2010-04-13 09:02:43Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 8542 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function checkOutflow(form) {
  return checkForm(form) && ($V(form.comments) || $V(form.service_id) != 0);
}
</script>

<form name="filter" action="?" method="get" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="pharmacie" />
  <input type="hidden" name="tab" value="vw_idx_outflow" />
  
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=1 onchange="this.form.submit()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=1 onchange="this.form.submit()"}}</td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<form name="newOutflow" method="post" action="?m={{$m}}&amp;tab={{$tab}}" onsubmit="return checkOutflow(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="date_dispensation" value="now" />
  
  <table class="table tbl">
    <tr>
      <th>{{mb_title class=CProductStockGroup field=product_id}}</th>
      <th>{{mb_title class=CProductDelivery field=quantity}}</th>
      <th>{{mb_title class=CProductDelivery field=date_delivery}}</th>
      <th>{{mb_title class=CProductDelivery field=service_id}}</th>
      <th>{{mb_title class=CProductDelivery field=comments}}</th>
      <th style="width: 0.1%;"></th>
    </tr>
    
    {{foreach from=$list_outflows item=_delivery}}
      <tr>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_delivery->_ref_stock->_guid}}')">
            {{mb_value object=$_delivery->_ref_stock field=product_id}}
          </span>
        </td>
        <td>{{mb_value object=$_delivery field=quantity}}</td>
        <td>{{mb_value object=$_delivery field=date_delivery}}</td>
        <td>
          {{if $_delivery->service_id}}
            {{mb_value object=$_delivery field=service_id}}
          {{/if}}
        </td>
        <td>{{mb_value object=$_delivery field=comments}}</td>
        <td></td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="10">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
      </tr>
    {{/foreach}}
    <tr>
      <td>{{mb_field class=CProductStockGroup field=product_id form="newOutflow" autocomplete="true,1,50,false,true"}}</td>
      <td>{{mb_field object=$delivrance field=quantity increment=true form="newOutflow" size=2}}</td>
      <td>{{mb_field object=$delivrance field=date_delivery form="newOutflow" register=1}}</td>
      <td>
        <select name="service_id">
          <option value=""> &ndash {{tr}}CService{{/tr}}</option>
          {{foreach from=$list_services item=_service}}
            <option value="{{$_service->_id}}">{{$_service}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>{{mb_field object=$delivrance field=comments prop="str"}}</td>
      <td><button class="tick" type="submit">{{tr}}Create{{/tr}}</button></td>
    </tr>
  </table>
</form>