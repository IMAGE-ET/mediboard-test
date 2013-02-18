{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=messagerie script=UserEmail}}


{{if count($mails) > 0}}
<script type="text/javascript">
  Main.add(function () {
    var tabsMail = Control.Tabs.create("tab-mail", true);
    tabsMail.activeLink.onmousedown();
    //messagerie.getLastMessages({{$user->_id}}, null);
  });
</script>
{{/if}}

<div>
  <button class="button change" onclick="messagerie.getLastMessages({{$user->_id}});">{{tr}}CUserMAil-button-getNewMails{{/tr}}</button>
  <!--<button class="button mail" onclick="messagerie.modalWriteNewMessage();">{{tr}}CUserMAil-button-writeMail{{/tr}}</button>-->
  <select style="width: 50px;" name="action">
    <option va>{{tr}}CUserMail-option-More{{/tr}}</option>
    <option value="AllMarkAsRead" onclick="messagerie.markallAsRead()">{{tr}}CUserMail-option-allmarkasread{{/tr}}</option>
    <option value="AllMarkAsRead" onclick="">{{tr}}CUserMail-option-delete{{/tr}}</option>
  </select>
</div>
<table class="main" id="list_external_mail">
  <tr>
    <td style="width:200px;">
      {{if count($mails) > 0}}
        <ul id="tab-mail" class="control_tabs">
        {{foreach from=$mails key=k item=_mailbox}}
          <li>
            <a href="#{{$k}}" style="white-space: nowrap;" onmousedown="messagerie.refreshList(messagerie.page,'{{$k}}')"
              class=" {{if !$_mailbox->active}}empty{{/if}}">{{$_mailbox->libelle}}
            </a>
          </li>
        {{/foreach}}
        </ul>
      {{else}}
        <div class="small-info">{{tr}}CUserMail-noAccount{{/tr}}</div>
        <div>
          <a href="?m=mediusers&amp;a=edit_infos">{{tr}}CSourcePOP-add-acount{{/tr}}</a>
        </div>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td>
      {{foreach from=$mails key=k item=_list}}
        <table id="{{$k}}" class="tbl" style="display: none;">
        </table>
      {{/foreach}}
    </td>
  </tr>
</table>

