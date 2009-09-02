{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

var cim10url = new Url;

reloadCim10 = function(sCode){
  var oForm = document.addDiagFrm;
  
  oCimField.add(sCode);
 
  {{if $_is_anesth}}
  if(DossierMedical.sejour_id){
    oCimAnesthField.add(sCode);
  }
  {{/if}}
  updateTokenCim10();
  oForm.code_diag.value="";
}

updateTokenCim10 = function() {
  var oForm = document.editDiagFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : DossierMedical.reloadDossierPatient });
}

updateTokenCim10Anesth = function(){
  var oForm = document.editDiagAnesthFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : DossierMedical.reloadDossierSejour });
}

onSubmitAnt = function (oForm) {
	if (oForm.rques.value.blank()) {
    return false;
  }

  onSubmitFormAjax(oForm, {
  	onComplete : DossierMedical.reloadDossiersMedicaux 
  } );
  
  // Nettoyage du formulaire
  oForm._hidden_rques.value = oForm.rques.value;
  oForm.rques.value = "";

  return false;
}

onSubmitTraitement = function (oForm) {
	if (oForm.traitement.value.blank()) {
    return false;
  }
  
  onSubmitFormAjax(oForm, {
  	onComplete : DossierMedical.reloadDossiersMedicaux 
  } );
  
  // Nettoyage du formulaire
  oForm._hidden_traitement.value = oForm.traitement.value;
  oForm.traitement.value = "";
  oForm._helpers_traitement.value = "";

  return false;
}

easyMode = function() {
  var width = 900;
  var height = 600;
  var url = new Url("dPcabinet", "vw_ant_easymode");
  url.addParam("user_id", "{{$userSel->_id}}");
  url.addParam("patient_id", "{{$patient->_id}}");
  url.pop(width, height, "Mode grille");
}

/**
 * Mise a jour du champ _sejour_id pour la creation d'antecedent et de traitement
 * et la widget de dossier medical
 */
DossierMedical = {
	sejour_id: '{{$sejour_id}}',
  updateSejourId : function(sejour_id) {
  	this.sejour_id = sejour_id;
  	
  	// Mise à jour des formulaire
  	if(document.editTrmtFrm){
	    document.editTrmtFrm._sejour_id.value   = sejour_id;
	  }
	  if(document.editAntFrm){
	    document.editAntFrm._sejour_id.value    = sejour_id;
	  }
  },
  
  reloadDossierPatient: function() {
	  var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents");
	  antUrl.addParam("patient_id", "{{$patient->_id}}");
	  antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
	  {{if $_is_anesth}}
	    antUrl.addParam("sejour_id", DossierMedical.sejour_id);
	  {{/if}}
	  antUrl.requestUpdate('listAnt', { waitingText : null } );
	},
	reloadDossierSejour: function(){
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents_anesth");  
	  antUrl.addParam("sejour_id", DossierMedical.sejour_id);
	  antUrl.requestUpdate('listAntCAnesth', { waitingText : null });
	},
	reloadDossiersMedicaux: function(){
	  DossierMedical.reloadDossierPatient();
    {{if $_is_anesth}}
    DossierMedical.reloadDossierSejour();
    {{/if}}
	}
}

refreshAidesAntecedents = function(){
  var url = new Url;
  var oForm = document.editAntFrm;
  url.setModuleAction("dPcompteRendu", "httpreq_vw_select_aides");
  url.addParam("object_class", "CAntecedent");
  url.addParam("depend_value_1", oForm.type.value);
  url.addParam("depend_value_2", oForm.appareil.value);
  url.addParam("user_id", "{{$userSel->_id}}")
  url.addParam("field", "rques");
  url.requestUpdate('div_helpers_rques', { waitingText: null } );
}
 
refreshAddPoso = function(code_cip){
  var url = new Url("dPprescription", "httpreq_vw_select_poso");
  url.addParam("code_cip", code_cip);
  url.requestUpdate("addPosoLine", { waitingText: null } );
}

Main.add(function () {
  DossierMedical.reloadDossiersMedicaux();
  refreshAidesAntecedents();
});

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      
<table class="form">
  <tr>
    <th class="category">
      <button style="float: left" class="edit" type="button" onclick="easyMode();">Mode grille</button>
      Antécédents
    </th>
  </tr>
  <tr>
    <td class="text">      
      <!-- Antécédents -->
      <form name="editAntFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitAnt(this);">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
      
      <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
      {{/if}}

      <table class="form">
        <tr>
          {{if $app->user_prefs.showDatesAntecedents}}
          <th>{{mb_label object=$antecedent field=date}}</th>
          <td>{{mb_field object=$antecedent field=date form=editAntFrm register=true}}</td>
          {{else}}
          <td colspan="2" />
          {{/if}}
          <th id="listAides_Antecedent_rques">
            {{mb_label object=$antecedent field="rques"}}
            <span id="div_helpers_rques">
            </span>
            <input type="hidden" name="_hidden_rques" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAntecedent', this.form._hidden_rques, 'rques', this.form.type.value, this.form.appareil.value)">
              {{tr}}New{{/tr}}
            </button>
          </th>
        </tr>

        <tr>
          <!-- Auto-completion -->
          <th style="width: 70px;">{{mb_label object=$antecedent field=_search}}</th>
          <td style="width:100px;">
            {{mb_field object=$antecedent field=_search size=10 class="autocomplete"}}
            {{mb_include_script module=dPcompteRendu script=aideSaisie}}
            <script type="text/javascript">
              Main.add(function() {
                prepareForm(document.editAntFrm);
                new AideSaisie.AutoComplete("editAntFrm" , "rques", "type", "appareil", "_search", "CAntecedent", "{{$userSel->_id}}");
              } );
            </script>
          </td>
          <td rowspan="3">
            <textarea name="rques" onblur="this.form.onsubmit();"></textarea>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$antecedent field="type"}}</th>
          <td>{{mb_field object=$antecedent field="type" defaultOption="&mdash; Aucun" alphabet="1" onchange="refreshAidesAntecedents()"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$antecedent field="appareil"}}</th>
          <td>{{mb_field object=$antecedent field="appareil" defaultOption="&mdash; Aucun" alphabet="1" onchange="refreshAidesAntecedents()"}}</td>
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
    </td>
  </tr>
  {{if $isPrescriptionInstalled}}
  <tr>
    <th class="category">
      Traitements (base de données de médicaments)
    </th>
  </tr>
  <tr>
    <td class="text">
      <!-- Formulaire d'ajout de traitements -->
      <form name="editLineTP" action="?m=dPcabinet" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_add_line_tp_aed" />
        <input type="hidden" name="code_cip" value="" onchange="refreshAddPoso(this.value);"/>
        <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="praticien_id" value="{{$userSel->_id}}" />
        
        <table class="form">
          <tr>
            <th style="width: 1%">Recherche</th>
            <td>
              <input type="text" name="produit" value="" size="12" class="autocomplete" />
              <button type="button" class="search notext" onclick="MedSelector.init('produit');"></button>
              <div style="display:none; width: 350px;" class="autocomplete" id="_produit_auto_complete"></div>
              <script type="text/javascript">
                  MedSelector.init = function(onglet){
                    this.sForm = "editLineTP";
                    this.sView = "produit";
                    this.sCode = "code_cip";
                    this.sSearch = document.editLineTP.produit.value;
                    this.selfClose = true;
                    this.sOnglet = onglet;
                    this.pop();
                  }
                  MedSelector.doSet = function(){
                    var oForm = document[MedSelector.sForm];
                    $('_libelle').update(MedSelector.prepared.nom);
                    $V(oForm.code_cip, MedSelector.prepared.code);
                    $('button_submit_traitement').focus();
                  }
              </script>
            </td>
            <td style="width: 50%">
              <strong>
              <div id="_libelle"></div>
              </strong>
            </td>
          </tr>
          
          <tr>
            {{if $app->user_prefs.showDatesAntecedents}}
            <th>{{mb_label object=$line field="debut"}}</th>
            <td>{{mb_field object=$line field="debut" register=true form=editLineTP}}</td>
            {{else}}
            <td colspan="2" />
            {{/if}}
            <td rowspan="3" id="addPosoLine"></td>
          </tr>
          
          {{if $app->user_prefs.showDatesAntecedents}}
          <tr>
            <th>{{mb_label object=$line field="fin"}}</th>
            <td>{{mb_field object=$line field="fin" register=true form=editLineTP}}</td>
          </tr>
          {{/if}}
          
          <tr>
            <th>{{mb_label object=$line field="commentaire"}}</th>
            <td>{{mb_field object=$line field="commentaire" size=20}}</td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button id="button_submit_traitement" class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { 
                onComplete: function(){
                  DossierMedical.reloadDossiersMedicaux();
                  resetEditLineTP();
                  $('addPosoLine').update('');
                  this.form.token_poso.value = '';
                  this.form.commentaire.value = '';
                }
               } ); this.form.produit.focus();">
                {{tr}}Add{{/tr}} un traitement
              </button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  {{/if}}
      
  <!-- Traitements -->
  {{if $dPconfig.dPpatients.CTraitement.enabled}}
  <tr>
    <th class="category">
      Traitements (texte simple)
    </th>
  </tr>
  <tr>
    <td class="text">
      <form name="editTrmtFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitTraitement(this);">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
      
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
      {{/if}}
      
      <table class="form">
        <tr>
          {{if $app->user_prefs.showDatesAntecedents}}
          <th>{{mb_label object=$traitement field=debut}}</th>
          <td>{{mb_field object=$traitement field=debut form=editTrmtFrm register=true}}</td>
          {{else}}
          <td colspan="2" />
          {{/if}}
          <th>
            {{mb_label object=$traitement field="traitement"}}
            <select name="_helpers_traitement" size="1" style="width: 80px;" onchange="pasteHelperContent(this)">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{html_options options=$traitement->_aides.traitement.no_enum}}
            </select>
            <input type="hidden" name="_hidden_traitement" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTraitement', this.form._hidden_traitement, 'traitement')">
              {{tr}}New{{/tr}}
            </button>
          </th>
        </tr>
        <tr>
          {{if $app->user_prefs.showDatesAntecedents}}
          <th>{{mb_label object=$traitement field=fin}}</th>
          <td>{{mb_field object=$traitement field=fin form=editTrmtFrm register=true}}</td>
          {{else}}
          <td colspan="2" />
          {{/if}}
          <td rowspan="3">
            <textarea name="traitement" onblur="this.form.onsubmit()"></textarea>
          </td>
        </tr>
        <tr>
          <!-- Auto-completion -->
          <th style="width: 70px;">{{mb_label object=$traitement field=_search}}</th>
          <td style="width: 100px;">
            {{mb_field object=$traitement field=_search size=10 class="autocomplete"}}
            {{mb_include_script module=dPcompteRendu script=aideSaisie}}
            <script type="text/javascript">
              Main.add(function() {
                prepareForm(document.editTrmtFrm);
                new AideSaisie.AutoComplete("editTrmtFrm" , "traitement", null, null, "_search", "CTraitement", "{{$userSel->_id}}");
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
    </td>
  </tr>
  {{/if}}
      
  <!-- Diagnostics CIM -->
  <tr>
    <th class="category">
      Base de données CIM
    </th>
  </tr>
  <tr>
    <td class="text">
      <form name="addDiagFrm" action="?m=dPcabinet" method="post">
        <strong>Ajouter un diagnostic</strong>
        <input type="hidden" name="chir" value="{{$userSel->_id}}" />
        <button class="search" type="button" onclick="CIM10Selector.init()">{{tr}}Search{{/tr}}</button>
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
          <tr class="script">
            <td>
              <script type="text/javascript">new PairEffect("category{{$cat}}");</script>
            </td>
          </tr>
          {{foreach from=$curr_cat item=curr_code key="key"}}
          <tr>
            <td class="text">
            <form name="code_finder-{{$curr_code->sid}}" action="?">
              <button class="tick notext" type="button" onclick="oCimField.add('{{$curr_code->code}}'); if(DossierMedical.sejour_id != '') { {{if $_is_anesth}}oCimAnesthField.add('{{$curr_code->code}}');{{/if}} }">
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
  </tr>
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
    <td class="text" id="listAnt"></td>
  </tr> 
  {{if $_is_anesth}}
  <tr>
    <th class="category">
      Eléments significatifs pour le séjour
    </th>
  </tr>
  <tr>
    <td class="text" id="listAntCAnesth"></td>
  </tr>
  {{/if}}
</table>

    </td>
  </tr>
</table>

<script type="text/javascript">
{{if $isPrescriptionInstalled}}
  var oFormTP = getForm(editLineTP);
  
  // UpdateFields de l'autocomplete de medicaments
  updateFieldsMedicament = function(selected) {
    resetEditLineTP();
    Element.cleanWhitespace(selected);
    var dn = selected.childElements();
    $V(oFormTP.code_cip, dn[0].innerHTML);
    $("_libelle").insert("<a href=\"javascript:void(0);\" onclick=\"Prescription.viewProduit('','"+dn[1].innerHTML+"','"+dn[2].innerHTML+"')\">"+dn[3].innerHTML.stripTags()+"</a>");
    $V(oFormTP.produit, '');
    $('button_submit_traitement').focus();
  }
  
  // Autocomplete des medicaments
  var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.autoComplete("editLineTP_produit", "_produit_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsMedicament, 
    callback: 
      function(input, queryString){
        return (queryString + "&produit_max=25"); 
      }
  } );
  
  resetEditLineTP = function(){
    $("_libelle").update("");
    oFormTP.code_cip.value = '';
  }
{{/if}}
</script>