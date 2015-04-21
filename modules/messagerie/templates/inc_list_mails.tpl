{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="user_messages_actions" style="margin: 5px;">
  {{mb_include module=messagerie template=inc_usermails_actions}}
</div>

<table class="tbl">
  {{if !$account_pop->active}}
    <tr>
      <td colspan="6"><div class="small-warning">{{tr}}CSourcePOP-msg-notActive{{/tr}}</div></td>
    </tr>
  {{/if}}

  <tr>
    <th  class="narrow"{{if $user->isAdmin()}} colspan="2"{{/if}}></th>
    <th style="width: 30px;">Date</th>
    <th>{{if $mode == 'sentbox'}}{{tr}}CUserMail-to{{/tr}}{{else}}{{tr}}CUserMail-from{{/tr}}{{/if}}</th>
    <th>{{tr}}CUserMail-subject{{/tr}}</th>
    <th>{{tr}}CUserMail-abstract{{/tr}}</th>
  </tr>
  <tbody>
  {{if $nb_mails != 0}}
    <tr>
      <td colspan="6">
        {{mb_include module=system template=inc_pagination total=$nb_mails current=$page change_page="messagerie.refreshListPage" step=$app->user_prefs.nbMailList}}
      </td>
    </tr>
  {{/if}}
    {{foreach from=$mails item=_mail}}
      {{assign var=_mail_id value=$_mail->_id}}
      {{assign var=onclick value="messagerie.modalExternalOpen('$_mail_id','$account_id');"}}
      {{if $mode == 'drafts'}}
        {{assign var=onclick value="messagerie.edit('$_mail_id');"}}
      {{/if}}
      <tr {{if !$_mail->date_read}}style="font-weight: bold; background: red!important;"{{/if}} class="message alternate">
        <td class="button">
          <input type="checkbox" name="item_mail" value="{{$_mail->_id}}" />
        </td>
        {{if $user->isAdmin()}}
          <td>
            {{if $_mail->uid}}
              <button type="button" onclick="messagerie.openMailDebug('{{$_mail->_id}}');">
                <i class="msgicon fa fa-wrench"></i>
              </button>
            {{/if}}
            {{if $_mail->_is_apicrypt}}
              <img title="apicrypt" src="modules/messagerie/images/cle.png" alt="attachments" style="height:15px;"/>
            {{/if}}
          </td>
        {{/if}}
        <td onclick="{{$onclick}}">
          <span title="{{mb_value object=$_mail field=date_inbox}}">
            {{$_mail->_date_inbox}}
          </span>
        </td>
        <td class="text" onclick="{{$onclick}}">
          {{if $mode == 'sentbox'}}
            <label title="{{$_mail->to}}">{{$_mail->_to}}</label>
          {{else}}
            <label title="{{$_mail->from}}">{{$_mail->_from}}</label>
          {{/if}}
        </td>
        {{assign var=subject value=$_mail->subject}}
        <td class="text {{if !$subject}}empty{{/if}}" onclick="{{$onclick}}">
          {{if $_mail->favorite && $mode != 'favorites'}}
            <i class="msgicon fa fa-star" style="font-size: 1.5em; float: right; color: #ffa306; margin-right: 2px;" title="{{tr}}CUserMail-favorite{{/tr}}"></i>
          {{/if}}
          {{if count($_mail->_attachments)}}
            <i class="msgicon fa fa-paperclip" style="font-size: 1.5em; float: right; margin-right: 2px;" title="{{tr}}Attachments{{/tr}} : {{$_mail->_attachments|@count}}"></i>
          {{/if}}
          {{if $_mail->_is_apicrypt}}
            <i class="msgicon fa fa-key" style="font-size: 1.5em; float: right; margin-right: 2px;" title="Apicrypt"></i>
          {{/if}}
          <a href="#{{$_mail->_id}}" style="display: inline; vertical-align: middle;">
            {{if $subject}}{{mb_include template=inc_vw_type_message}}{{else}}{{tr}}CUserMail-no_subject{{/tr}}{{/if}}
          </a>
        </td>
        <td onclick="{{$onclick}}"{{if $_mail->_text_plain->content == ""}} class="empty">({{tr}}CUserMail-content-empty{{/tr}}){{else}} class="text compact">{{$_mail->_text_plain->content|truncate:256}}{{/if}}</td>
      </tr>
    {{foreachelse}}
      <tr><td class="empty" colspan="6"">{{tr}}CUserMail-none{{/tr}}</td></tr>
    {{/foreach}}

    {{if $nb_mails != 0}}
    <tr>
      <td colspan="6">
        {{mb_include module=system template=inc_pagination total=$nb_mails current=$page change_page="messagerie.refreshListPage" step=$app->user_prefs.nbMailList}}
      </td>
    </tr>
    {{/if}}
  </tbody>
</table>