{{* $Id$ *}}

<script type="text/javascript">

var Patient = {
  create: function() {
    this.edit(0);
  },

  edit: function(patient_id) {
    var url = new Url();
    url.setModuleAction("dPpatients", "vw_edit_patients");
    url.addParam("patient_id", patient_id);
    url.addParam("dialog", "1");

	var oForm = null;
    if (oForm = document.patientSearch) {
      url.addElement(oForm.name);
      url.addElement(oForm.firstName);
    }
    
    if (oForm = document.patientEdit) {
      url.addParam("useVitale", 1);
    }

    url.redirect();
  },

  selectAndUpdate: function(patient_id) {
    var oForm = document.patientEdit;
    oForm.patient_id.value = patient_id;
    submitFormAjax(oForm, 'systemMsg');
    Patient.select(patient_id, oForm.nom.value);
  },
  
  select: function(patient_id, patient_view) {
    var oSelector = window.opener.PatSelector;
    if (oSelector) {
      oSelector.set(patient_id, patient_view);
    }
    else {
      window.opener.setPat(patient_id, patient_view);
    }
    window.close();
  }
}

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
      url.setModuleAction("dPpatients", "pat_selector");
      url.addParam("dialog", 1);

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

{{if $patVitale}}

<!-- Formulaire de mise à jour Vitale -->
<form name="patientEdit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="dosql" value="do_patients_aed" />
{{mb_field object=$patVitale field="patient_id" hidden="true"}}

<table class="form">

	<tr>
	  <th class="category" colspan="2">Valeurs SESAM Vitale</th>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="nom"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="nom"}}
	    {{mb_field object=$patVitale field="nom" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="prenom"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="prenom"}}
	    {{mb_field object=$patVitale field="prenom" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="naissance"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="naissance"}}
	    {{mb_field object=$patVitale field="naissance" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="matricule"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="matricule"}}
	    {{mb_field object=$patVitale field="matricule" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="adresse"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="adresse"}}
	    {{mb_field object=$patVitale field="adresse" hidden="true"}}
	  </td>
  </tr>

</table>

</form>

{{else}}

<!-- Formulaire de recherche -->
<form action="?" name="patientSearch" method="get">

<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="a" value="pat_selector" />
<input type="hidden" name="dialog" value="1" />

<table class="form">

<tr>
  <th class="category" colspan="3">Critères de sélection</th>
</tr>

<tr>
  <th><label for="name" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
  <td><input name="name" value="{{$name|stripslashes}}" size="30" /></td>
  <td>
    {{if $app->user_prefs.GestionFSE}}
      <button class="search" type="button" onclick="Intermax.result();">Carte vitale</button>
    {{/if}}
  </td>
</tr>

<tr>
  <th><label for="firstName" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
  <td><input name="firstName" value="{{$firstName|stripslashes}}" size="30" /></td>
  <td><button class="search" type="submit">Rechercher</button></td>
</tr>

<tr>
  <td colspan="2">
  </td>
</tr>

</table>

</form>
{{/if}}

<!-- Liste de patients -->
<table class="tbl">
  <tr>
    <th class="category" colspan="5">Choisissez un patient dans la liste</th>
  </tr>
  <tr>
    <th align="center">Patient</th>
    <th align="center">Date de naissance</th>
    {{if $patVitale}}
    <th align="center">{{mb_label object=$patVitale field="matricule"}}</th>
    <th align="center">{{mb_label object=$patVitale field="adresse"}}</th>
    {{else}}
    <th align="center">Téléphone</th>
    <th align="center">Mobile</th>
    {{/if}}
    <th align="center">Actions</th>
  </tr>

  <!-- Recherche exacte -->
  {{foreach from=$patients item=_patient}}
    {{include file="inc_line_pat_selector.tpl"}}
  {{foreachelse}}
  {{if $name || $firstName}}
  <tr>
    <td class="button" colspan="5">
      Aucun résultat exact
    </td>
  </tr>
  {{/if}}
  {{/foreach}}
  <tr>
    <td class="button" colspan="5">
      <button class="submit" type="button" onclick="Patient.create()">Créer un patient</button>
      <button class="cancel" type="button" onclick="window.close()">Annuler</button>
    </td>
  </tr>

  <!-- Recherche phonétique -->
  {{if $patientsSoundex|@count}}
  <tr>
    <th colspan="5">
      <em>Résultats proches</em>
    </th>
  </tr>
  {{/if}}

  {{foreach from=$patientsSoundex item=_patient}}
    {{include file="inc_line_pat_selector.tpl"}}
  {{/foreach}}
</table>
