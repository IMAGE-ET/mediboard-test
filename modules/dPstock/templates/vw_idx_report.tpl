{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
      <tr>
        <th style="width: 40px;">Actuel</th>
        <th style="width: 40px;">Futur</th>
        <th>{{tr}}CProduct-name{{/tr}}</th>
        <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
        <th>Commandes en cours</th>
        <th>Quantité en commande</th>
      </tr>
      {{foreach from=$list_stocks item=curr_stock}}
        <tr>
          {{assign var=current value=$curr_stock->_zone}}
          {{assign var=future value=$curr_stock->_zone_future}}
          <td style="background: {{$colors.$current}}"></td>
          <td style="background: {{$colors.$future}}"></td>
          <td><a href="?m={{$m}}&amp;tab=vw_idx_stock_group&amp;stock_id={{$curr_stock->_id}}" title="{{tr}}CProductStockGroup.modify{{/tr}}">{{$curr_stock->_view}}</a></td>
          <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
          <td>
            {{foreach from=$curr_stock->_orders item=curr_order}}
		        <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_order->_guid}}')">
              {{mb_value object=$curr_order field=date_ordered}}
            </span><br />
            {{/foreach}}
          </td>
          <td>{{mb_value object=$curr_stock field=_ordered_count}}</td>
        </tr>
      {{/foreach}}
      </table>
    </td>
  </tr>
</table>