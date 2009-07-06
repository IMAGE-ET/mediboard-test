{{* $Id: inc_deliveries_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <!-- Affichage des delivrances globales -->
  <tr>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
      <th>Stock pharmacie</th>
    {{/if}}
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>Stock service</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th><button type="button" onclick="dispenseAll('list-orders', refreshOrders)" class="tick">Tout dispenser</button></th>
  </tr>
  {{foreach from=$deliveries item=curr_delivery}}
    {{include file="inc_vw_line_order.tpl" curr_delivery=$curr_delivery}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.order.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
<script type="text/javascript">
  $$('a[href=#list-orders] small').first().update('({{$deliveries|@count}})');
</script>