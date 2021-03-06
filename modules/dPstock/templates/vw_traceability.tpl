{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
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
    <th rowspan="2" class="narrow">{{tr}}CProduct{{/tr}}</th>
    <th colspan="2" style="width: 25%;">{{tr}}CProductOrderItemReception{{/tr}}</th>
    <th colspan="3" style="width: 25%;">D�livrance</th>
    <th colspan="2" style="width: 25%;">R�ception service</th>
    <th colspan="3" style="width: 25%;">{{tr}}CAdministration{{/tr}}</th>
  </tr>
  <tr>
    <th>Date</th>
    <th>Quantit�</th>
    
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
          <th rowspan="{{$codes|@count}}" class="category">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$product->_guid}}')">
              {{$product}}
            </span>
          </th>
        {{/if}}
        <!-- <td class="narrow">{{$date|date_format:"%m/%d/%Y %H:%M:%S"}}</td>-->
        {{if $curr_evt.reception}}
          {{assign var=obj value=$curr_evt.reception}}
          <td style="text-align: center;">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">
            {{mb_value object=$obj field=date}}
            </span>
          </td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
        {{else}}
          <td colspan="2" />
        {{/if}}
        
        {{if $curr_evt.delivery}}
          {{assign var=obj value=$curr_evt.delivery}}
          <td style="text-align: center;" >
            <span onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">
            {{mb_value object=$obj field=$date_delivery}}
            </span>
          </td>
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
          <td style="text-align: center;" >
            <span onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">
            {{mb_value object=$obj field=date_reception}}
            </span>
          </td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
          <td style="text-align: center;">{{$obj->_ref_delivery->_ref_service}}</td>
        {{else}}
          <td colspan="2" />
        {{/if}}
        
        {{if $curr_evt.administration && false}}
          {{assign var=obj value=$curr_evt.administration}}
          <td style="text-align: center;">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$obj->_guid}}')">
            {{mb_value object=$obj field=date}}
            </span>
          </td>
          <td style="text-align: center;">{{mb_value object=$obj field=quantity}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=service}}</td>
          <td style="text-align: center;">{{mb_value object=$obj field=patient}}</td>
        {{else}}
          <td colspan="3" />
        {{/if}}
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="20">Aucun �venement</td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="20">Aucun code correspondant</td>
    </tr>
  {{/foreach}}
</table>