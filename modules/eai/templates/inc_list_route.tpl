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
    <th>
      {{mb_title class=CEAIRoute field=sender_class}}
    </th>
    <th>
      {{mb_title class=CInteropSender field=nom}} {{tr}}CInteropSender{{/tr}}
    </th>
    <th>
      {{mb_title class=CEAIRoute field=receiver_class}}
    </th>
    <th>
      {{mb_title class=CInteropReceiver field=nom}} {{tr}}CInteropReceiver{{/tr}}
    </th>
    <th>
      {{mb_title class=CEAIRoute field=active}}
    </th>
  </tr>
  {{foreach from=$routes item=_route}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="Route.edit('{{$_route->_id}}')">
          {{tr}}Edit{{/tr}}
        </button>
        {{$_route->sender_class}}
      </td>
      <td>
        {{$_route->_ref_sender->nom}}
      </td>
      <td>
        {{$_route->receiver_class}}
      </td>
      <td>
        {{$_route->_ref_receiver->nom}}
      </td>
      <td>
        <form name="editActiveRoute" method="post" onsubmit="return onSubmitFormAjax(this)">
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