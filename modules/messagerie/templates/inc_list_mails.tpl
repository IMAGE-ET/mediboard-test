{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th>Recu le</th>
  <th>Sujet</th>
  <th>De</th>
</tr>
<tbody>
  {{foreach from=$mails item=_mail}}
    <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
      <td>{{mb_value object=$_mail field=date_inbox format=relative}}</td>
      <td><a href="#{{$_mail->uid}}"  onclick="messagerie.modalPOPOpen('{{$_mail->uid}}','{{$type}}');">{{if $_mail->subject}}{{mb_include template=inc_vw_type_message subject=$_mail->subject}}{{$_mail->subject|truncate:100:"(...)"}}{{else}}{{tr}}CUserMail-no_subject{{/tr}}{{/if}}</a></td>
      <td><label title="{{$_mail->from}}">{{$_mail->_from}}</label></td>
      <td><label title="{{$_mail->to}}">{{$_mail->_to}}</label></td>
    </tr>
  {{/foreach}}
</tbody>