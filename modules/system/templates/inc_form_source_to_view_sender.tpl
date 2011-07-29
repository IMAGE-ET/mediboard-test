{{* $Id: view_messages.tpl 7622 2009-12-16 09:08:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-{{$source_to_vw_sender->_guid}}" action="?m={{$m}}" method="post" onsubmit="return SourceToViewSender.onSubmit(this);">
  <input type="hidden" name="@class" value="{{$source_to_vw_sender->_class}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$source_to_vw_sender}}
  
  <table class="form">
    <tr>
      {{if $source_to_vw_sender->_id}}
      <th class="title modify" colspan="2">
        {{tr}}CSourceToViewSender-title-modify{{/tr}} '{{$source_to_vw_sender}}'
      {{else}}
      <th class="title" colspan="2">
        {{tr}}CSourceToViewSender-title-create{{/tr}}
      {{/if}}
      </th>
    </tr>
    
    <tr>
      <th>{{mb_label object=$source_to_vw_sender field=sender_id}}</th>
      <td>{{mb_field object=$source_to_vw_sender field=sender_id form="Edit-`$source_to_vw_sender->_guid`" autocomplete="true,1,50,true,true"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$source_to_vw_sender field=source_id}}</th>
      <td>{{mb_field object=$source_to_vw_sender field=source_id form="Edit-`$source_to_vw_sender->_guid`" autocomplete="true,1,50,true,true"}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $source_to_vw_sender->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="SourceToViewSender.confirmDeletion(this.form);">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>