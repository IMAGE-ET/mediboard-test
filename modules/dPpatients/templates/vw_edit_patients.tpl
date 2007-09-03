<!-- $Id$ -->

{{mb_include_script module="dPpatients" script="autocomplete"}}


<script type="text/javascript">

var Intermax = {
  currentFunction : "unknown",
  newLine : {{$newLine|json}},
  
  bindContent: function(sContent) {
    var aContentLines = sContent.split(this.newLine);
    var oContent = {}
    var sCurrentCategory = "";
    aContentLines.each(function(line) {
      
      // Create new category
      if (aMatches = line.match(/\[(\w*)\]/)) {
        sCurrentCategory = aMatches[1];
        oContent[sCurrentCategory] = {}
      }
      
      // Fill a key-value pair in current category
      if (aMatches = line.match(/(\w*)=(.*)/)) {
        sKey = aMatches[1];
        sValue = aMatches[2];
        oContent[sCurrentCategory][sKey] = sValue;
      }
      
    } );
    
	return oContent;
  },
  
  makeContent: function(oContent) {
    var sContent = '';
    $H(oContent).each(function(pair) {
      sContent += printf ("[%s]%s", pair.key, Intermax.newLine);
      $H(pair.value).each( function(pair) {
        sContent += printf ("%s = %s%s", pair.key, pair.value, Intermax.newLine);
      } );
    } );
    
    return sContent;
  },

  trigger: function(sFunction) {
    this.currentFunction = sFunction;
    
    var oContent = {
      FONCTION: {
        NOM: sFunction
      },
      PARAM: {
        AFFICHAGE: 1
      }
    }
    
    var sContent = this.makeContent(oContent);
    document.intermaxTrigger.performWrite(sContent);
  },
  
  result: function() {
    document.intermaxResult.performRead();
    setTimeout(Intermax.handleContent.bind(Intermax), 100);
    
  },
  
  handleContent: function() {
    if (oAppletContent = document.intermaxResult.getContent()) {
      // Append with empty Js String will cast a Java string to a Js string
      var sContent = oAppletContent + ""; 
      oContent = this.bindContent(sContent);
      this.createResultMessages(oContent);
      var fResultHandler = this.ResultHandler[oContent.FONCTION.NOM] || Prototype.emptyFunction;
      fResultHandler(oContent);
    }
  },
  
  createResultMessages: function(oContent) {
  },
  
  ResultHandler : {
    "Lire Vitale" : function (oContent) {
      oVitale = oContent.VITALE;
      
      url = new Url;
      url.setModuleTab("dPpatients", "vw_edit_patients");

      url.addParam("useVitale", "1");
      url.addParam("vitale[nom]", oVitale.VIT_NOM);
      url.addParam("vitale[prenom]", oVitale.VIT_PRENOM);

      var sAdresse = [
        oVitale.VIT_ADRESSE_1, 
        oVitale.VIT_ADRESSE_2,
        oVitale.VIT_ADRESSE_1, 
        oVitale.VIT_ADRESSE_2,
        oVitale.VIT_ADRESSE_1].without("").join("\n");
      url.addParam("vitale[adresse]", sAdresse);
      
      var sNaissance = Date.fromLocaleDate(oVitale.VIT_DATE_NAISSANCE).toDATE();
      url.addParam("vitale[naissance]", sNaissance);
      
      var sMatricule = oVitale.VIT_NUMERO_SS_INDIV ?
        oVitale.VIT_NUMERO_SS_INDIV + oVitale.VIT_CLE_SS_INDIV :
        oVitale.VIT_NUMERO_SS + oVitale.VIT_CLE_SS
      url.addParam("vitale[matricule]", sMatricule);
      url.redirect();
      
      window.setPat = function(patient_id, patient_view) {
      }
    }
  }
}

var httpreq_running = false;
function confirmCreation(oForm){
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
}

</script>


{{if $app->user_prefs.GestionFSE}}
<!-- Yoplet to trigger functions -->

<applet 
  name="intermaxTrigger"
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar" 
  width="0" 
  height="0"
>
  <param name="action" value="sleep"/>
  <param name="lineSeparator" value="{{$newLine}}"/>
  <param name="debug" value="false" />
  <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.INI" />
  <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/CALL.FLG" />
</applet>

<!-- Yoplet to read results -->
<applet 
  name="intermaxResult"
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar" 
  width="0" 
  height="0"
>
  <param name="action" value="sleep"/>
  <param name="lineSeparator" value="{{$newLine}}"/>
  <param name="debug" value="false" />
  <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.OUT" />
  <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/RETURN.FLG" />
</applet>
{{/if}}

<table class="main">
  {{if $patient->patient_id}}
  <tr>
    <td><a class="buttonnew" href="?m={{$m}}&amp;patient_id=0">Créer un nouveau patient</a></td>
  </tr>
  {{/if}}

  <tr>
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return confirmCreation(this)">
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_field object=$patient field="patient_id" hidden=1 prop=""}}
      {{if $dialog}}
      <input type="hidden" name="dialog" value="{{$dialog}}" />
      {{/if}}
      
      <table class="main">

      <tr>
      {{if $patient->patient_id}}
        <th class="title modify" colspan="5">
          {{if $app->user_prefs.GestionFSE}}
					  <button class="search" type="button" onclick="Intermax.result();" style="float: left;">Carte vitale</button>
					{{/if}}
        
          <div class="idsante400" id="CPatient-{{$patient->_id}}"></div>
              
          <a style="float:right;" href="#" onclick="view_log('CPatient',{{$patient->_id}})">
            <img src="images/icons/history.gif" alt="historique" />
          </a>
          Modification du dossier de {{$patient->_view}}
        </th>
      {{else}}
        <th class="title" colspan="5">
          {{if $app->user_prefs.GestionFSE}}
					  <button class="search" type="button" onclick="Intermax.result();" style="float: left;">Carte vitale</button>
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
          </div>
        </td>
      </tr>
      
      <tr>
        <td class="button" colspan="5" style="text-align:center;" id="button">
          <div id="divSiblings" style="display:none;"></div>
          {{if $patient->patient_id}}
            <button tabindex="400" type="submit" class="submit">Valider</button>
            <button type="button" class="print" onclick="printPatient({{$patient->patient_id}})">
              Imprimer
            </button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'le patient',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          {{else}}
            <button tabindex="400" type="submit" class="submit">Créer</button>
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
