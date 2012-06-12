{{mb_script module="dPplanningOp" script="cim10_selector"}}

{{if "dPmedicament"|module_active}}
{{mb_script module="dPmedicament" script="medicament_selector"}}
{{/if}}

<script type="text/javascript">

var cim10url = new Url;

reloadCim10 = function(sCode){
  var oForm = getForm("addDiagFrm");
  
  oCimField.add(sCode);
 
  {{if $_is_anesth}}
  if(DossierMedical.sejour_id){
    oCimAnesthField.add(sCode);
  }
  {{/if}}
  $V(oForm.code_diag, '');
  $V(oForm.keywords_code, '');
}

updateTokenCim10 = function() {
  var oForm = getForm("editDiagFrm");
  onSubmitFormAjax(oForm, { onComplete : DossierMedical.reloadDossierPatient });
}

updateTokenCim10Anesth = function(){
  var oForm = getForm("editDiagAnesthFrm");
  onSubmitFormAjax(oForm, { onComplete : DossierMedical.reloadDossierSejour });
}

onSubmitAnt = function (form) {
  var rques = $(form.rques);
  if (!rques.present()) {
    return false;
  }

  onSubmitFormAjax(form, { onComplete : DossierMedical.reloadDossiersMedicaux } );

  rques.clear().focus();

  return false;
}

onSubmitTraitement = function (form) {
  var trait = $(form.traitement);
  if (!trait.present()) {
    return false;
  }
  
  onSubmitFormAjax(form, {
    onComplete : DossierMedical.reloadDossiersMedicaux 
  } );
  
  trait.clear().focus();

  return false;
}

easyMode = function() {
  var url = new Url("dPcabinet", "vw_ant_easymode");
  url.addParam("patient_id", "{{$patient->_id}}");
  {{if isset($consult|smarty:nodefaults)}}
    url.addParam("consult_id", "{{$consult->_id}}");
  {{/if}}
  url.pop(900, 600, "Mode grille");
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
      $V(document.editTrmtFrm._sejour_id, sejour_id, false);
    }
    if(document.editAntFrm){
      $V(document.editAntFrm._sejour_id, sejour_id, false);
    }
  },
  
  reloadDossierPatient: function() {
    var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents");
    antUrl.addParam("patient_id", "{{$patient->_id}}");
    antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
    {{if $_is_anesth}}
      antUrl.addParam("sejour_id", DossierMedical.sejour_id);
    {{/if}}
    antUrl.requestUpdate('listAnt');
  },
  reloadDossierSejour: function(){
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents_anesth");  
    antUrl.addParam("sejour_id", DossierMedical.sejour_id);
    antUrl.requestUpdate('listAntCAnesth');
  },
  reloadDossiersMedicaux: function(){
    DossierMedical.reloadDossierPatient();
    {{if $_is_anesth || $sejour_id}}
    DossierMedical.reloadDossierSejour();
    {{/if}}
  }
}
 
refreshAddPoso = function(code_cip){
  var url = new Url("dPprescription", "httpreq_vw_select_poso");
  url.addParam("code_cip", code_cip);
  url.requestUpdate("addPosoLine");
}

Main.add(function () {
  DossierMedical.reloadDossiersMedicaux();
  
  if($('tab_traitements_perso')){
    Control.Tabs.create('tab_traitements_perso', false);
  }
});

</script>

<table class="main">
  {{mb_default var=show_header value=0}}
  {{if $show_header}} 
    <tr>
      <th class="title" colspan="2">
        <a style="float: left" href="?m=patients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
          {{mb_include module=patients template=inc_vw_photo_identite size=42}}
        </a>
       
        <h2 style="color: #fff; font-weight: bold;">
          {{$patient}}
          {{if isset($sejour|smarty:nodefaults)}}
          <span style="font-size: 0.7em;"> - {{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
          {{/if}}
        </h2> 
      </th>
    </tr>   
  {{/if}}   
  
  <tr>
    <td class="halfPane">
      
<table class="form">
  <tr>
    <td class="button">
      <button class="edit" type="button" onclick="easyMode();">Mode grille</button>
    </td>
  </tr>
  <tr>
    <td>
      <fieldset>
        <legend>Antécédents et allergies</legend>

        <form name="editAntFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitAnt(this);">
      
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_antecedent_aed" />
        <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
      
        <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
        
        {{if $sejour_id}}
        <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
        <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
        {{/if}}
        
          <table class="layout main">
            <tr>
              {{if $app->user_prefs.showDatesAntecedents}}
                <th style="height: 1%">{{mb_label object=$antecedent field=date}}</th>
                <td>{{mb_field object=$antecedent field=date form=editAntFrm register=true}}</td>
              {{else}}
                <td colspan="2" />
              {{/if}}
              <td rowspan="3" style="width: 100%">
                {{mb_field object=$antecedent field="rques" rows="4" form="editAntFrm"
                  aidesaisie="filterWithDependFields: false, validateOnBlur: 0"}}
              </td>
            </tr>
    
            <tr>
              <th style="height: 100%">{{mb_label object=$antecedent field="type"}}</th>
              <td>{{mb_field object=$antecedent field="type" emptyLabel="None" alphabet="1" style="width: 9em;" onchange=""}}</td>
            </tr>
    
            <tr>
              <th>{{mb_label object=$antecedent field="appareil"}}</th>
              <td>{{mb_field object=$antecedent field="appareil" emptyLabel="None" alphabet="1" style="width: 9em;"}}</td>
            </tr>
            
            <tr>
              <td class="button" colspan="3">
                <button class="tick" type="button" onclick="this.form.onsubmit();">
                  {{tr}}Add{{/tr}} l'antécédent
                </button>
              </td>
            </tr>
          </table>
          
        </form>
      
      </fieldset>
      
      {{if $isPrescriptionInstalled || $conf.dPpatients.CTraitement.enabled}}
      <fieldset>
        <legend>Traitements personnels</legend>
        <table class="layout main">
          <tr>
            <td class="text">
              <ul id="tab_traitements_perso" class="control_tabs small">
                {{if $isPrescriptionInstalled}}
                  <li><a href="#tp_base_med">Base de données de médicaments</a></li>
                {{/if}}
                {{if $conf.dPpatients.CTraitement.enabled}}
                  <li><a href="#tp_texte_simple">Texte simple</a></li>
                {{/if}}
              </ul>
              <hr class="control_tabs" /> 
            </td>
          </tr>
          {{/if}}
          
          {{if $isPrescriptionInstalled}}
          <tr id="tp_base_med">
            <td class="text">
              <!-- Formulaire d'ajout de traitements -->
              <form name="editLineTP" action="?m=dPcabinet" method="post">
                <input type="hidden" name="m" value="dPprescription" />
                <input type="hidden" name="dosql" value="do_add_line_tp_aed" />
                <input type="hidden" name="code_cip" value="" onchange="refreshAddPoso(this.value);"/>
                <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
                <input type="hidden" name="praticien_id" value="{{$userSel->_id}}" />
                
                <table class="layout">
                  <col style="width: 70px;" />
                  <col class="narrow" />
                
                  <tr>
                    <th>Recherche</th>
                    <td>
                      <div class="dropdown">
                        <input type="text" name="produit" value="" size="12" class="autocomplete" />
                        <div style="display:none; width: 350px;" class="autocomplete" id="_produit_auto_complete"></div>
                      </div>
                      
                      <button type="button" class="search notext" onclick="MedSelector.init('produit');"></button>
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
                    <td>
                      <strong><div id="_libelle"></div></strong>
                    </td>
                  </tr>
                  
                  <tr>
                    {{if $app->user_prefs.showDatesAntecedents}}
                    <th>{{mb_label object=$line field="debut"}}</th>
                    <td>{{mb_field object=$line field="debut" register=true form=editLineTP}}</td>
                    {{else}}
                    <td colspan="2"></td>
                    {{/if}}
                    <td rowspan="2" id="addPosoLine"></td>
                  </tr>
                  
                  {{if $app->user_prefs.showDatesAntecedents}}
                  <tr>
                    <th>{{mb_label object=$line field="fin"}}</th>
                    <td>{{mb_field object=$line field="fin" register=true form=editLineTP}}</td>
                  </tr>
                  {{/if}}
                  
                  <tr>
                    <th>{{mb_label object=$line field="commentaire"}}</th>
                    <td>{{mb_field object=$line field="commentaire" size=20 form=editLineTP}}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="button">
                      <button id="button_submit_traitement" class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { 
                        onComplete: function(){
                          DossierMedical.reloadDossiersMedicaux();
                          resetEditLineTP();
                          resetFormTP();
                        }
                       } ); this.form.produit.focus();">
                        {{tr}}Add{{/tr}} le traitement
                      </button>
                    </td>
                  </tr>
                </table>
              </form>
            </td>
          </tr>
          {{/if}}
              
          <!-- Traitements -->
          {{if $conf.dPpatients.CTraitement.enabled}}
          <tr id="tp_texte_simple">
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
              
              <table class="layout">
                 
                <tr>
                  {{if $app->user_prefs.showDatesAntecedents}}
                  <th style="height: 100%;">{{mb_label object=$traitement field=debut}}</th>
                  <td>{{mb_field object=$traitement field=debut form=editTrmtFrm register=true}}</td>
                  {{else}}
                  <td colspan="2"></td>
                  {{/if}}
                  <td rowspan="2" style="width: 100%">
                    {{mb_field object=$traitement field=traitement rows=4 form=editTrmtFrm
                      aidesaisie="validateOnBlur: 0"}}
                  </td>
                </tr>
                <tr>
                  {{if $app->user_prefs.showDatesAntecedents}}
                  <th>{{mb_label object=$traitement field=fin}}</th>
                  <td>{{mb_field object=$traitement field=fin form=editTrmtFrm register=true}}</td>
                  {{else}}
                  <td colspan="2"></td>
                  {{/if}}
                </tr>
        
                <tr>
                  <td class="button" colspan="3">
                    <button class="tick" type="button" onclick="this.form.onsubmit()">
                      {{tr}}Add{{/tr}} le traitement
                    </button>
                  </td>
                </tr>
              </table>
              </form>
            </td>
          </tr>
          {{/if}}
        </table>
      </fieldset>
      <fieldset>
        <legend>Base de données CIM</legend>
        {{main}}
          var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
          url.autoComplete(getForm("addDiagFrm").keywords_code, '', {
            minChars: 1,
            dropdown: true,
            width: "250px",
            select: "code",
            afterUpdateElement: function(oHidden) {
              oForm = getForm("addDiagFrm");
              $V(oForm.code_diag, oHidden.value);
              reloadCim10($V(oForm.code_diag));
            }
          });
        {{/main}}
        <form name="addDiagFrm" action="?m=dPcabinet" method="post" onsubmit="return false;">
          <strong>Ajouter un diagnostic</strong>
          <input type="hidden" name="chir" value="{{$userSel->_id}}" />
          <input type="text" name="keywords_code" class="autocomplete str code cim10" value="" size="10"/>
          <input type="hidden" name="code_diag" onchange="$V(this.form.keywords_code, this.value)"/>
          <button class="search" type="button" onclick="CIM10Selector.init()">{{tr}}Search{{/tr}}</button>
          <button class="tick notext" type="button" onclick="reloadCim10(this.form.code_diag.value)">{{tr}}Validate{{/tr}}</button>
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
        
      </fieldset>
    </td>
  </tr>
</table>

    </td>
    <td class="halfPane">
      
<table class="form">
  <tr>
    <th class="category">Dossier patient</th>
  </tr>
  <tr>
    <td class="text" id="listAnt"></td>
  </tr> 
  {{if $_is_anesth || $sejour_id}}
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

{{if $isPrescriptionInstalled}}
<script type="text/javascript">
var oFormTP = getForm("editLineTP");

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicamentTP = function(selected) {
  // Submit du formulaire avant de faire le selection d'un nouveau produit
  if(oFormTP.code_cip.value){
    submitFormAjax(oFormTP, "systemMsg", { onComplete: function() { 
      updateTP(selected);
      DossierMedical.reloadDossiersMedicaux();
    } } );
  } else {
    updateTP(selected);
  }
}
  
updateTP = function(selected){
  resetEditLineTP();
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();
  $V(oFormTP.code_cip, dn[0].innerHTML);
  $("_libelle").insert("<button type='button' class='cancel notext' onclick='resetEditLineTP(); resetFormTP();'></button><a href=\"#nothing\" onclick=\"Prescription.viewProduit('','"+dn[1].innerHTML+"','"+dn[2].innerHTML+"')\">"+dn[3].innerHTML.stripTags()+"</a>");
  $V(oFormTP.produit, '');
  $('button_submit_traitement').focus();
}  

// Autocomplete des medicaments
var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto.autoComplete(getForm('editLineTP').produit, "_produit_auto_complete", {
  minChars: 3,
  updateElement: updateFieldsMedicamentTP, 
  callback: function(input, queryString){
    return (queryString + "&produit_max=40"); 
  }
} );

resetEditLineTP = function(){
  $("_libelle").update("");
  oFormTP.code_cip.value = '';
}

resetFormTP = function(){
  $V(oFormTP.commentaire, '');
  $V(oFormTP.token_poso, '');
  $('addPosoLine').update('');                   
}
</script>
{{/if}}