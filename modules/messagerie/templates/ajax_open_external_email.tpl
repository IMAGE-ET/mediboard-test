{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{mb_script module="dPfiles" script="files" ajax=true}}
{{mb_script module=patients    script=pat_selector    ajax=true}}

<table class="form">
  <tr>
    <th class="title" colspan="4">{{mb_value object=$mail field=subject}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_label object=$mail field=from}}</th><td style="text-align: left;">{{mb_value object=$mail field=from}}</td>
    <th>{{mb_label object=$mail field=to}}</th><td style="text-align: left;">{{mb_value object=$mail field=to}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=date_inbox}}</th><td>{{mb_value object=$mail field=date_inbox}}</td>
    <th>{{mb_label object=$mail field=date_read}}</th><td>{{mb_value object=$mail field=date_read}}</td>
</table>
<hr/>
      <style>
        #content-html iframe, #content-plain {height:{{if !$mail->_attachments|count}}660{{else}}490{{/if}}px; overflow: auto;}
        #content-html td { padding:0; margin: 0; border:0; }
        div.gmail_quote,div.moz-forward-container { margin-left:10px; margin-top:20px; padding-left: 10px; border-left: grey 2px solid;  }
        #content-html iframe img {max-width: 90%;}

        #content-html iframe *{font-size: 11px;}

      </style>
      {{if $mail->text_html_id && $app->user_prefs.ViewMailAsHtml}}
        <div style="text-align: left;" id="content-html">
          {{if $mail->_text_html->content == ''}}
            {{tr}}CUserMail-msg-noContentText{{/tr}}
          {{else}}
            <iframe src="?m={{$m}}&amp;a=vw_html_content&amp;mail_id={{$mail->_id}}&amp;suppressHeaders=1" style="width:100%;"></iframe>
          {{/if}}
        </div>
      {{elseif $mail->text_plain_id}}
      <div style="text-align: left;" id="content-plain">
        {{$mail->_text_plain->content|nl2br}}
      </div>
      {{else}}
        <h1>{{tr}}CUserMail-msg-noContentText{{/tr}}</h1>
      {{/if}}
{{if $mail->_attachments|count}}
<table class="form">
  <tr><th class="title">{{tr}}Attachments{{/tr}} ({{$nbAttachPicked}}/{{$nbAttachAll}}) {{if $nbAttachPicked != $nbAttachAll}}<a href="#" tilte="{{tr}}CMailAttachment-button-getAllAttachments-desc{{/tr}}" onclick="messagerie.getAttachment('{{$mail->_id}}','0')" class="button download">{{tr}}CMailAttachment-button-getAllAttachments{{/tr}}</a>{{/if}}</th></tr>
</table>
  <ul id="list_attachment">
    <style>
      #list_attachment {
        height:170px;
        overflow: auto;
      }

      #list_attachment p{
        height:85px;
        margin-bottom: 0;
      }

      .attachments_list svg,.attachments_list img {
        max-width:200px;
        max-height:70px;
        box-shadow: -2px -2px 2px black;
      }

      .attachments_list {
        list-style: none;
        width:210px;
        height:130px;
        float:left;
      }
    </style>
    {{foreach from=$mail->_attachments key=key item=_attachment}}
        {{if $_attachment->_file->_id}}
          <li class="attachments_list">
            {{assign var=file value=$_attachment->_file}}
            <p>
                <a onclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', 'CFile', '{{$file->_id}}', '0');"  href="#" title="{{tr}}CMailAttachments-openAttachment{{/tr}}">
                  <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->_id}}&amp;phpThumb=1&amp;f=png" alt="Preview"/><br/>
                {{$file->file_name}} ({{$file->_file_size}})
                </a>
            </p>
              <form name="editFile{{$file->_id}}" action="" method="post">
              <input type="hidden" name="m" value="files" />
              <input type="hidden" name="dosql" value="do_file_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="file_id" value="{{$file->_id}}"/>
              <button type="button" class="trash notext" onclick="return confirmDeletion(this.form,{typeName:'messagerie',objName:'{{$file->_view|smarty:nodefaults|JSAttribute}}'})">trash</button>
            </form>
          </li>
        {{else}}
            <li class="attachments_list">
              <p>
                <a href="#{{$_attachment->_id}}" onclick="messagerie.getAttachment('{{$_attachment->_id}}')" style="text-align: center;">
                  <img src="images/pictures/unknown.png" style="height:100px;" alt=""/><br/>
          {{$_attachment->name}} ({{$_attachment->bytes}})
                </a>
              </p>
                <a href="#test" class="button lookup notext" onclick="messagerie.AttachFromPOP('{{$mail->_id}}','{{$_attachment->part}}')">{{tr}}Preview{{/tr}}</a>
              <a class="button download singleclick" href="#{{$_attachment->_id}}" onclick="messagerie.getAttachment('{{$mail->_id}}','{{$_attachment->_id}}')">{{tr}}CMailAttachment-button-getTheAttachment{{/tr}}</a>
            </li>
            <td></td>
        {{/if}}

    {{/foreach}}
  </ul>
{{/if}}
<table class="form">
    <tr><th class="title">Actions</th></tr>
    <tr>
      <td>
      <a href="#answer"  class="button"><img alt="message" src="images/icons/usermessage.png">{{tr}}CUserMail-button-answer{{/tr}}</a>
        {{if $app->user_prefs.LinkAttachment}}
          <a href="#{{$mail->_id}}" class="button copy" onclick="messagerie.linkAttachment('{{$mail->_id}}');">{{tr}}CMailAttachments-button-append{{/tr}}</a>
        {{/if}}
      <a href="#{{$mail->_id}}" class="button change">{{tr}}CUserMail-button-archive{{/tr}}</a>
      </td>
    </tr>
</table>
