{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{mb_script module=messagerie script=UserEmail}}
{{mb_script path="lib/ckeditor/ckeditor.js"}}

<script type="text/javascript">
  CKEDITOR.editorConfig = function(config) {
    config.toolbar = [['Preview', 'Print', '-','Find']];
  }
  Main.add(function () {
  Control.Tabs.create("tab-mail", true);
  });
</script>

{{* Error, POP config not found

*}}
{{if !$account_ok}}
  <div class="small-warning">
    {{tr}}CSourcePOP-error-AccountNotFound{{/tr}} (<a href="?m=mediusers&a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a>)
  </div>
{{/if}}

<table class="main">
  <tr>
    <td style="vertical-align: top; " class="narrow">
      <ul id="tab-mail" class="control_tabs_vertical">
        {{foreach from=$listMails key=k item=_item}}
          {{assign var=count value=$_item|@count}}
          <li>
            <a href="#{{$k}}" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>{{tr}}CUserMail.{{$k}}{{/tr}} <small>({{$count}})</small> </a>
          </li>
        {{/foreach}}

      </ul>
    </td>
    <td>
      {{foreach from=$listMails key=k item=_list}}
        <table class="main tbl" id="{{$k}}" style="display: none;">
          <tr>
            <th class="title" colspan="4">{{tr}}CUserMail.{{$k}}{{/tr}} </th>
          </tr>
          <tr>
            <th>
              {{tr}}Date{{/tr}}
            </th>
            <th>
              {{tr}}Subject{{/tr}}
            </th>
            <th>
              {{tr}}From{{/tr}}
            </th>
            <th>
              {{tr}}To{{/tr}}
            </th>
          </tr>
          {{foreach from=$_list item=_msg}}
            <tr>
              <td>{{$_msg->date|date_format:"%d/%m/%Y @%H:%M"}}</td>
              <td><a href="#{{$_msg->msgno}}"  onclick="messagerie.modalPOPOpen({{$_msg->msgno}});"><strong>{{$_msg->subject|truncate:100:"(...)"}}</strong></a></td>
              <td><label title="{{$_msg->from}}">{{$_msg->_from}}</label></td>
              <td><label title="{{$_msg->to}}">{{$_msg->_to}}</label></td>
            </tr>
          {{/foreach}}
        </table>
      {{/foreach}}
    </td>
  </tr>
</table>
