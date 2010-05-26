{{* $Id: inc_deliveries_list.tpl 7561 2009-12-09 09:58:48Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 7561 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  deliveriesTabs = Control.Tabs.create("global-deliveries-tabs", true);
  var tabs = $("global-deliveries-tabs");
  tabs.up("td").setStyle({width: tabs.getWidth()+"px"});
  $$('a[href=#list-globales] small')[0].update('({{$deliveries|@count}})');
});
</script>

<table class="main layout">
  <tr>
    <td style="width: 140px; white-space: nowrap;">
      <form name="print-plan-cueillette-global" action="" method="get" onsubmit="return printPreparePlan(this)">
        <input type="hidden" name="m" value="pharmacie" />
        <input type="hidden" name="a" value="print_prepare_plan" />
        <input type="hidden" name="mode" value="global" />
        
        <button type="submit" class="print" style="font-weight: normal;">Plan cueillette</button>
        
        <ul class="control_tabs_vertical" id="global-deliveries-tabs" style="position: fixed;">
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <li>
            {{math assign="remaining" equation="x-y" x=$_deliveries|@count y=$delivered_counts.$service_id}}
            
            <input type="checkbox" style="float: right; margin: 4px;" 
                   name="service_id[]" value="{{$service_id}}"
                   title="Cocher pour inclure ce service dans le plan de cueillette" />
                   
            <a href="#global-service-{{$service_id}}"
               style="padding-right: 2em;" 
              {{if $remaining == 0}}class="empty"{{/if}}
              >
              {{$services.$service_id}} 
              
              <small style="min-width: 4em; display: inline-block; text-align: right;">({{$remaining}}/{{$_deliveries|@count}})</small>
            </a>
          </li>
        {{/foreach}}
        </ul>
      </form>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>{{tr}}CProduct{{/tr}}</th>
          <th colspan="2">{{mb_title class=CProductDelivery field=date_dispensation}}</th>
          {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
            <th>Stock pharmacie</th>
          {{/if}}
          <th>Stock service</th>
          <th><button type="button" onclick="deliverAll(deliveriesTabs.activeContainer.id)" class="tick">Tout délivrer</button></th>
          <th>{{mb_title class=CProduct field=_unit_title}}</th>
        </tr>
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <tbody id="global-service-{{$service_id}}" style="display: none;">
            {{foreach from=$_deliveries item=_delivery}}
              {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$_delivery}}
            {{foreachelse}}
              <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
            {{/foreach}}
          </tbody>
        {{foreachelse}}
        <tr>
          <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
