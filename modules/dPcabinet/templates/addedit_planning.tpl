<!-- $Id$ -->

{{mb_script module=patients    script=pat_selector    ajax=true}}
{{mb_script module=cabinet     script=plage_selector  ajax=true}}
{{mb_script module=cabinet     script=file            ajax=true}}
{{mb_script module=compteRendu script=document        ajax=true}}
{{mb_script module=compteRendu script=modele_selector ajax=true}}

{{if $consult->_id}}
  {{mb_ternary var=object_consult test=$consult->_refs_dossiers_anesth|@count value=$consult->_ref_consult_anesth other=$consult}}
  {{mb_include module="dPfiles" template="yoplet_uploader" object=$object_consult}}
{{/if}}

{{assign var=attach_consult_sejour value=$conf.dPcabinet.CConsultation.attach_consult_sejour}}

{{if "maternite"|module_active}}
  {{assign var=maternite_active value="1"}}
{{else}}
  {{assign var=maternite_active value="0"}}
{{/if}}

<script type="text/javascript">
Medecin = {
  form: null,
  edit : function() {
    this.form = getForm("editFrm");
    var url = new Url("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  set: function(id, view) {
    $('_adresse_par_prat').show().update('Autres : '+view);
    $V(this.form.adresse_par_prat_id, id);
    $V(this.form._correspondants_medicaux, '', false);
  }
};

refreshListCategorie = function(praticien_id){
  var url = new Url("dPcabinet", "httpreq_view_list_categorie");
  url.addParam("praticien_id", praticien_id);
  url.requestUpdate("listCategorie");
};

refreshFunction = function(chir_id) {
  {{if !$consult->_id && $conf.dPcabinet.CConsultation.create_consult_sejour}}
    var url = new Url("dPcabinet", "ajax_refresh_secondary_functions");
    url.addParam("chir_id", chir_id);
    url.requestUpdate("secondary_functions", function() {
      if (chir_id) {
        var form = getForm("editFrm");
        var chir = form.chir_id;
        var facturable = chir.options[chir.selectedIndex].get('facturable');
        form.___facturable.checked = facturable ? 'checked' : '';
        $V(form._facturable, facturable);
      }
    });
  {{/if}}
};

changePause = function(){
  var oForm = getForm("editFrm");
  if(oForm._pause.checked){
    oForm.patient_id.value = "";
    oForm._pat_name.value = "";
    $("viewPatient").hide();
    $("infoPat").update("");
  }else{
    $("viewPatient").show();
  }
};

requestInfoPat = function() {
  var oForm = getForm("editFrm");
  if(!oForm.patient_id.value){
    return false;
  }
  var url = new Url("patients", "httpreq_get_last_refs");
  url.addElement(oForm.patient_id);
  url.addElement(oForm.consultation_id);
  url.requestUpdate("infoPat");
};

ClearRDV = function(){
  var oForm = getForm("editFrm");
  $V(oForm.plageconsult_id, "", true);
  $V(oForm._date, "");
  $V(oForm.heure, "");
  if (Preferences.choosePatientAfterDate == 1) {
    PlageConsultSelector.init();
  }
};

checkFormRDV = function(form){
  if(!form._pause.checked && form.patient_id.value == ""){
    alert("Veuillez sélectionner un patient");
    PatSelector.init(false);
    return false;
  }
  else{
    var infoPat = $('infoPat');
    var operations = infoPat.select('input[name=_operation_id]');
    var checkedOperation = operations.find(function (o) {return o.checked});
    if (checkedOperation) {
      form._operation_id.value = checkedOperation.value;
    }
    
    return checkForm(form);
  }
};

submitRDV = function() {
  var form = getForm('editFrm');
  if (checkFormRDV(form)) {
    form.submit(); 
  }
};

printForm = function() {
  var url = new Url("dPcabinet", "view_consultation"); 
  url.addElement(getForm("editFrm").consultation_id);
  url.popup(700, 500, "printConsult");
  return;
};

printDocument = function(iDocument_id) {
  var form = getForm("editFrm");
  if (iDocument_id.value != 0) {
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addElement(form.consultation_id, "object_id");
    url.addElement(iDocument_id, "modele_id");
    url.popup(700, 600, "Document");
    return true;
  }
  return false;
};

linkSejour = function() {
  var url = new Url("dPcabinet", "ajax_link_sejour");
  url.addParam("consult_id", "{{$consult->_id}}");
  url.requestModal(350, 300);
};

unlinkSejour = function() {
  if (!confirm($T("CConsultation-_unlink_sejour"))) {
    return;
  }
  var form = getForm("editFrm");
  $V(form.sejour_id, "");
  $V(form._force_create_sejour, 1);
  form.submit();
};

checkCorrespondantMedical = function(id){
  var form = getForm("editFrm"+id);
  var url = new Url("dPplanningOp", "ajax_check_correspondant_medical");
  url.addParam("patient_id", $V(form.patient_id));
  url.addParam("object_id" , $V(form.consultation_id));
  url.addParam("object_class", '{{$consult->_class}}');
  url.requestUpdate("correspondant_medical");
};

Main.add(function () {
  var form = getForm("editFrm");
  requestInfoPat();
  {{if $plageConsult->_id && !$consult->_id && !$consult->heure}}
    $V(form.chir_id, '{{$plageConsult->chir_id}}', false);
    $V(form.plageconsult_id, '{{$plageConsult->_id}}');
    refreshListCategorie({{$plageConsult->chir_id}});
    PlageConsultSelector.init();
  {{elseif ($pat->_id || $date_planning) && !$consult->_id && !$consult->heure}}
    if($V(form.chir_id)) {
      PlageConsultSelector.init();
    }
  {{/if}}
  
  {{if $consult->_id && $consult->patient_id}}
    $("print_fiche_consult").disabled = "";
  {{/if}}
  
});
</script>


<div id="simple">
  {{mb_include template="plage_selector/inc_consult_simple"}}
  <table class="form">
    <tr>
      <td colspan="2" class="button">
        {{if $consult->_id}}
          {{if !$consult->_locks || $can->admin}}
            <button class="modify" type="submit" onclick="submitRDV();">
              {{tr}}Save{{/tr}}
            </button>
          {{/if}}

          {{mb_include template=inc_cancel_planning}}

          <button class="print" id="print_fiche_consult" type="button" onclick="printForm();"
            {{if !$consult->patient_id}} disabled="disabled" {{/if}}>
            {{tr}}Print{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit" onclick="submitRDV();">
            {{tr}}Create{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
    <tr>
      <td>{{mb_include template="plage_selector/inc_info_patient"}}</td>
    </tr>
  </table>
</div>


<script>
  PlageConsultSelector.init = function(mode){
    this.multipleMode   = (mode) ? 1 : 0;
    this.sForm            = "editFrm";
    this.sHeure           = "heure";
    this.sPlageconsult_id = "plageconsult_id";
    this.sDate            = "_date";
    this.sChir_id         = "chir_id";
    this.sFunction_id     = "_function_id";
    this.sDatePlanning    = "_date_planning";
    this.sLineElementId   = "_line_element_id";
    this.options          = {width: -30, height: -30};
    if (mode) {
      this.resetConsult();
      this.resetPage();
    }
    this.modal();
  };

  PatSelector.init = function(){
    this.sForm      = "editFrm";
    this.sId        = "patient_id";
    this.sView      = "_pat_name";
    var seekResult  = $V(getForm(this.sForm)._seek_patient).split(" ");
    this.sName      = seekResult[0] ? seekResult[0] : "";
    this.sFirstName = seekResult[1] ? seekResult[1] : "";
    {{if "maternite"|module_active && !$consult->_id}}
    this.sSexe = "_patient_sexe";
    {{/if}}
    this.pop();
  };
</script>