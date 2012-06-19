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
    <th class="title" colspan="3">Production</th>
    <th class="title" colspan="8">Envoi</th>
  <tr>
    <th>{{mb_title class=CViewSender field=name}}</th>
    <th>{{mb_title class=CViewSender field=last_duration}}</th>
    <th>{{mb_title class=CViewSender field=last_size}}</th>
    
    <th>{{mb_title class=CSourceToViewSender field=source_id}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_duration}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_size}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_status}}</th>
    <th>
      {{mb_title class=CSourceToViewSender field=last_count}} / 
      {{mb_title class=CViewSender field=max_archives}}
    </th>
    <th>
      {{mb_title class=CSourceToViewSender field=_last_age}} / 
      {{mb_title class=CViewSender field=period}}
    </th>
  </tr>
  
	{{foreach from=$senders item=_sender}}
  <tbody class="hoverable">
	  <tr>
	    {{assign var=count_sources value=$_sender->_ref_senders_source|@count}}
	
	    <td rowspan="{{$count_sources}}">
        {{mb_value object=$_sender field=name}}
      </td>
      
      <td rowspan="{{$count_sources}}">
        {{$_sender->last_duration|string_format:"%.3f"}}s
      </td>
      
      <td rowspan="{{$count_sources}}" title="{{$_sender->last_size}}">
        {{$_sender->last_size|decabinary}}
      </td>

	    {{foreach from=$_sender->_ref_senders_source item=_sender_source name=sender_source}}
	    <td>{{mb_value object=$_sender_source field=source_id tooltip=true}}</td>
	    <td>{{$_sender_source->last_duration|string_format:"%.3f"}}s</td>
			
      {{assign var=class value=ok}}
      {{if $_sender_source->last_size < 1000}} 
        {{assign var=class value=error}}
      {{/if}}
	    
      <td class="{{$class}}" title="{{$_sender_source->last_size}}">
        {{$_sender_source->last_size|decabinary}}
      </td>
      
      {{assign var=class value=ok}}
      {{assign var=colspan value="1"}}
      {{if $_sender_source->last_status != "checked"}} 
        {{assign var=class value=error}}
      {{/if}}
      {{if !$_sender_source->last_status}} 
	      {{assign var=colspan value="3"}}
	      {{assign var=class value=warning}}
      {{/if}}
	    <td colspan="{{$colspan}}" class="{{$class}}">
        {{mb_value object=$_sender_source field=last_status}}
      </td>

      {{if $_sender_source->last_status}} 
	      {{assign var=class value=ok}}
	      {{if $_sender_source->last_count < $_sender->max_archives}} 
	        {{assign var=class value=warning}}
	      {{/if}}
	      {{if $_sender_source->last_count > $_sender->max_archives}} 
	        {{assign var=class value=error}}
	      {{/if}}
	      <td class="{{$class}}">
	        {{mb_value object=$_sender_source field=last_count}} / 
	        {{mb_value object=$_sender field=max_archives}} 
	      </td>
	      
	      {{assign var=class value=ok}}
        {{if $_sender_source->_last_age > $_sender->period}} 
          {{assign var=class value=warning}}
        {{/if}}
	      {{if $_sender_source->_last_age > 2 * $_sender->period}} 
	        {{assign var=class value=error}}
	      {{/if}}
	      <td class="{{$class}}">
	        <span title="{{mb_value object=$_sender_source field=last_datetime}}">{{mb_value object=$_sender_source field=_last_age}}</span> / 
	        {{mb_value object=$_sender field=period}} 
	      </td>
      {{/if}}
      
	    {{if !$smarty.foreach.sender_source.last}}</tr><tr>{{/if}}
	    {{foreachelse}}
      <tr><td class="compact">{{tr}}CViewSender.none{{/tr}}</td></tr>
      {{/foreach}}
	  </tr>
  </tbody>

	{{foreachelse}}
  <tr>
    <td colspan="7" class="empty">
      {{tr}}CViewSender.noneactive{{/tr}}
    </td>
  </tr>
	{{/foreach}}
  
</table>