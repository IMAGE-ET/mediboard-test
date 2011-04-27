{{* $Id: view_messages.tpl 7622 2009-12-16 09:08:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
	<tr>
    <th colspan="2">{{mb_title class=CViewSender field=name}}</th>
		<th>{{mb_title class=CViewSender field=description}}</th>
		</th>
    <th>{{mb_title class=CViewSender field=params}}</th>
    <th class="narrow">
      {{mb_title class=CViewSender field=period}}
    </th>
    <th class="narrow">
      {{mb_title class=CViewSender field=offset}}
    </th>
		<th colspan="60">
			{{tr}}CViewSender-_hour_plan{{/tr}}
		</th>
	</tr>

  {{foreach from=$senders item=_sender}}
  <tr>
    <td class="narrow">
      <button class="edit notext" style="float: right;" onclick="ViewSender.edit('{{$_sender->_id}}');">
        {{tr}}Edit{{/tr}}
      </button> 
      {{$_sender->_active}}
    </td>
    <td {{if ($_sender->_active)}}style="font-weight: bold;"{{/if}}>
      {{mb_value object=$_sender field=name}}
		</td>
		<td class="text compact">
      {{mb_value object=$_sender field=description}}
    </td>
    <td class="text compact">
    	{{$_sender->params|nl2br|replace:"=":" = "}}
		</td>
    <td style="text-align: right; padding-right: 0.5em;">{{mb_value object=$_sender field=period}}</td>
    <td style="text-align: right; padding-right: 0.5em;">{{mb_value object=$_sender field=offset}}mn</td>
    {{assign var=status value=$_sender->active|ternary:"ok":"off"}}
		{{foreach from=$_sender->_hour_plan key=min item=plan}}
    
    {{assign var=active value=""}}
    {{if ($min == $minute && $_sender->_active)}}{{assign var=active value=active}}{{/if}}
    
    <td class="hour-plan min-{{$min}} {{$plan|ternary:$status:''}} {{$active}}" title="{{$min}}" />
		{{/foreach}}
  </tr>
  {{foreachelse}}
	<tr>
		<td class="empty" colspan="65">{{tr}}CViewSender.none{{/tr}}</td>
	</tr>
  {{/foreach}}

  <!-- Bilan horaire -->
	{{if count($senders)}} 
  <tr style="height: 2px; border-top: 2px solid #888;"></tr>
  <tr>
    <td colspan="6" style="text-align: right;"><strong>Bilan horaire</strong></th>
    {{foreach from=$hour_sum key=min item=sum}}

    {{assign var=status value=""}}
    {{if $sum == 1}}{{assign var=status value=ok     }}{{/if}}
    {{if $sum >= 2}}{{assign var=status value=warning}}{{/if}}
    {{if $sum >= 4}}{{assign var=status value=error  }}{{/if}}

    {{assign var=active value=""}}
    {{if $sum && $min == $minute}}{{assign var=active value=active}}{{/if}}
    
    <td class="hour-plan {{$status}} {{$active}}" title="{{$sum}} @ {{$min}}" />
    {{/foreach}}
  </tr>
	{{/if}}
  
</table>

