{{*
  * List of attachments to link
  *  
  * @category messagerie
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<form name="select_attach" method="get" action="?">
  <table class="tbl" id="list_attach">
    <tr>
      <th>Doc</th>
      <th><input type="checkbox" onclick="messagerie.toggleSelect('list_attach', this.checked, 'checkbox_att'); checkrelation()" checked="checked" value="0"/></th>
    </tr>

  {{if $mail->_text_plain->_id}}

    {{if !$mail->_text_html->_id}}
      <tr>
        <td {{if $mail->text_file_id}}class="ok"{{/if}}>
          <textarea style="width:100%; height:100px;">{{$mail->_text_plain->content}}</textarea>
          {{if $mail->text_file_id}}
            <div style="text-align: center;" "><img src="style/mediboard/images/buttons/link.png" alt=""/>{{tr}}CMailAttachments-LinkedTo{{/tr}}<span onmouseover="ObjectTooltip.createEx(this, '{{$mail->_ref_file_linked->_ref_object->_guid}}')">{{$mail->_ref_file_linked->_ref_object->_view}}</span></div>
          {{/if}}
        </td>
        <td class="plain"><input type="checkbox" name="attach_plain" class="check_att" {{if !$mail->text_file_id}}checked="checked"{{/if}} value="{{$mail->_text_plain->_id}}" onclick="checkrelation()"/></td>
      </tr>
    {{elseif $mail->_text_html->_id}}
      <tr>
        <td {{if $mail->text_file_id}}class="ok"{{/if}}><iframe src="?m={{$m}}&amp;a=vw_html_content&amp;mail_id={{$mail->_id}}&amp;suppressHeaders=1" style="width:100%;"></iframe>
          {{if $mail->text_file_id}}
            <div><img src="style/mediboard/images/buttons/link.png" alt=""/>{{tr}}CMailAttachments-LinkedTo{{/tr}}<span onmouseover="ObjectTooltip.createEx(this, '{{$mail->_ref_file_linked->_ref_object->_guid}}')">{{$mail->_ref_file_linked->_ref_object->_view}}</span></div>
          {{/if}}
        </td>
        <td class="html"><input type="checkbox" name="attach_html" class="check_att" {{if !$mail->text_file_id}}checked="checked"{{/if}} value="{{$mail->_text_html->_id}}" onclick="checkrelation()"/></td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="2">
        <label>
          Renommer : <input type="text" name="rename_text" value="sans_titre" onchange="checkrelation()" />
        </label>
        <label>
          Catégorie : <select name="category_id" style="width:12em;" onchange="checkrelation()">
            <option value="">&mdash; Sans catégorie</option>
            {{foreach from=$cats item=_cat}}
              <option value="{{$_cat->_id}}">{{$_cat}}</option>
            {{/foreach}}
          </select>
        </label>
      </td>
    </tr>
  {{/if}}

  {{assign var=attachments value=$mail->_attachments}}
  {{foreach from=$attachments item=_attachment}}
    <tr class="attachment">
      <td style="text-align: center;" {{if $_attachment->file_id}}class="ok"{{/if}}>
        {{assign var=file value=$_attachment->_file}}
        {{if $file->_id}}
          <div>
            <a onclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', 'CFile', '{{$file->_id}}', '0');"  href="#" title="{{tr}}CMailAttachments-openAttachment{{/tr}}">
              <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->_id}}&amp;phpThumb=1&amp;f=png&amp;h=50" alt="Preview"/><br/>
              {{$file->file_name}}<br/>
            </a>
          </div>
          {{if $_attachment->file_id}}
            <div style="text-align: left;">
              <img src="style/mediboard/images/buttons/link.png" alt=""/>{{tr}}CMailAttachments-LinkedTo{{/tr}}<span onmouseover="ObjectTooltip.createEx(this, '{{$_attachment->_file->_ref_object->_guid}}')">{{$_attachment->_file->_ref_object->_view}}</span>
              <button type="button" class="cancel notext" onclick="messagerie.cancelAttachment('{{$_attachment->_id}}','{{$mail->_id}}')"></button>
            </div>
          {{/if}}
          {{else}}
          <img src="images/pictures/unknown.png" alt=""/><br/>
          {{$_attachment->name}}
        {{/if}}
      </td>
      <td class="check {{if $_attachment->file_id}}ok{{/if}}"><input type="checkbox" class="check_att" name="checkbox_att"  {{if !$_attachment->file_id}}checked="checked"{{/if}} value="{{$_attachment->_id}}" onclick="checkrelation()"/> </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="3" class="empty">{{tr}}CMailAttachments-none{{/tr}}</td></tr>
  {{/foreach}}
  </table>
</form>