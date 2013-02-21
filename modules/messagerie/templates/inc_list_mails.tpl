{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$account_pop->active}}
  <tr>
    <td colspan="5"><div class="warning">{{tr}}CSourcePOP-msg-notActive{{/tr}}</div></td>
  </tr>
{{/if}}

<tr>
  <th style="width: 10px;"><input type="checkbox" value="" onclick="messagerie.toggleSelect('list_external_mail', this.checked,'item_mail')"/>{{tr}}Actions{{/tr}}</th>
  <th style="width: 30px;">{{tr}}CUserMail-date_inbox{{/tr}}</th>
  <th>{{tr}}CUserMail-from{{/tr}}</th>
  <th>{{tr}}CUserMail-subject{{/tr}}</th>
  <th>{{tr}}CUserMail-abstract{{/tr}}</th>
</tr>
<tbody>
  {{foreach from=$mails item=_mail}}
    <tr {{if !$_mail->date_read}}style="font-weight: bold; background: red!important;"{{/if}}>
      <td>
        <input type="checkbox" name="item_mail" value="{{$_mail->_id}}" />

        <form name="editMail{{$_mail->_id}}" method="post" action="">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_usermail_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="user_mail_id" value="{{$_mail->_id}}"/>
          <button type="button" class="trash notext" onclick="return confirmDeletion(this.form,{typeName:'messagerie',objName:'{{$_mail->_view|smarty:nodefaults|JSAttribute}}'}, {onComplete: messagerie.refreshList.curry(messagerie.page,'{{$account}}')})">Supprimer le message</button>
        </form>
        <!--(<button class="tag notext" title="button.tag notext">tag</button>)-->
        <button onclick="messagerie.toggleFavorite('{{$_mail->_id}}');" class="nowrap"><img src="modules/{{$m}}/images/favorites-{{$_mail->favorite}}.png" alt="" style="height:15px;"/></button>
      </td>
      <td>{{mb_value object=$_mail field=date_inbox format=relative}}</td>
      <td>
        <label title="{{$_mail->from}}">{{$_mail->_from}}</label>
      </td>
      <td>
        <a href="#{{$_mail->_id}}"  onclick="messagerie.modalExternalOpen('{{$_mail->_id}}','{{$account}}');" style="display: inline; vertical-align: middle;">
          {{if $_mail->subject}}{{mb_include template=inc_vw_type_message subject=$_mail->subject}}{{$_mail->subject|truncate:100:"(...)"}}{{else}}{{tr}}CUserMail-no_subject{{/tr}}{{/if}}
        </a>
        {{if count($_mail->_attachments)}}
          <img title="{{$_mail->_attachments|@count}}" src="modules/messagerie/images/attachments.png" alt="attachments"/>
        {{/if}}
        {{if $_mail->_is_apicrypt}}
          <img title="apicrypt" src="modules/messagerie/images/cle.png" alt="attachments" style="height:15px;"/>
        {{/if}}
      </td>
      <td{{if $_mail->_text_plain->content == ""}} class="empty">({{tr}}CUserMail-content-empty{{/tr}}){{else}}>{{$_mail->_text_plain->content|truncate}}{{/if}}</td>
    </tr>
  {{foreachelse}}
    <tr><td class="empty" colspan="5">{{tr}}CUserMail-none{{/tr}}</td></tr>
  {{/foreach}}

  {{if $nb_mails != 0}}
  <tr>
    <td colspan="5">
      {{mb_include module=system template=inc_pagination total=$nb_mails current=$page change_page='messagerie.refreshList' step=$app->user_prefs.nbMailList}}
    </td>
  </tr>
  {{/if}}
</tbody>