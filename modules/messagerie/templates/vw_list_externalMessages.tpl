{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=messagerie script=UserEmail}}

<script type="text/javascript">
  Main.add(function () {
    var tabs =Control.Tabs.create("tab-mail", true);
    tabs.activeLink.onmousedown();
    //messagerie.getLastMessages({{$user->_id}},null);
  });
</script>

<div>
<button class="button change" onclick="messagerie.getLastMessages({{$user->_id}});">{{tr}}CUserMAil-button-getNewMails{{/tr}}</button>
  <select style="width: 50px;" name="action">
    <option va>{{tr}}CUserMail-option-More{{/tr}}</option>
    <option value="">{{tr}}CUserMail-option-allmarkasread{{/tr}}</option>
  </select>
</div>
<table class="main" id="list_external_mail">
  <tr>
    <td style="width:200px;">
      <ul id="tab-mail" class="control_tabs_vertical">
      {{foreach from=$mails key=k item=_mail}}
        <li>
          <a href="#{{$k}}" style="white-space: nowrap;" onmousedown="messagerie.refreshList('{{$k}}')"
            {{if count($_mail)==0}}class="empty"{{/if}}>{{tr}}CUserMail.{{$k}}{{/tr}} <small>({{$_mail|@count}})</small> </a>
        </li>
      {{/foreach}}
      </ul>
    </td>
    <td>
      {{foreach from=$mails key=k item=_list}}
        <table id="{{$k}}" class="tbl" style="display: none;"></table>
      {{/foreach}}

    </td>
  </tr>
</table>


