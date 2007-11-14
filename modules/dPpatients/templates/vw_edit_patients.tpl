<!-- $Id$ -->

{{mb_include_script module="dPpatients" script="autocomplete"}}

{{include file="../../dPpatients/templates/inc_intermax.tpl" debug=false}}

<script type="text/javascript">

Intermax.ResultHandler["Consulter Vitale"] =
Intermax.ResultHandler["Lire Vitale"] = function() {
  var url = new Url;
  url.setModuleTab("dPpatients", "vw_edit_patients");
  url.addParam("useVitale", 1);
  url.redirect();
}

function checkNaissance(){
  var oForm = document.editFrm;
  
  if(oForm._jour.value > 31 || oForm._mois.value > 12) {
     if(!confirm('Le date de naissance ne correspond pas à une date du calendrier, souhaitez vous la sauvegarder ?')){
       return false;
     }
   } 
   return true;
}

function checkAssureNaissance(){
  var oForm = document.editFrm;
  
  if(oForm._assure_jour.value > 31 || oForm._assure_mois.value > 12) {
     if(!confirm('Le date de naissance de l\'assuré ne correspond pas à une date du calendrier, souhaitez vous la sauvegarder ?')){
       return false;
     }
   } 
   return true;
}


var httpreq_running = false;
function confirmCreation(oForm){
  
  if(!checkNaissance()){
    return false;
  }
  
  if(!checkAssureNaissance()){
    return false;
  }
  
  if(httpreq_running) {
    return false;
  }
  if(!checkForm(oForm)){
    return false;
  }
  httpreq_running = true;
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_get_siblings");
  url.addParam("patient_id", oForm.patient_id.value);
  url.addParam("nom", oForm.nom.value);
  url.addParam("prenom", oForm.prenom.value);  
  if(oForm._annee.value!="" && oForm._mois.value!="" && oForm._jour.value!=""){
    url.addParam("naissance", oForm._annee.value + "-" + oForm._mois.value + "-" + oForm._jour.value);
  }
  url.requestUpdate('divSiblings', { evalScripts: true, waitingText: null });
  return false;
}

function printPatient(id) {
  var url = new Url();
  url.setModuleAction("dPpatients", "print_patient");
  url.addParam("patient_id", id);
  url.popup(700, 550, "Patient");
}

function pageMain() {
  initInseeFields("editFrm", "cp", "ville","pays");
  initInseeFields("editFrm", "prevenir_cp", "prevenir_ville", "_tel31");
  initInseeFields("editFrm", "employeur_cp", "employeur_ville", "_tel41");
  initPaysField("editFrm", "pays","_tel1");
  regFieldCalendar("editFrm", "cmu");
  regFieldCalendar("editFrm", "fin_validite_vitale");
  initInseeFields("editFrm", "assure_cp", "assure_ville","assure_pays");
  initPaysField("editFrm", "assure_pays","_assure_tel1");
}

</script>

<table class="main">
  {{if $patient->_id}}
  <tr>
    <td><a class="buttonnew" href="?m={{$m}}&amp;patient_id=0">Créer un nouveau patient</a></td>
  </tr>
  {{/if}}

  <tr>
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return confirmCreation(this)">
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="del" value="0" />
      {{if $patient->_bind_vitale}}
      <input type="hidden" name="_bind_vitale" value="1" />
      {{/if}}
      
      {{mb_field object=$patient field="patient_id" hidden=1 prop=""}}
      {{if $dialog}}
      <input type="hidden" name="dialog" value="{{$dialog}}" />
      {{/if}}
      
      <table class="main">

      <tr>
      {{if $patient->_id}}
        <th class="title modify" colspan="5">
          {{if $app->user_prefs.GestionFSE}}
			      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');" style="float: left;">
			        Lire Vitale
			      </button>
 			      {{if $patient->_idVitale}}
 			      <button class="search" type="button" onclick="Intermax.Triggers['Consulter Vitale']({{$patient->_idVitale}});" style="float: left;">
			        Consulter Vitale
			      </button>
			      {{/if}}
			      <button class="tick" type="button" onclick="Intermax.result();" style="float: left;">
			        Résultat Vitale
			      </button>
					{{/if}}
        
          <div class="idsante400" id="CPatient-{{$patient->_id}}"></div>
              
          <a style="float:right;" href="#" onclick="view_log('CPatient',{{$patient->_id}})">
            <img src="images/icons/history.gif" alt="historique" />
          </a>
          Modification du dossier de {{$patient->_view}} {{if $patient->_IPP}} [{{$patient->_IPP}}] {{/if}}
        </th>
      {{else}}
        <th class="title" colspan="5">
          {{if $app->user_prefs.GestionFSE}}
			      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');" style="float: left;">
			        Lire Vitale
			      </button>
			      <button class="tick" type="button" onclick="Intermax.result();" style="float: left;">
			        Résultat Vitale
			      </button>
					{{/if}}
          Création d'un dossier
        </th>
      {{/if}}
      </tr>
      
      <tr>
        <td colspan="5">
          <div class="accordionMain" id="accordionConsult">
          
            <div id="Identite">
              <div id="IdentiteHeader" class="accordionTabTitleBar">
                Identité
              </div>
              <div id="IdentiteContent"  class="accordionTabContentBox">
              {{include file="inc_acc/inc_acc_identite.tpl"}}
              </div>
            </div>
            <div id="Medical">
              <div id="MedicalHeader" class="accordionTabTitleBar">
                Médical
              </div>
              <div id="MedicalContent"  class="accordionTabContentBox">
              {{include file="inc_acc/inc_acc_medical.tpl"}}
              </div>
            </div>
            <div id="Corresp">
              <div id="CorrespHeader" class="accordionTabTitleBar">
                Correspondance
              </div>
              <div id="CorrespContent"  class="accordionTabContentBox">
              {{include file="inc_acc/inc_acc_corresp.tpl"}}
              </div>
            </div>
            <div id="Assure">
              <div id="AssureHeader" class="accordionTabTitleBar">
                Assuré social
              </div>
              <div id="AssureContent"  class="accordionTabContentBox">
              {{include file="inc_acc/inc_acc_assure.tpl"}}
              </div>
            </div>
          </div>
        </td>
      </tr>
      
      <tr>
        <td class="button" colspan="5" style="text-align:center;" id="button">
          <div id="divSiblings" style="display:none;"></div>
          {{if $patient->patient_id}}
            <button tabindex="400" type="submit" class="submit">
              {{tr}}Modify{{/tr}}
              {{if $patient->_bind_vitale}}
              &amp; {{tr}}BindVitale{{/tr}}
              {{/if}}
            </button>
            <button type="button" class="print" onclick="printPatient({{$patient->patient_id}})">
              {{tr}}Print{{/tr}}
            </button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'le patient',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button tabindex="400" type="submit" class="submit">
              {{tr}}Create{{/tr}}
              {{if $patient->_bind_vitale}}
              &amp; {{tr}}BindVitale{{/tr}}
              {{/if}}
            </button>
          {{/if}}
        </td>
      </tr>

      </table>
      </form>
    </td>
  </tr>
</table>
<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accordionConsult'), { 
  panelHeight: ViewPort.SetAccordHeight('accordionConsult', { sOtherElmt: 'button', iBottomMargin : 20 }),
  showDelay: 50, 
  showSteps: 3 
} );
</script>
