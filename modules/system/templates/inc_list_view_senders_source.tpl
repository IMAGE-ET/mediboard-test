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
    <th colspan="2">{{mb_title class=CViewSenderSource field=name}}</th>
    <th>{{mb_title class=CViewSenderSource field=libelle}}</th>
    <th>{{mb_title class=CViewSenderSource field=group_id}}</th>
    <th class="narrow" colspan="2">{{mb_title class=CViewSenderSource field=actif}}</th>
    <th>{{tr}}CViewSenderSource-back-senders_link{{/tr}}</th>
  </tr>

  {{foreach from=$senders_source item=_sender_source}}
  <tr>
    <td class="narrow">
      <button class="edit notext" style="float: right;" onclick="ViewSenderSource.edit('{{$_sender_source->_id}}');">
        {{tr}}Edit{{/tr}}
      </button> 
    </td>
    <td>{{mb_value object=$_sender_source field=name}}</td>
    <td class="text compact">{{mb_value object=$_sender_source field=libelle}}</td>
    <td class="text compact">{{mb_value object=$_sender_source field=group_id}}</td>
    <td>{{mb_value object=$_sender_source field=actif}}</td>
    <td>
      {{mb_include module=system template=inc_img_status_source exchange_source=$_sender_source->_ref_source_ftp}}
    </td>
    <td>
      {{foreach from=$_sender_source->_ref_senders item=_sender}}
      <div><span onmouseover="ObjectTooltip.createEx(this, '{{$_sender->_guid}}');">{{$_sender}}</span></div>
      {{foreachelse}}
      <div class="empty">{{tr}}CViewSenderSource-back-senders_link.empty{{/tr}}</div>
      {{/foreach}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty" colspan="65">{{tr}}CViewSenderSource.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>