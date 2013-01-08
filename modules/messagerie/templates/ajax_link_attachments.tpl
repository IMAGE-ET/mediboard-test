{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage  messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{* {{mb_script module=patients    script=pat_selector    ajax=true}} *}}
<script>
  requestInfoPat = function() {
    var oForm = getForm("editFrm");
    if(!oForm.patient_id.value){
    return false;
    }
    var url = new Url("messagerie", "ajax_radio_last_refs");
    url.addElement(oForm.patient_id);
    url.addElement(oForm.consultation_id);
    url.requestUpdate("recherche_patient");
  };
</script>
<style>
  #linkAttachment textarea, #linkAttachment img{
    max-width: 100px;
    max-height: 100px;
  }

  #linkAttachment li{
    list-style: none;
  }

  #linkAttachment li div{
    width:150px;
    height:100px;
    text-align: center;

  }
</style>

<table class="main" id="linkAttachment">
  <tr><th colspan="2" class="title">Liaison à mediboard</th></tr>
  <tr>
    <td style="width:50%;">
      <table class="tbl" id="list_attach">
        <tr>
          <th>Doc</th>
          <th>extension</th>
          <th><input type="checkbox" onclick="messagerie.toggleSelect('list_attach', this.checked, 'checkbox_att');" checked="checked"/></th>
        </tr>
      {{if $mail->_text_plain->_id && !$mail->_text_html->_id && $mail->_text_plain->content != ''}}
        <tr>
          <td><textarea>{{$mail->_text_plain->content}}</textarea></td>
          <td>{{tr}}CUserMail-body{{/tr}} (text)</td>
          <td class="check"><input type="checkbox" name="checkbox_att" checked="checked"/></td>
        </tr>
      {{/if}}

      {{if $mail->_text_plain->_id && $mail->_text_html->_id && $mail->_text_html->content != ''}}
        <tr>
          <td><iframe src="?m={{$m}}&amp;a=vw_html_content&amp;mail_id={{$mail->_id}}&amp;suppressHeaders=1" style="width:100%;"></iframe></td>
          <td>{{tr}}CUserMail-body{{/tr}} (html)</td>
          <td class="check"><input type="checkbox" name="checkbox_att" checked="checked"/></td>
        </tr>
      {{/if}}
        {{assign var=attachments value=$mail->_attachments}}
      {{foreach from=$attachments item=_attachment}}
        <tr class="attachment">
          <td style="text-align: center;">
          {{assign var=file value=$_attachment->_file}}
          {{if $file->_id}}
            <div>
              <a onclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', 'CFile', '{{$file->_id}}', '0');"  href="#" title="{{tr}}CMailAttachments-openAttachment{{/tr}}">
                <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->_id}}&amp;phpThumb=1&amp;f=png&amp;h=50" alt="Preview"/><br/>
                {{$file->file_name}}
              </a>
            </div>
            {{else}}
            <img src="images/pictures/unknown.png" alt=""/><br/>
            {{$_attachment->name}}
          {{/if}}
          </td>
          <td>{{$_attachment->extension}}</td>
          <td class="check"><input type="checkbox" name="checkbox_att" checked="checked"/> </td>
        </tr>
      {{/foreach}}

      </table>
    </td>
    <td style="width:50%;">
      <form class="watched prepared" method="post" action="?m=dPcabinet" name="editFrm" autocomplete="off" novalidate="on">
      {{mb_field object=$pat field="patient_id" hidden=1 ondblclick="PatSelector.init()" onchange="requestInfoPat();"}}
        <input type="text" name="_pat_name" style="width: 15em;" value="" readonly="readonly" onclick="PatSelector.init()" />
        <button class="search notext" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
        <script type="text/javascript">
          PatSelector.init = function(){
            this.sForm      = "editFrm";
            this.sId        = "patient_id";
            this.sView      = "_pat_name";
            var seekResult  = $V(getForm(this.sForm)._seek_patient).split(" ");
            this.sName      = seekResult[0] ? seekResult[0] : "";
            this.sFirstName = seekResult[1] ? seekResult[1] : "";
            this.pop();
          }
        </script>
        <button id="button-edit-patient" type="button"
                onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form.patient_id.value"
                class="edit notext" {{if !$pat->_id}}style="display: none;"{{/if}}>
        {{tr}}Edit{{/tr}}
        </button>
        <br />
        <input type="text" name="_seek_patient" style="width: 13em;" placeholder="{{tr}}fast-search{{/tr}}" "autocomplete" onblur="$V(this, '')" />
      </form>

      <div id="recherche_patient"></div>

      <button id="do_link_attachments" style="display: none;">{{tr}}Lier{{/tr}}</button>
    </td>
  </tr>
</table>
