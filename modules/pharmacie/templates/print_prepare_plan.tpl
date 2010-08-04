{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(window.print);
</script>

<h1>Plan de cueillette du 
  {{$date_min|date_format:$dPconfig.datetime}} au 
  {{$date_max|date_format:$dPconfig.datetime}} le 
  {{$smarty.now|date_format:$dPconfig.datetime}}</h1>

<h2>Délivrances {{if $mode == "nominatif"}}nominatives{{else}}globales{{/if}}</h2>
<table class="main tbl">
  <colgroup>
    <col span="5" style="width: 0.1%;" />
  </colgroup>
  
  <tr>
  	{{if $mode == "nominatif"}}
		<th>{{tr}}CProductDelivery-patient_id{{/tr}}</th>
    {{/if}}
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDeliveryTrace-quantity{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-location_id{{/tr}}</th>
    <th>{{tr}}CProductDeliveryTrace-code{{/tr}}</th>
  </tr>
{{foreach from=$deliveries item=curr_list key=id}}
  <tr>
    <th colspan="20">{{$list_services.$id->_view}}</th>
  </tr>
  {{foreach from=$curr_list item=disp}}
  <tr>
  	{{if $disp->patient_id}}
  	<td>{{$disp->_ref_patient->_view}}</td>
		{{/if}}
    <td>{{$disp->_ref_stock->_ref_product->code}}</td>
    <td><strong>{{$disp->_ref_stock->_ref_product->name}}</strong></td>
    <td style="text-align: right;">{{$disp->quantity}}</td>
    <td>{{$disp->_ref_stock->_ref_product->_unit_title}}</td>
		<td>{{$disp->_ref_stock->_ref_location->name}}</td>
    <td></td>
  </tr>
  {{/foreach}}
{{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>