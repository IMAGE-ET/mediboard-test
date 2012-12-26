{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th>{{tr}}Actions{{/tr}}</th>
  <th>{{tr}}CUserMail-date_inbox{{/tr}}</th>
  <th>{{tr}}CUserMail-from{{/tr}}</th>
  <th>{{tr}}CUserMail-subject{{/tr}}</th>
  <th>{{tr}}CUserMail-abstract{{/tr}}</th>
  <th>{{tr}}CUserMail-attachments{{/tr}}</th>
</tr>
<tbody>
  {{foreach from=$mails item=_mail}}
    <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
      <td>
        <button class="trash notext" title="button.trash notext">trash</button>
        <button class="hslip notext" title="button.hslip notext">hslip</button>
        <button class="print notext" title="button.print notext">print</button>
        <button class="tag notext" title="button.tag notext">tag</button>
      </td>
      <td>{{mb_value object=$_mail field=date_inbox format=relative}}</td>
      <td><label title="{{$_mail->from}}">{{$_mail->_from}}</label></td>
      <td><a href="#{{$_mail->_id}}"  onclick="messagerie.modalExternalOpen('{{$_mail->_id}}','{{$type}}');">{{if $_mail->subject}}{{mb_include template=inc_vw_type_message subject=$_mail->subject}}{{$_mail->subject|truncate:100:"(...)"}}{{else}}{{tr}}CUserMail-no_subject{{/tr}}{{/if}}</a></td>
      <td>{{$_mail->_text_plain->content|truncate}}</td>
      <td><img src="modules/messagerie/images/attachments.png" alt="attachments"/></td>
    </tr>
  {{/foreach}}
</tbody>