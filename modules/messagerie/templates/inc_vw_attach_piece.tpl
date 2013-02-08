{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage  messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  requestInfoPat = function(pat_id, dossier_id) {
    var url = new Url("messagerie", "ajax_radio_last_refs");

    var oForm = getForm("editFrm");
    if (pat_id) {
      url.addParam("patient_id", pat_id);
    }
    else {
      if(!oForm.patient_id.value){
        return false;
      }
      url.addElement(oForm.patient_id);
    }

    if(dossier_id) {
      url.addParam("dossier_id", dossier_id);
    }

    url.addElement(oForm.consultation_id);
    url.requestUpdate("recherche_patient");
    return true;
  };

  attach = {
  object: null,
  id : null,
  files: "",
  plain:null,
  html:null,

  setObject: function(object_class, object_id) {
    this.object = object_class;
    this.id = object_id;
  }
};

  checkrelation = function() {
    attach.files = "";
    attach.plain = null;
    attach.html = null;
    $$(".check input:checked").each(function(data) {
      if (attach.files !="") {
        attach.files = attach.files+"-";
      }
      attach.files = attach.files+data.value;
    });

    var aform = getForm("select_attach");

    if ($$(".plain input:checked").length > 0) {
      attach.plain = aform.attach_plain.value;
    }

    if ($$(".html input:checked").length > 0) {
      attach.html = aform.attach_html.value;
    }

    if (attach.object && attach.id && (attach.files.length > 0 || attach.plain || attach.html)) {
      $("do_link_attachments").show();
    }
  };

  Main.add(function () {
    messagerie.listAttachLink('{{$mail_id}}');
    {{if $patient->_id}}
      requestInfoPat('{{$patient->_id}}','{{$dossier_id}}');
    {{/if}}
  });
</script>

<style>
  #linkAttachment img{
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
      <td style="width:50%;" id="list_attachments">
      </td>
      <td style="width:50%;">
        <form class="watched prepared" method="post" action="?m=dPcabinet" name="editFrm" autocomplete="off" novalidate="on">
        {{mb_field object=$patient field="patient_id" hidden=1 ondblclick="PatSelector.init()" onchange="requestInfoPat();"}}
          <input type="text" name="_pat_name" style="width: 15em;" value="{{$patient}}" readonly="readonly" onclick="PatSelector.init()" />
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
                  class="edit notext" {{if !$patient->_id}}style="display: none;"{{/if}}>
          {{tr}}Edit{{/tr}}
          </button>
          <br />
          <input type="text" name="_seek_patient" style="width: 13em;" placeholder="{{tr}}fast-search{{/tr}}" "autocomplete" onblur="$V(this, '')" />
        </form>

        <div id="recherche_patient"></div>
        <div>
          <button id="do_link_attachments" style="display: none;" onclick="messagerie.dolinkAttachment(attach, '{{$mail_id}}')">
            <img src="style/mediboard/images/buttons/up.png" alt=""/>{{tr}}Lier{{/tr}}<img src="style/mediboard/images/buttons/up.png" alt=""/>
          </button>
        </div>

      </td>
    </tr>
  </table>
