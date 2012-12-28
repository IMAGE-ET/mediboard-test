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
</tr>
<tbody>
  {{foreach from=$mails item=_mail}}
    <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
      <td>
        <form name="editMail{{$_mail->_id}}" action="" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_usermail_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="user_mail_id" value="{{$_mail->_id}}"/>
          <button type="button" class="trash notext" onclick="return confirmDeletion(this.form,{typeName:'messagerie',objName:'{{$_mail->_view|smarty:nodefaults|JSAttribute}}'}, {onComplete: messagerie.refreshList })">trash</button>
        </form>
        <button class="tag notext" title="button.tag notext">tag</button>
      </td>
      <td>{{mb_value object=$_mail field=date_inbox format=relative}}</td>
      <td>
        <label title="{{$_mail->from}}">{{$_mail->_from}}</label>
      </td>
      <td>
        <a href="#{{$_mail->_id}}"  onclick="messagerie.modalExternalOpen('{{$_mail->_id}}','{{$type}}');">
          {{if count($_mail->_attachments)}}<img title="{{$_mail->_attachments|@count}}" src="modules/messagerie/images/attachments.png" alt="attachments"/>{{/if}}
          {{if $_mail->subject}}{{mb_include template=inc_vw_type_message subject=$_mail->subject}}{{$_mail->subject|truncate:100:"(...)"}}{{else}}{{tr}}CUserMail-no_subject{{/tr}}{{/if}}
        </a></td>
      <td>{{$_mail->_text_plain->content|truncate}}</td>
    </tr>
  {{/foreach}}
</tbody>