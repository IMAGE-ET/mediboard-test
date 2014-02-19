{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th colspan="6" class="title">
      {{tr}}CEAIRoute.list{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="section" colspan="2">{{tr}}CInteropSender{{/tr}}</th>
    <th class="section" colspan="2">{{tr}}CInteropReceiver{{/tr}}</th>
    <th class="section"></th>
  </tr>
  <tr>
    <th> {{mb_title class=CEAIRoute field=sender_class}} </th>
    <th> {{mb_title class=CEAIRoute field=sender_id}} </th>
    <th> {{mb_title class=CEAIRoute field=receiver_class}} </th>
    <th> {{mb_title class=CEAIRoute field=receiver_id}} </th>
    <th> {{mb_title class=CEAIRoute field=active}} </th>
  </tr>
  {{foreach from=$routes item=_route}}
    {{assign var=sender value=$_route->_ref_sender}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="Route.edit('{{$_route->_id}}')">
          {{tr}}Edit{{/tr}}
        </button>
        {{$sender->_class}}
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sender->_guid}}');">
           {{$sender->_view}}
         </span>
      </td>

      {{assign var=receiver value=$_route->_ref_receiver}}
      <td>
        {{$receiver->_class}}
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$receiver->_guid}}');">
           {{$receiver->_view}}
         </span>
      </td>
      <td>
        <form name="editActiveRoute{{$_route->_id}}" method="post" onsubmit="return onSubmitFormAjax(this)">
          {{mb_key object=$_route}}
          {{mb_class object=$_route}}
          {{mb_field object=$_route field="active" onchange=this.form.onsubmit()}}
        </form>
      </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="6" class="empty">{{tr}}CEAIRoute.none{{/tr}}</td></tr>
  {{/foreach}}
</table>