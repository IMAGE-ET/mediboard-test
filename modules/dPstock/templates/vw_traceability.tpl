{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<form action="?" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="text" name="code" value="{{$code}}" />
  <button type="submit" class="tick notext">{{tr}}Filter{{/tr}}</button>
</form>

<table class="main tbl">
  <tr>
  	<th rowspan="2" style="width: 0.1%;">{{tr}}CProduct{{/tr}}</th>
    <th colspan="2" style="width: 25%;">{{tr}}CProductOrderItemReception{{/tr}}</th>
    <th colspan="3" style="width: 25%;">Délivrance</th>
    <th colspan="2" style="width: 25%;">Réception service</th>
		<th colspan="3" style="width: 25%;">{{tr}}CAdministration{{/tr}}</th>
  </tr>
	<tr>
		<th>Date</th>
    <th>Quantité</th>
		
    <th>Date</th>
    <th>Type</th>
    <th>Cible</th>
		
    <th>Date</th>
    <th>Service</th>
		
		<th>Date</th>
    <th>Service</th>
    <th>Patient</th>
  </tr>
  {{foreach from=$codes item=curr_code key=code name=code}}
    {{assign var=product value=$products.$code}}
    {{foreach from=$curr_code item=curr_evt key=date name=evt}}
      <tr>
	      {{if $smarty.foreach.code.first}}
		      <th rowspan="{{$codes|@count}}" class="tooltip-trigger category" onmouseover="ObjectTooltip.createEx(this, '{{$product->_guid}}')">
		        {{$product}}
		      </th>
		    {{/if}}
				<!-- <td style="width: 1%;">{{$date|date_format:"%m/%d/%Y %H:%M:%S"}}</td>-->
				{{if $curr_evt.reception}}
				  {{assign var=obj value=$curr_evt.reception}}
	        <td style="text-align: center;" class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">{{mb_value object=$obj field=date}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
				{{else}}
				  <td colspan="2" />
				{{/if}}
				
				{{if $curr_evt.delivery}}
          {{assign var=obj value=$curr_evt.delivery}}
          <td style="text-align: center;" class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">{{mb_value object=$obj field=$date_delivery}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
					{{if $obj->_ref_delivery->patient_id}}
	          <td style="text-align: center;">Patient</td>
						<td style="text-align: center;">{{$obj->_ref_delivery->_ref_patient}}</td>
				  {{else}}
            <td style="text-align: center;">Service</td>
            <td style="text-align: center;">{{$obj->_ref_delivery->_ref_service}}</td>
					{{/if}}
				{{else}}
				  <td colspan="3" />
				{{/if}}
			
			  {{if $curr_evt.delivery_reception}}
          {{assign var=obj value=$curr_evt.delivery_reception}}
          <td style="text-align: center;" class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">{{mb_value object=$obj field=date_reception}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
          <td style="text-align: center;">{{$obj->_ref_delivery->_ref_service}}</td>
				{{else}}
          <td colspan="2" />
        {{/if}}
				
				{{if $curr_evt.administration && false}}
          {{assign var=obj value=$curr_evt.administration}}
          <td style="text-align: center;" class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">{{mb_value object=$obj field=date}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=service}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=patient}}</td>
        {{else}}
          <td colspan="3" />
        {{/if}}
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="10">Aucun évenement</td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="10">Aucun code correspondant</td>
    </tr>
  {{/foreach}}
</table>