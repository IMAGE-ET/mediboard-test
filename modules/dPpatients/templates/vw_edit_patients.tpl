<!-- $Id$ -->

{{mb_script module="dPpatients"    script="autocomplete"}}
{{mb_script module="dPpatients"    script="siblings_checker"}}
{{mb_script module="dPpatients"    script="patient"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPfiles"       script="files"}}
{{mb_script module="dPcabinet"     script="file"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{if $patient->_id}}
  {{mb_include module="dPfiles" template="yoplet_uploader" object=$patient}}
  {{mb_script module="dPpatients"  script="correspondant"}}
{{/if}}

{{assign var=modFSE value="fse"|module_active}}
{{assign var=patient_id value=$patient->_id}}

{{if $app->user_prefs.VitaleVision}}
  {{mb_include template=inc_vitalevision}}
  
	<script type="text/javascript">
		var lireVitale = VitaleVision.read;
	</script>
{{else}}
	<script type="text/javascript">
		var urlFSE = new Url;
	  urlFSE.addParam("m", "dPpatients");
	  urlFSE.addParam("{{$actionType}}",  "vw_edit_patients");
	  urlFSE.addParam("dialog",  "{{$dialog}}");
	  urlFSE.addParam("useVitale", 1);
	</script>
{{/if}}

<script type="text/javascript">

function copyAssureValues(element) {
	// Hack pour gérer les form fields
	var sPrefix = element.name[0] == "_" ? "_assure" : "assure_";
  eOther = element.form[sPrefix + element.name];
  
  // Copy value
  $V(eOther, $V(element));
  
  // Radio buttons seem to be null, et valuable with $V 
  if (element.type != 'radio') {
    eOther.fire("mask:check");
  }
}

function copyIdentiteAssureValues(element) {
	if (element.form.qual_beneficiaire.value == "0") {
		copyAssureValues(element);
	}
}

function delAssureValues() {
	var form = getForm("editFrm");
  $V(form.assure_nom            , "");
  $V(form.assure_prenom         , "");
  $V(form.assure_prenom_2       , "");
  $V(form.assure_prenom_3       , "");
  $V(form.assure_prenom_4       , "");
  $V(form.assure_nom_jeune_fille, "");
  $V(form.assure_naissance      , "");
  $V(form.assure_sexe           , "");
  $V(form.assure_cp_naissance  , "");
  $V(form._assure_pays_naissance_insee, "");
  $V(form.assure_lieu_naissance, "");
	$V(form.assure_profession    , "");
}

function copieAssureValues() {
	var form = getForm("editFrm");
  $V(form.assure_nom            , $V(form.nom));
  $V(form.assure_prenom         , $V(form.prenom));
  $V(form.assure_prenom_2       , $V(form.prenom_2));
  $V(form.assure_prenom_3       , $V(form.prenom_3));
  $V(form.assure_prenom_4       , $V(form.prenom_4));
  $V(form.assure_nom_jeune_fille, $V(form.nom_jeune_fille));
  $V(form.assure_naissance      , $V(form.naissance));
  $V(form.assure_sexe           , $V(form.sexe));
  $V(form.assure_cp_naissance  , $V(form.cp_naissance));
  $V(form._assure_pays_naissance_insee, $V(form._pays_naissance_insee));
  $V(form.assure_lieu_naissance, $V(form.lieu_naissance));
  $V(form.assure_profession    , $V(form.profession));
}

function confirmCreation(oForm){
  if (!checkForm(oForm)) {
    return false;
  }

  SiblingsChecker.submit = true;
  SiblingsChecker.request(oForm);
  return false;
}

function reloadListFileEditPatient(sAction, category_id){
  if(sAction == "delete" && file_preview == file_deleted){
    ZoomAjax("","","","", 0);
  }
  var url = new Url("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", document.FrmClass.selKey.value);
  url.addParam("selClass", document.FrmClass.selClass.value);  
  url.addParam("typeVue", document.FrmClass.typeVue.value);
  if (category_id != undefined) {
    url.addParam("category_id", category_id);
    if (category_id == "") category_id = 0;
    url.addParam("category_id", category_id);
    url.requestUpdate('Category-' + category_id);
  }
  else {
    url.requestUpdate('listView');
  }
}

function mapIdCorres(id, object) {
  var oForm = getForm("editCorrespondant_"+object.relation);
  if (!$V(oForm.correspondant_patient_id)) {
    $V(oForm.correspondant_patient_id, id);
  }
}

var tabs;
Main.add(function () {
  initPaysField("editFrm", "_pays_naissance_insee", "profession");
  initPaysField("editFrm", "pays", "tel");
  
  InseeFields.initCPVille("editFrm", "cp", "ville","pays");
  InseeFields.initCPVille("editFrm", "cp_naissance", "lieu_naissance","_pays_naissance_insee");
  InseeFields.initCPVille("editFrm", "assure_cp", "assure_ville","assure_pays_insee");
  InseeFields.initCPVille("editFrm", "assure_cp_naissance", "assure_lieu_naissance","_assure_pays_naissance_insee");

  InseeFields.initCSP("editFrm", "_csp_view");
	
  initPaysField("editFrm", "_assure_pays_naissance_insee", "assure_profession");
  initPaysField("editFrm", "assure_pays", "assure_tel");

  tabs = new Control.Tabs('tab-patient');

  {{if $patient->_id}}
    setObject( {
      objClass: '{{$patient->_class}}', 
      keywords: '', 
      id: '{{$patient->_id}}',
      view: '{{$patient->_view|smarty:nodefaults|JSAttribute}}' });
  {{/if}}

  {{if $useVitale && $app->user_prefs.VitaleVision}}
  lireVitale.delay(1); // 1 second
  {{/if}}
});

</script>

<form name="delete-photo-identite-form" method="post" action="?">
  <input type="hidden" name="m" value="dPfiles" />
  <input type="hidden" name="file_id" value="" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="dosql" value="do_file_aed" />
</form>

<form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="reloadListFileEditPatient('load'); return false;">
  <input type="hidden" name="selKey"   value="" />
  <input type="hidden" name="selClass" value="" />
  <input type="hidden" name="selView"  value="" />
  <input type="hidden" name="keywords" value="" />
  <input type="hidden" name="file_id"  value="" />
  <input type="hidden" name="typeVue"  value="1" />
</form>

{{if $patient->_id}}
<a class="button new" href="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;patient_id=0">
  {{tr}}CPatient-title-create{{/tr}}
</a>
{{/if}}

<div id="modal-beneficiaire" style="display:none; text-align:center;">
  <p id="msg-multiple-benef">
    Cette carte vitale semble contenir plusieurs bénéficiaires, merci de sélectionner la personne voulue :
  </p>
  <p id="msg-confirm-benef" style="display: none;">
    Vous êtes sur le point de remplacer les données du formulaire par les données de la carte. <br />
    Veuillez vérifier le nom du bénéficiaire :
  </p>
	<p id="benef-nom">
	  <select id="modal-beneficiaire-select"></select>
    <span></span>
  </p>
  <div>
  	<button type="button" class="tick" onclick="VitaleVision.fillForm(getForm('editFrm'), $V($('modal-beneficiaire-select'))); VitaleVision.modalWindow.close();">{{tr}}Choose{{/tr}}</button>
	  <button type="button" class="cancel" onclick="VitaleVision.modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
  </div>
</div>

<table class="main">
  <tr>
  {{if $patient->_id}}
    <th class="title modify" colspan="5">
      {{if $app->user_prefs.VitaleVision}}
        <button class="search singleclick" type="button" onclick="lireVitale();" style="float: left;">
         Lire Vitale
        </button>
      {{elseif $modFSE && $modFSE->canRead()}}
        {{mb_include module=fse template=inc_button_vitale}}
      {{/if}}
    
			{{if $patient->date_lecture_vitale}}
      <div style="float: right;">
	      <img src="images/icons/carte_vitale.png" title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$patient field="date_lecture_vitale" format=relative}}" />
      </div>
      {{/if}}
      
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history    object=$patient}}
      Modification du dossier de {{$patient}} 
      {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
      {{if $patient->_bind_vitale}}{{tr}}UseVitale{{/tr}}{{/if}}
    </th>
  {{else}}
    <th class="title" colspan="5">
      {{if $app->user_prefs.VitaleVision}}
        <button class="search singleclick" type="button" onclick="lireVitale();" style="float: left;">
         Lire Vitale
        </button>
      {{elseif $modFSE && $modFSE->canRead()}}
        {{mb_include module=fse template=inc_button_vitale}}
      {{/if}}
			{{tr}}Create{{/tr}}
      {{if $patient->_bind_vitale}}{{tr}}UseVitale{{/tr}}{{/if}}
    </th>
  {{/if}}
  </tr>
  {{mb_ternary var=x test=$patient->medecin_traitant value=1 other=0}}
  {{math equation="$x+y" y=$patient->_ref_medecins_correspondants|@count assign=count_correspondants}}
  <tr>
    <td colspan="5">
      <ul id="tab-patient" class="control_tabs">
        <li><a href="#identite">Patient</a></li>
        <li><a href="#medecins" {{if !$count_correspondants}}class="empty"{{/if}}>Correspondants médicaux <small>({{$count_correspondants}})</small></a></li>
        <li><a href="#correspondance" {{if !$patient->_ref_correspondants_patient|@count}}class="empty"{{/if}}>Correspondance <small>({{$patient->_ref_correspondants_patient|@count}})</small></a></li>
        <li><a href="#assure">Assuré social</a></li>
        <li><a href="#beneficiaire">Bénéficiaire de soins</a></li>
        <li><a href="#listView" {{if $patient->_nb_files_docs == 0}}class="empty"{{/if}}
          {{if $patient->_id}}onmousedown="reloadListFileEditPatient('load')"{{/if}}>Documents ({{$patient->_nb_files_docs}})</a></li>
      </ul>
      <hr class="control_tabs" />
      
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return confirmCreation(this)">
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="_purge" value="0" />
        {{mb_key object=$patient}}

        {{if $patient->_bind_vitale}}
        <input type="hidden" name="_bind_vitale" value="1" />
        {{/if}}
        
        <button type="submit" style="display: none;">&nbsp;</button>
                
        {{if !$patient->_id}}
				{{mb_field object=$patient field="medecin_traitant" hidden=1}}
				{{/if}}
				
        {{if $dialog}}
        <input type="hidden" name="dialog" value="{{$dialog}}" />
        {{/if}}
        
        <div id="identite">
          {{mb_include template=inc_acc/inc_acc_identite}}
        </div>
        <div id="assure" style="display: none;">
          {{mb_include template=inc_acc/inc_acc_assure}}
        </div>
        <div id="beneficiaire" style="display: none;">
          {{mb_include template=inc_acc/inc_acc_beneficiaire}}   
        </div>
      </form>
      <div id="correspondance" style="display: none;">
        {{mb_include template=inc_acc/inc_acc_corresp}}
      </div>
      <div id="medecins" style="display: none;">
        {{mb_include template=inc_acc/inc_acc_medecins}}
      </div>
      <div id="listView" style="display: none;">
        <div class="big-info">{{tr}}CPatient.save_for_files{{/tr}}</div>
      </div>
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="5" style="text-align:center;" id="button">
      <div id="divSiblings" style="display:none;"></div>
      {{if $patient->_id}}
        <button tabindex="400" id="submit-patient" type="submit" class="submit" onclick="return document.editFrm.onsubmit();">
          {{tr}}Save{{/tr}}
          {{if $patient->_bind_vitale}}
          &amp; {{tr}}BindVitale{{/tr}}
          {{/if}}
        </button>
        
        <button type="button" class="print" onclick="Patient.print('{{$patient->_id}}')">
          {{tr}}Print{{/tr}}
        </button>
        
        <button type="button" class="trash" onclick="confirmDeletion(document.editFrm,{typeName:'le patient',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>

        {{if $can->admin}}        
        <script type="text/javascript">
          function confirmPurge() {
          	var oForm = document.editFrm;
          	if (confirm("ATTENTION : Vous êtes sur le point de purger le dossier de ce patient")) {
          	  oForm._purge.value = "1";
	          	confirmDeletion(oForm,	{
	          		typeName:'le patient',
	          		objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'
	          	} );
	          }
	        }
        </script>
			
        <button type="button" class="cancel" onclick="confirmPurge();">
          {{tr}}Purge{{/tr}}
        </button>
        {{/if}} 

      {{else}}
        <button tabindex="400" id="submit-patient" type="submit" class="submit" onclick="return document.editFrm.onsubmit();">
          {{tr}}Create{{/tr}}
          {{if $patient->_bind_vitale}}
          &amp; {{tr}}BindVitale{{/tr}}
          {{/if}}
        </button>
      {{/if}}
    </td>
  </tr>
</table>
