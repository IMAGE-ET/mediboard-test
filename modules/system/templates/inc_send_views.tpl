{{* $Id: view_messages.tpl 7622 2009-12-16 09:08:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>
  Envois pour la minute : '{{$minute}}'
  (heure courante {{$time|date_format:$conf.time}})
</h1>

<form name="ViewSenderUser" method="get" onsubmit="return ViewSender.doSend($V(this.user_username), $V(this.user_password));">

<table class="form">
  <tr>
    <th>{{mb_label object=$user field=user_username}}</th>
    <td>{{mb_field object=$user field=user_username}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field=user_password}}</th>
    <td>{{mb_field object=$user field=user_password prop=password}}</td>
  </tr>
  
  <tr>
    <td class="button" colspan="2">
      <button type="submit" class="change">{{tr}}Send{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CViewSender field=name}}</th>
    <th>
      {{mb_title class=CViewSender field=_url}} / 
      {{mb_title class=CViewSender field=_file}}
    </th>
    <th>{{mb_title class=CViewSender field=last_duration}}</th>
    <th>{{mb_title class=CViewSender field=last_size}}</th>
    <th>{{mb_title class=CSourceToViewSender field=source_id}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_duration}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_size}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_status}}</th>
    <th>{{mb_title class=CSourceToViewSender field=last_count}}</th>
  </tr>
	{{foreach from=$senders item=_sender}}
  <tbody class="hoverable">
	  <tr>
	    {{assign var=count_sources value=$_sender->_ref_senders_source|@count}}
	
	    <td rowspan="{{$count_sources}}">{{mb_value object=$_sender field=name}}</td>
	    <td rowspan="{{$count_sources}}" class="text compact">
	      {{mb_value object=$_sender field=_url}}
	      <br />
	      {{mb_value object=$_sender field=_file}}
	    </td>
	    <td rowspan="{{$count_sources}}">
	      {{$_sender->last_duration|round:3}}s
	    </td>
	    <td rowspan="{{$count_sources}}"> 
	      {{$_sender->last_size|decabinary}}
	    </td>
	    
	    {{foreach from=$_sender->_ref_senders_source item=_sender_source name=sender_source}}
	    <td>{{mb_value object=$_sender_source field=source_id tooltip=true}}</td>
	    <td>{{$_sender_source->last_duration|round:3}}s</td>
	    <td>{{$_sender_source->last_size|decabinary}}</td>
      
      {{assign var=class value=ok}}
      {{if $_sender_source->last_status != "checked"}} 
      {{assign var=class value=error}}
      {{/if}}
	    <td class="{{$class}}">
        {{mb_value object=$_sender_source field=last_status}}
      </td>

      {{assign var=class value=ok}}
      {{if $_sender_source->last_count != $_sender->max_archives}} 
      {{assign var=class value=warning}}
      {{/if}}
	    <td class="{{$class}}">
        {{mb_value object=$_sender_source field=last_count}} / 
        {{mb_value object=$_sender field=max_archives}} 
      </td>
      
	    {{if !$smarty.foreach.sender_source.last}}</tr><tr>{{/if}}
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