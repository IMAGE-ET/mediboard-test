{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">

var cim10url = new Url;

function reloadCim10(sCode){
  var oForm = document.addDiagFrm;
  
  oCimField.add(sCode);
 
  {{if $_is_anesth}}
  if (document.addOpFrm && document.addOpFrm.operation_id.value != '') {
    oCimAnesthField.add(sCode);
  }
  {{/if}}
  updateTokenCim10();
  oForm.code_diag.value="";
}

function updateTokenCim10() {
  var oForm = document.editDiagFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadDossierMedicalPatient });
}

function updateTokenCim10Anesth(){
  var oForm = document.editDiagAnesthFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadDossierMedicalSejour });
}

function dateAntecedent(){
  var oForm = document.editAntFrm;
  var oEnCours = oForm._date_ant;
  var oHiddenField = oForm.date;
  oForm._hidden_rques.value = oForm.rques.value;
  oForm.rques.value = "";
  var oViewField = document.getElementById('editAntFrm_date_da');
  var oTriggerField = document.getElementById('editAntFrm_date_trigger');
  if (oEnCours.checked) {
    oHiddenField.value = "{{$today}}";
    oViewField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerField.style.display = "inline";
  }else{
    oHiddenField.value = "";
    oViewField.innerHTML = "";
    oTriggerField.style.display = "none";
  }   
}

function dateTrmt(){
  var oForm = document.editTrmtFrm;
  var oDateTrmt = oForm._datetrmt
  var oHiddenDebutField = oForm.debut;
  var oViewDebutField = document.getElementById('editTrmtFrm_debut_da');
  var oTriggerDebutField = document.getElementById('editTrmtFrm_debut_trigger');
  var oHiddenFinField = oForm.fin;
  var oViewFinField = document.getElementById('editTrmtFrm_fin_da');
  var oTriggerFinField = document.getElementById('editTrmtFrm_fin_trigger');
  if (oDateTrmt.checked) {
    oHiddenDebutField.value = "{{$today}}";
    oViewDebutField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerDebutField.style.display = "inline";
    oForm._en_cours.disabled = false;
    dateFinTrmt();
  }else{
    oHiddenDebutField.value = "";
    oViewDebutField.innerHTML = "";
    oTriggerDebutField.style.display = "none";
    oHiddenFinField.value = "";
    oViewFinField.innerHTML = "";
    oTriggerFinField.style.display = "none";
    oForm._en_cours.disabled = true;
  }
}

function dateFinTrmt(){
  var oForm = document.editTrmtFrm;
  var oEnCours = oForm._en_cours;
  var oHiddenField = oForm.fin;
  var oViewField = document.getElementById('editTrmtFrm_fin_da');
  var oTriggerField = document.getElementById('editTrmtFrm_fin_trigger');
  if (oEnCours.checked) {
    oHiddenField.value = "{{$today}}";
    oViewField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerField.style.display = "inline";

  } else {
    oHiddenField.value = "";
    oViewField.innerHTML = "En cours";
    oTriggerField.style.display = "none";
  }
}

function finTrmt() {
  var oForm = document.editTrmtFrm;
  oForm._hidden_traitement.value = oForm.traitement.value;
  oForm.traitement.value = "";
  oForm._helpers_traitement.value = "";
}


function reloadDossierMedicalPatient(){
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents");
  antUrl.addParam("patient_id", "{{$patient->_id}}");
  antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
  {{if $_is_anesth}}
  antUrl.addParam("sejour_id", tabSejour[document.addOpFrm.operation_id.value]);
  {{/if}}
  antUrl.requestUpdate('listAnt', { waitingText : null } );
}

function reloadDossierMedicalSejour() {
  var antUrl = new Url;
  var sejour_id = tabSejour[document.addOpFrm.operation_id.value];
  if(!sejour_id) {
    sejour_id = document.addOpFrm.sejour_id.value;
  }
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents_anesth");  
  antUrl.addParam("sejour_id", sejour_id);
  antUrl.requestUpdate('listAntCAnesth', { waitingText : null });
}

function reloadDossiersMedicaux(){
  reloadDossierMedicalPatient();

  {{if $_is_anesth}}
  if (document.addOpFrm.operation_id.value != ""){
    reloadDossierMedicalSejour();
  }
  {{/if}}
}

function onSubmitAnt(oForm) {
	if (oForm.rques.value.blank()) {
    return false;
  }
	
  onSubmitFormAjax(oForm, {
  	onComplete : reloadDossiersMedicaux 
  } );
  
  dateAntecedent();
  
  return false;
}

function onSubmitTraitement(oForm) {
	if (oForm.traitement.value.blank()) {
    return false;
  }
	
  onSubmitFormAjax(oForm, {
  	onComplete : reloadDossiersMedicaux 
  } );
  
  finTrmt();
  
  return false;
}

function easyMode() {
  var width = 800;
  var height = 500;

  var url = new Url();
  url.setModuleAction("dPcabinet", "vw_ant_easymode");
  url.addParam("user_id", "{{$userSel->_id}}");

  url.pop(width, height, "easyMode");
}

</script>

<table class="form">
  <tr>
    <td class="text">
      <button class="edit" type="button" onclick="easyMode();">Mode de saisie simplifié</button>
    
     {{include file="../../dPcabinet/templates/inc_consult_anesth/inc_addictions.tpl}}
      <hr />
      
      <!-- Antécédents -->
      <form name="editAntFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitAnt(this);">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->patient_id}}" />
      
      <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_id}}" />
      {{/if}}

      <table class="form">
        <tr>
          <th>
            <input type="checkbox" name="_date_ant" onclick="dateAntecedent()" />
            {{mb_label object=$antecedent field="date"}}
          </th>
          <td class="date">
            <div id="editAntFrm_date_da"></div>
            <input type="hidden" name="date" value="" />
            <img id="editAntFrm_date_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début" style="display:none;" />
          </td>
          
          <td id="listAides_Antecedent_rques">
            {{mb_label object=$antecedent field="rques"}}
            {{foreach from=$antecedent->_aides.rques item=_helpers key=dependsOn}}
            {{if $dependsOn != "no_enum"}} 
            <select name="_helpers_rques-{{$dependsOn}}" size="1" onchange="pasteHelperContent(this)" style="display:none;">
              <option value="">&mdash; Choisir une aide</option>
              {{foreach from=$_helpers item=list_aides key=sTitleOpt}}
                <optgroup label="{{$sTitleOpt}}">
                  {{html_options options=$list_aides}}
                </optgroup>
              {{/foreach}}
            </select>
            {{/if}}
            {{/foreach}}
            <input type="hidden" name="_hidden_rques" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAntecedent', this.form._hidden_rques, 'rques', this.form.type.value)">
              Nouveau
            </button>
          </td>
        </tr>

        <tr>
			    <!-- Auto-completion -->
			    <th style="width: 70px;">{{mb_label object=$antecedent field=_search}}</th>
			    <td style="width:100px;">
			      {{mb_field object=$antecedent field=_search size=10}}
						{{mb_include_script module=dPcompteRendu script=aideSaisie}}
			      <script type="text/javascript">
			      	Main.add(function() {
				        new AideSaisie.AutoComplete("editAntFrm" , "rques", "type", "_search", "CAntecedent", "{{$userSel->_id}}");
			      	} );
			      </script>
			    </td>
          <td rowspan="3">
            <textarea name="rques" onblur="this.form.onsubmit();"></textarea>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$antecedent field="type"}}</th>
          <td>
            {{mb_field object=$antecedent field="type" defaultOption="&mdash; Aucun" onchange="putHelperContent(this,'rques')"}}
          </td>
        </tr>

        <tr>
          <td class="button" colspan="2">
            <button class="tick" type="button">
              {{tr}}Add{{/tr}} un antécédent
            </button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />
      
			<!-- Traitements -->
      <form name="editTrmtFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitTraitement(this);">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->patient_id}}" />
      
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_id}}" />
      {{/if}}
      
      <table class="form">
        <tr>
          <th>
            <input type="checkbox" name="_datetrmt" onclick="dateTrmt()" />
            {{mb_label object=$traitement field="debut"}}
          </th>
          <td class="date">
            <div id="editTrmtFrm_debut_da"></div>
            <input type="hidden" name="debut" class="{{$traitement->_props.debut}}" value="" />
            <img id="editTrmtFrm_debut_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début" style="display:none;" />
          </td>
          <td>
            {{mb_label object=$traitement field="traitement"}}
            <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$traitement->_aides.traitement.no_enum}}
            </select>
            <input type="hidden" name="_hidden_traitement" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTraitement', this.form._hidden_traitement, 'traitement')">
              Nouveau
            </button>
          </td>
        </tr>
        <tr>
          <th>
            <input type="checkbox" name="_en_cours" disabled="disabled" onclick="dateFinTrmt()" />
            {{mb_label object=$traitement field="fin"}}
          </th>
          <td class="date">
            <div id="editTrmtFrm_fin_da"></div>
            <input type="hidden" name="fin" class="{{$traitement->_props.fin}}" value="" />
            <img id="editTrmtFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin" style="display:none;" />
          </td>
          <td rowspan="3">
            <textarea name="traitement" onblur="this.form.onsubmit()"></textarea>
          </td>
        </tr>
        
        <tr>
			    <!-- Auto-completion -->
			    <th style="width: 70px;">{{mb_label object=$traitement field=_search}}</th>
			    <td style="width: 100px;">
			      {{mb_field object=$traitement field=_search size=10}}
						{{mb_include_script module=dPcompteRendu script=aideSaisie}}
			      <script type="text/javascript">
			      	Main.add(function() {
				        new AideSaisie.AutoComplete("editTrmtFrm" , "traitement", null, "_search", "CTraitement", "{{$userSel->_id}}");
			      	} );
			      </script>
			    </td>
        </tr>

        <tr>
          <td class="button" colspan="2">
            <button class="tick" type="button">
              {{tr}}Add{{/tr}} un traitement
            </button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />
      
      <form name="addDiagFrm" action="?m=dPcabinet" method="post">
        <strong>Ajouter un diagnostic</strong>
        <input type="hidden" name="chir" value="{{$userSel->_id}}" />
        <button class="search" type="button" onclick="CIM10Selector.init()">Chercher un diagnostic</button>
        <input type="text" name="code_diag" size="5"/>
        <button class="tick notext" type="button" onclick="reloadCim10(code_diag.value)">Valider</button>
        <script type="text/javascript">   
          CIM10Selector.init = function(){
            this.sForm = "addDiagFrm";
            this.sView = "code_diag";
            this.sChir = "chir";
            this.options.mode = "favoris";
            this.pop();
          }
        </script> 
      </form>
      
      <table style="width: 100%">
      {{foreach from=$patient->_static_cim10 key=cat item=curr_cat}}
        <tr id="category{{$cat}}-trigger">
          <td>{{$cat}}</td>
        </tr>
        <tbody id="category{{$cat}}">
          <tr class="script"><td><script type="text/javascript">new PairEffect("category{{$cat}}");</script></td></tr>
          {{foreach from=$curr_cat item=curr_code key="key"}}
          <tr>
            <td class="text">
            <form name="code_finder-{{$curr_code->sid}}" action="?">
              <button class="tick notext" type="button" onclick="oCimField.add('{{$curr_code->code}}'); if(document.addOpFrm &amp;&amp; document.addOpFrm.operation_id.value != '') { {{if $_is_anesth}}oCimAnesthField.add('{{$curr_code->code}}');{{/if}} }">
                Ajouter
              </button>
              
              <input type="hidden" name="codeCim" value="{{$curr_code->code}}" />
              <button class="down notext" type="button" onclick="CIM10Selector.initfind{{$curr_code->sid}}()">
                Parcourir
              </button>
              <script type="text/javascript">   
                CIM10Selector.initfind{{$curr_code->sid}} = function(){
                  this.sForm = "code_finder-{{$curr_code->sid}}";
                  this.sCode = "codeCim";
                  this.find();
                }
              </script> 
              {{$curr_code->code}}: {{$curr_code->libelle}}
              </form>
            </td>
          </tr>
           {{/foreach}}
        </tbody>
      {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="category">
            Dossier patient
          </th>
        </tr>
        <tr>
          <td class="text" id="listAnt">       
          </td>
        </tr> 
        {{if $_is_anesth}}
        <tr>
          <th class="category">
            Eléments significatifs pour le séjour
          </th>
        </tr>
        <tr>
          <td class="text" id="listAntCAnesth">
          </td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>