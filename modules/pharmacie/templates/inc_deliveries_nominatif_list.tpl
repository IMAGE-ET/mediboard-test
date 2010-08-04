{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("nominatif-deliveries-tabs", true);
  $$('a[href=#list-nominatives] small')[0].update('({{$deliveries|@count}})'); 
  
  var tabs = $("nominatif-deliveries-tabs");
  Event.observe(window, "scroll", function(){
    tabs.setStyle({marginTop: document.viewport.getScrollOffsets().top+"px"});
  });
});
</script>

<table class="main layout">
  <tr>
    <td style="white-space: nowrap; width: 0.1%; ">
      <form name="print-plan-cueillette-nominatif" action="" method="get" onsubmit="return printPreparePlan(this)">
        <input type="hidden" name="m" value="pharmacie" />
        <input type="hidden" name="a" value="print_prepare_plan" />
        <input type="hidden" name="mode" value="nominatif" />
        
        <button type="submit" class="print" style="font-weight: normal;">Plan cueillette</button>
        <br /><!-- BR required (rendering bug in Chrome) -->
      
        <ul class="control_tabs_vertical" id="nominatif-deliveries-tabs" style="font-size: 0.9em;">
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <li>
            {{math assign="remaining" equation="x-y" x=$_deliveries|@count y=$delivered_counts.$service_id}}
            
            <input type="checkbox" style="float: right; margin: 3px;" 
                   name="service_id[]" value="{{$service_id}}"
                   title="Cocher pour inclure ce service dans le plan de cueillette" />
                   
            <a href="#nominatif-service-{{$service_id}}"
               style="padding-right: 2em;" 
              {{if $remaining == 0}}class="empty"{{/if}}
              >
              {{$services.$service_id}} 
              <small style="min-width: 3em; display: inline-block; text-align: right;">({{$remaining}}{{if $display_delivered}}/{{$_deliveries|@count}}{{/if}})</small>
            </a>
          </li>
        {{/foreach}}
        </ul>
      </form>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>{{tr}}CProductDelivery-patient_id{{/tr}}</th>
          <th>
            {{mb_colonne class=CProductStockGroup field=product_id order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          <th colspan="2">
            {{mb_colonne class=CProductDelivery field=date_dispensation order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
            <th>Stock pharm.</th>
          {{/if}}
          <th>
            {{mb_colonne class=CProductStockGroup field=location_id order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          {{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
            <th>Stock serv.</th>
          {{/if}}
          <th style="width: 0.1%;"><button type="button" onclick="deliverAll('list-nominatives')" class="tick">Tout délivrer</button></th>
          <th>{{mb_title class=CProduct field=_unit_title}}</th>
          <th style="width: 0.1%;"></th>
        </tr>
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <tbody id="nominatif-service-{{$service_id}}" style="display: none;">
            {{foreach from=$_deliveries item=_delivery}}
              <tr id="{{$_delivery->_guid}}">
                {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$_delivery}}
              </tr>
            {{foreachelse}}
              <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
            {{/foreach}}
          </tbody>
        {{foreachelse}}
        <tr>
          <td colspan="10">{{tr}}CProductDelivery.nominatif.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
