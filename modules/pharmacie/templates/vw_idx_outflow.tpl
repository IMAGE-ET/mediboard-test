{{* $Id: vw_idx_delivrance.tpl 8542 2010-04-13 09:02:43Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 8542 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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

<div class="small-info">
  En cours de développement
</div>

<table class="table tbl">
  <tr>
    <th>{{mb_title class=CProductStockGroup field=product_id}}</th>
    <th>{{mb_title class=CProductDelivery field=quantity}}</th>
    <th>{{mb_title class=CProductDelivery field=date_delivery}}</th>
    <th>{{mb_title class=CProductDelivery field=comments}}</th>
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
      <td>{{mb_value object=$_delivery field=comments}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
