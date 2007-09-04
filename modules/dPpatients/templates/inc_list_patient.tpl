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
      url.setModuleTab("dPpatients", "vw_idx_patients");

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

      <form name="find" action="./index.php" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="4">Recherche d'un dossier patient</th>
        </tr>
  
        <tr>
          <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
          <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
          <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
          <td><input tabindex="3" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
          <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
          <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
          <td><input tabindex="4" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="jeuneFille" title="Nom de naissance">Nom de naissance</label></th>
          <td><input tabindex="2" type="text" name="jeuneFille" value="{{$jeuneFille|stripslashes}}" /></td>
          <td colspan="2"></td>
        </tr>
        
        
        <tr>
          <th colspan="2">
            <label for="check_naissance" title="Date de naissance du patient à rechercher">
              <input type="checkbox" name="check_naissance" onclick="affNaissance()" {{if $naissance == "on"}}checked="checked"{{/if}}/>
              <input type="hidden" name="naissance" {{if $naissance == "on"}}value="on"{{else}}value="off"{{/if}} />
              Date de naissance
            </label>
          </th>
          <td colspan="2">
            {{if $naissance == "on"}}
               {{html_select_date
                 time=$datePat
                 start_year=1900
                 field_order=DMY
                 day_empty="Jour"
                 month_empty="Mois"
                 year_empty="Année"
                 all_extra="style='display:inline;'"}}
                 {{else}}
               {{html_select_date
                 time=$datePat
                 start_year=1900
                 field_order=DMY
                 day_empty="Jour"
                 month_empty="Mois"
                 year_empty="Année"
                 all_extra="style='display:none;'"}}
               {{/if}}  
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="4">
            {{if $board}}
              <button class="search" type="button" onclick="updateListPatients()">Rechercher</button>
            {{else}}
              <button class="search" type="submit">Rechercher</button>
					    {{if $app->user_prefs.GestionFSE}}
					      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
					        Lire Vitale
					      </button>
					      <button class="tick" type="button" onclick="Intermax.result();">
					        Résultat Vitale
					      </button>
					    {{/if}}
            {{/if}}
          </td>
        </tr>
      </table>
      </form>

      <form name="fusion" action="index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_pat" />
      <table class="tbl">
        <tr>
          <th><button type="submit" class="search">Fusion</button></th>
          <th>Patient</th>
          <th>Date de naissance</th>
          <th>Adresse</th>
        </tr>

        {{mb_ternary var="tabPatient" test=$board 
                     value="vw_full_patients&patient_id=" 
                     other="vw_idx_patients&patient_id="}}
        
        {{foreach from=$patients item=curr_patient}}
        <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
          <td><input type="checkbox" name="fusion_{{$curr_patient->_id}}" /></td>
          <td class="text">
            <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
              {{mb_value object=$curr_patient field="_view"}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
              {{mb_value object=$curr_patient field="naissance"}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
              {{mb_value object=$curr_patient field="adresse"}}
              {{mb_value object=$curr_patient field="cp"}}
              {{mb_value object=$curr_patient field="ville"}}
            </a>
          </td>
        </tr>
        {{/foreach}}
        {{if $patientsSoundex|@count}}
        <tr>
          <th colspan="4">
            <em>Résultats proches</em>
          </th>
        </tr>
        {{/if}}
        {{foreach from=$patientsSoundex item=curr_patient}}
        <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
          <td><input type="checkbox" name="fusion_{{$curr_patient->_id}}" /></td>
          <td class="text">
            <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
              {{mb_value object=$curr_patient field="_view"}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
              {{mb_value object=$curr_patient field="naissance"}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
              {{mb_value object=$curr_patient field="adresse"}}
              {{mb_value object=$curr_patient field="cp"}}
              {{mb_value object=$curr_patient field="ville"}}
            </a>
          </td>
        </tr>
        {{/foreach}}
        
      </table>
      </form>