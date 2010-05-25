{{* $Id: inc_deliveries_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page=refreshOrders current=$start step=30}}

<table class="tbl">
  <!-- Affichage des delivrances globales -->
  <tr>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th colspan="2">{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-comments{{/tr}}</th>
    {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
      <th>Stock pharmacie</th>
    {{/if}}
    
    {{* 
    {{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
      <th>Stock service</th>
    {{/if}}
    *}}
    
    <th style="width: 0.1%;">
      <button type="button" onclick="dispenseAll('list-orders', refreshOrders)" class="tick">
        Disp. les {{$deliveries|@count}} visibles
      </button>
    </th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
  </tr>
  {{foreach from=$deliveries item=curr_delivery}}
    {{include file="inc_vw_line_order.tpl" curr_delivery=$curr_delivery nodebug=true}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.order.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
<script type="text/javascript">
  $$('a[href=#list-orders] small')[0].update('({{$total}})');
</script>