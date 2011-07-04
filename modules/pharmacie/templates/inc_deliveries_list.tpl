{{* $Id: inc_deliveries_{{$mode}}_list.tpl 11928 2011-04-20 12:29:12Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 11928 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("{{$mode}}-deliveries-tabs", true);
  $$('a[href=#list-{{$mode}}] small')[0].update('({{$deliveries|@count}})');
  
	{{if $deliveries_by_service|@count < 25}}
	  var tabs = $("{{$mode}}-deliveries-tabs");
    tabs.style.position = "static";
    tabs.style.top = 0;
		
		var posY = tabs.positionedOffset().top;
		
	  Event.observe(window, "scroll", function(){
		  var scroll = document.viewport.getScrollOffsets();
      posY = posY || (tabs.style.position = "static") && tabs.positionedOffset().top;
			tabs.style.position = ((scroll.top > posY) ? "fixed" : "static");
	  });
	{{/if}}
});
</script>

<table class="main layout">
  <tr>
    <td style="white-space: nowrap; width: 128px;" class="narrow">
      <form name="print-plan-cueillette-{{$mode}}" action="" method="get" onsubmit="return printPreparePlan(this)">
        <input type="hidden" name="m" value="pharmacie" />
        <input type="hidden" name="a" value="print_prepare_plan" />
        <input type="hidden" name="mode" value="{{$mode}}" />
        
        <button type="submit" class="print" style="font-weight: normal;">Plan cueillette</button>
        <br /><!-- BR required (rendering bug in Chrome) -->
      
        <ul class="control_tabs_vertical small" id="{{$mode}}-deliveries-tabs" style="font-size: 0.9em;">
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <li>
            {{math assign="remaining" equation="x-y" x=$_deliveries|@count y=$delivered_counts.$service_id}}
            
            <input type="checkbox" style="float: right; margin: 2px;" 
                   name="service_id[]" value="{{$service_id}}"
                   title="Cocher pour inclure ce service dans le plan de cueillette" />
                   
            <a href="#{{$mode}}-service-{{$service_id}}"
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
          <th style="width: 16px;"></th>
					{{if $mode == "nominatif"}}
            <th>{{tr}}CProductDelivery-patient_id{{/tr}}</th>
					{{/if}}
          <th>
            {{mb_colonne class=CProductStockGroup field=product_id order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          <th colspan="2">
            {{mb_colonne class=CProductDelivery field=date_dispensation order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          {{if !$conf.dPstock.CProductStockGroup.infinite_quantity}}
            <th>Stock pharm.</th>
          {{/if}}
          <th>
            {{mb_colonne class=CProductStockGroup field=location_id order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          {{if !$conf.dPstock.CProductStockService.infinite_quantity}}
            <th>Stock serv.</th>
          {{/if}}
          <th class="narrow"><button type="button" onclick="deliverAll('list-{{$mode}}')" class="tick">Tout délivrer</button></th>
          <th>{{mb_title class=CProduct field=_unit_title}}</th>
          <th class="narrow"></th>
        </tr>
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <tbody id="{{$mode}}-service-{{$service_id}}" style="display: none;">
            {{foreach from=$_deliveries item=_delivery}}
              <tr id="{{$_delivery->_guid}}">
                {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$_delivery}}
              </tr>
            {{foreachelse}}
              <td colspan="{{if $mode == "nominatif"}}11{{else}}10{{/if}}" class="empty">{{tr}}CProductDelivery.none{{/tr}}</td>
            {{/foreach}}
          </tbody>
        {{foreachelse}}
        <tr>
          <td colspan="{{if $mode == "nominatif"}}11{{else}}10{{/if}}" class="empty">{{tr}}CProductDelivery.{{$mode}}.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
