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
    <td>{{mb_field object=$user field=user_password}}</td>
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
    <th>{{mb_title class=CViewSender field=_url}} / {{mb_title class=CViewSender field=_file}}</th>
    <th>Durée</th>
    <th>Poids</th>
    <th>Envois</th>
  </tr>
	{{foreach from=$senders item=_sender}}
  {{if $_sender->_active}}
  <tr>
    <td>{{mb_value object=$_sender field=name}}</td>
    <td class="text compact">
      {{mb_value object=$_sender field=_url}}
      <br />
      {{mb_value object=$_sender field=_file}}
    </td>
  </tr
  {{/if}}
	{{foreachelse}}
  <tr><td class="empty">Aucune active</td></tr>
	{{/foreach}}
</table>