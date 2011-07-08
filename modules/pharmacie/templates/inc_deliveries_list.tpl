{{* $Id: inc_deliveries_{{$mode}}_list.tpl 11928 2011-04-20 12:29:12Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 11928 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=max_services value=25}}
{{assign var=count_date value=0}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("{{$mode}}-deliveries-tabs", true);
  $$('a[href=#list-{{$mode}}] small')[0].update('({{$deliveries_count}})');
  
	{{if $deliveries_by_service|@count < $max_services}}
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
    <td style="white-space: nowrap; {{if $deliveries_by_service|@count < $max_services}}width: 128px;{{/if}}" class="narrow">
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
              {{if $remaining == 0}}class="empty"{{/if}}>
              {{$services.$service_id}}
              <small style="min-width: 3em; display: inline-block; text-align: right;">
							  ({{$deliveries_count_by_service.$service_id}})
							  {{* ({{$remaining}}{{if $display_delivered}}/{{$deliveries_count_by_service.$service_id}}{{/if}}) *}}
							</small>
            </a>
          </li>
        {{/foreach}}
        </ul>
      </form>
    </td>
    <td>
			{{assign var=rowspan value=1}}
			
      {{if $mode == "nominatif"}}
			  {{assign var=rowspan value=2}}
			{{/if}}
			
      {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
			  {{assign var=_service value=$services.$service_id}}
        {{assign var=cols value=7}}
				
	      <table class="tbl" id="{{$mode}}-service-{{$service_id}}" style="display: none;">
	        <tr>
	          <th style="width: 16px;" rowspan="{{$rowspan}}"></th>
						
	          <th rowspan="{{$rowspan}}">
	            {{mb_colonne class=CProductStockGroup field=product_id order_col=$order_col order_way=$order_way function=changeSort}}
	          </th>
						
	          <th colspan="2" rowspan="{{$rowspan}}">
	            {{mb_colonne class=CProductDelivery field=date_dispensation order_col=$order_col order_way=$order_way function=changeSort}}
	          </th>
						
	          {{if !$conf.dPstock.CProductStockGroup.infinite_quantity}}
						  {{assign var=cols value=$cols+1}}
							<th rowspan="{{$rowspan}}">
								Stock <br />pharm.
							</th>
	          {{/if}}
						
						{{if !$single_location}}
		          {{assign var=cols value=$cols+1}}
	            <th rowspan="{{$rowspan}}">
		            {{mb_colonne class=CProductStockGroup field=location_id order_col=$order_col order_way=$order_way function=changeSort}}
		          </th>
						{{/if}}
						
	          {{if !$conf.dPstock.CProductStockService.infinite_quantity}}
	            {{assign var=cols value=$cols+1}}
              <th rowspan="{{$rowspan}}">
              	Stock <br /> serv.
							</th>
	          {{/if}}
						
	          <th class="narrow" rowspan="{{$rowspan}}">
	          	<button type="button" onclick="deliverAll('{{$mode}}-service-{{$service_id}}')" class="tick">Tout déliv.</button>
						</th>
						
	          <th rowspan="{{$rowspan}}">{{mb_title class=CProduct field=_unit_title}}</th>
	          <th rowspan="{{$rowspan}}" class="narrow"></th>
						
						{{if $mode == "nominatif"}}
						  {{assign var=count_date value=$pilulier_init|@count}}
						  <th colspan="{{$count_date*4}}">Pilulier</th>
						{{/if}}
	        </tr>
					
					{{if $mode == "nominatif"}}
						<tr>
						  {{foreach from=$pilulier_init key=_date item=_pilulier_by_date}}
							  {{foreach from=$_pilulier_by_date key=_moment item=_quant}}
	                <th style="font-size: 0.9em; padding:1px;">{{$nom_moments.$_moment}}</th>
								{{/foreach}}
	            {{/foreach}}
						</tr>	
				  {{/if}}
					
          {{foreach from=$_deliveries item=_delivery_by_patient name="deliveries_patient"}}
            {{foreach from=$_delivery_by_patient item=_delivery_by_ucd name="deliveries_ucd"}}
							{{foreach from=$_delivery_by_ucd item=_delivery name="deliveries"}}
		            {{if  $smarty.foreach.deliveries.first &&  $smarty.foreach.deliveries_ucd.first  && $mode == "nominatif"}}
		              <tr>
		                <th colspan="{{$cols}}" class="title">{{$_delivery->_ref_patient}}</th>
		                {{foreach from=$pilulier_init key=_date item=_pilulier_by_date}}
		                  <th class="title" style="font-size: 0.9em;" colspan="4">{{$_date|date_format:"%a %d/%m"}}</th>
		                {{/foreach}}
		              </tr>
		            {{/if}}
								
                {{if $smarty.foreach.deliveries.first && $_delivery->patient_id}}
								  {{assign var=_count_delivery_ucd  value=$_delivery_by_ucd|@count}}
                    <tr>
                      <td colspan="{{$cols}}" style="padding: 0; border-none; border-top: 1px solid #999;"></td>
  
                      {{if $_delivery->_ref_prises_dispensation_med|@count}}
                        {{foreach from=$_delivery->_pilulier key=_date item=_pilulier_by_date}}
                          {{foreach from=$_pilulier_by_date key=_moment item=_quantite name=quantites}}
													  {{assign var=hour_pil value=$list_moments.$_moment}}
                            <td rowspan="{{$_count_delivery_ucd+1}}" 
														    style="background-color: {{if !$_quantite && ("$_date $hour_pil:00:00" < $_delivery->datetime_min || "$_date $hour_pil:00:00" > $_delivery->datetime_max)}}#ddd{{else}}{{$color_moments.$_moment}}{{/if}}; 
																      {{if $smarty.foreach.quantites.last}}border-right: 1px solid #bbb;{{/if}} 
																			width: 20px; text-align: center; font-weight: bold; padding: 0; border-top: 1px solid #999;">
                              {{$_quantite}}
                            </td>
                          {{/foreach}}
                        {{/foreach}}
                      {{else}}
                        <td rowspan="{{$_count_delivery_ucd+1}}" colspan="{{$count_date*4}}" class="empty button">
                          Aucun pilulier
                        </td>
                      {{/if}}
                      
                    </tr>
                {{/if}}
							
		            <tr id="{{$_delivery->_guid}}">
		              {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$_delivery}}
		            </tr>
							{{/foreach}}
						{{/foreach}}
          {{foreachelse}}
            <td colspan="{{$cols+$count_date*4}}" class="empty">{{tr}}CProductDelivery.none{{/tr}}</td>
          {{/foreach}}
        {{foreachelse}}
				
        <tr>
          <td colspan="{{$cols+$count_date*4}}" class="empty">{{tr}}CProductDelivery.{{$mode}}.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>