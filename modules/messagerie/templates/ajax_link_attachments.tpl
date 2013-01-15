{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage  messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
    return true;
  };

  attach = {
  object: null,
  id : null,
  files: "",

  setObject: function(object_class, object_id) {
    this.object = object_class;
    this.id = object_id;
  },

  doattach: function() {
    var url = new Url("messagerie","ajax_attach_codable");
    url.addParam("object_class", attach.object);
    url.addParam("object_id", attach.id);
    url.addParam("attachments", attach.files);

  }
};

  checkrelation = function() {
    attach.files = "";
    $$(".check input:checked").each(function(data) {
      if (attach.files !="") {
        attach.files = attach.files+"-";
      }
      attach.files = attach.files+data.value;
    });

    if (attach.object && attach.id && attach.files.length > 0) {
      $("do_link_attachments").show();
    }
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
          <th><input type="checkbox" onclick="messagerie.toggleSelect('list_attach', this.checked, 'checkbox_att'); checkrelation()" checked="checked" value="0"/></th>
        </tr>
      {{if $mail->_text_plain->_id && !$mail->_text_html->_id && $mail->_text_plain->content != ''}}
        <tr>
          <td><textarea>{{$mail->_text_plain->content}}</textarea></td>
          <td>{{tr}}CUserMail-body{{/tr}} (text)</td>
          <td class="check"><input type="checkbox" name="checkbox_att" class="check_att" checked="checked" value="plain"/></td>
        </tr>
      {{/if}}

      {{if $mail->_text_plain->_id && $mail->_text_html->_id && $mail->_text_html->content != ''}}
        <tr>
          <td><iframe src="?m={{$m}}&amp;a=vw_html_content&amp;mail_id={{$mail->_id}}&amp;suppressHeaders=1" style="width:100%;"></iframe></td>
          <td>{{tr}}CUserMail-body{{/tr}} (html)</td>
          <td class="check"><input type="checkbox" name="checkbox_att" class="check_att" checked="checked" value="html"/></td>
        </tr>
      {{/if}}
        {{assign var=attachments value=$mail->_attachments}}
      {{foreach from=$attachments item=_attachment}}
        <tr class="attachment">
          <td style="text-align: center;">
            {{if $_attachment->file_id}}<div style="float:left;" title="{{tr}}CMail-Attachment-linked{{/tr}}"><img src="style/mediboard/images/buttons/link.png" alt=""/><img src="style/mediboard/images/buttons/warning.png" alt=""/></div>{{/if}}
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
          <td class="check"><input type="checkbox" class="check_att" name="checkbox_att"  {{if !$_attachment->file_id}}checked="checked"{{/if}} value="{{$_attachment->_id}}" onclick="checkrelation()"/> </td>
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
      <div>
        <button id="do_link_attachments" style="display: none;" onclick="messagerie.dolinkAttachment(attach, '{{$mail->_id}}')">
          <img src="style/mediboard/images/buttons/up.png" alt=""/>{{tr}}Lier{{/tr}}<img src="style/mediboard/images/buttons/up.png" alt=""/>
        </button>
      </div>

    </td>
  </tr>
</table>
