<script type="text/javascript">

togglePrenomsList = function(element) {
	var list = $("patient_identite").select('.prenoms_list').invoke('toggle');
	Element.classNames(element).flip('up', 'down');
}

selectFirstEnabled = function(select){
  var found = false;
  $A(select.options).each(function (o,i) {
    if (!found && !o.disabled) {
      $V(select, o.value);
      found = true;
    }
  });
}

disableOptions = function (select, list) {
	$A(select.options).each(function (o) {
    o.disabled = (list.indexOf(o.value) != -1);
	});
  if (select.options[select.selectedIndex].disabled) {
    selectFirstEnabled(select);
  }
}

changeCiviliteForSexe = function(element, assure) {
	var oForm = document.editFrm.elements;
	var valueSexe = $V(element);
	if(valueSexe == 'm') {
		disableOptions($(oForm[(assure ? 'assure_' : '')+'civilite']), ['mme', 'melle', 'vve']);
	} else {
		disableOptions($(oForm[(assure ? 'assure_' : '')+'civilite']), ['m']);
	} 
}

var adult_age = {{$dPconfig.dPpatients.CPatient.adult_age}};

changeCiviliteForDate = function(element, assure) {
	var oForm = document.editFrm.elements;
  if ($V(element)) {
	  var date = new Date();
	  var naissance = $V(element).split('/')[2];
	  if (((date.getFullYear()- adult_age) <= naissance) && (naissance <= (date.getFullYear()))) {
		  $V($(oForm[(assure ? 'assure_' : '')+'civilite']), "enf");
	  } else {
		  changeCiviliteForSexe(element.form.sexe);
	  }
  }
}

anonymous = function() {
	$V("editFrm_nom"   , "anonyme");   
  $V("editFrm_prenom", "anonyme"); 	
}

Main.add(function() {
  var i, 
      list = $("patient_identite").select(".prenoms_list input"),
      button = $("patient_identite").select("button.down.notext");
  for (i = 0; i < list.length; i++) {
    if ($V(list[i])) {
    	togglePrenomsList(button[0]);
      break;
    }
  }
  changeCiviliteForSexe(document.forms.editFrm.elements.sexe);
  changeCiviliteForSexe(document.forms.editFrm.elements.assure_sexe, true);
}); 
</script>

<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<table class="form" id="patient_identite">
  <tr>
    <th class="category" colspan="3">Identité</th>
	</tr>
  <tr>
    <th style="width:30%">{{mb_label object=$patient field="nom"}}</th>
    <td>
      {{mb_field object=$patient field="nom" onchange="copyIdentiteAssureValues(this)"}}
      {{if !$patient->_id}}
        <button type="button" style="padding: 0px" onclick="anonymous()" tabIndex="1000"><img src="modules/dPpatients/images/anonyme.png" alt="Anonyme" /></button>
	    {{/if}}
    </td>
    {{if $patient->_id}}
    <td rowspan="14" style="width: 0.1%; text-align: center;" id="{{$patient->_guid}}-identity">
      {{include file=inc_vw_photo_identite.tpl mode="edit"}}
    </td>
    {{/if}}
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>
	    {{mb_field object=$patient field="prenom" onchange="copyIdentiteAssureValues(this)"}} 
	    <button type="button" class="down notext" onclick="togglePrenomsList(this)" tabIndex="1000">{{tr}}Add{{/tr}}</button> 
    </td>
	</tr>
	
  <tr class="prenoms_list" style="display: none;">
    <th>{{mb_label object=$patient field="prenom_2"}}</th>
    <td>{{mb_field object=$patient field="prenom_2" onchange="copyIdentiteAssureValues(this)"}} </td>
  </tr>
  
  <tr class="prenoms_list" style="display: none;">
    <th>{{mb_label object=$patient field="prenom_3"}}</th>
    <td>{{mb_field object=$patient field="prenom_3" onchange="copyIdentiteAssureValues(this)"}}</td>
  </tr>
  
  <tr class="prenoms_list" style="display: none;">
    <th>{{mb_label object=$patient field="prenom_4"}}</th>
    <td>{{mb_field object=$patient field="prenom_4" onchange="copyIdentiteAssureValues(this)"}} </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_field object=$patient field="nom_jeune_fille" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_field object=$patient field="sexe" onchange="copyIdentiteAssureValues(this);changeCiviliteForSexe(this);"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_field object=$patient field="naissance" onchange="copyIdentiteAssureValues(this);changeCiviliteForDate(this);"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="civilite"}}</th>
    <td>
      {{assign var=civilite_locales value=$patient->_specs.civilite}} 
      <select name="civilite" onchange="copyIdentiteAssureValues(this);">
        {{foreach from=$civilite_locales->_locales key=key item=curr_civilite}} 
        <option value="{{$key}}" {{if $key == $patient->civilite}}selected="selected"{{/if}}> {{tr}}CPatient.civilite.{{$key}}-long{{/tr}} - ({{$curr_civilite}}) </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="rang_naissance"}}</th>
    <td>{{mb_field object=$patient field="rang_naissance"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="cp_naissance"}}</th>
    <td>{{mb_field object=$patient field="cp_naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="lieu_naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="_pays_naissance_insee"}}</th>
    <td>
      {{mb_field object=$patient field="_pays_naissance_insee" onchange="copyIdentiteAssureValues(this)" class="autocomplete"}}
      <div style="display:none;" class="autocomplete" id="_pays_naissance_insee_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="nationalite"}}</th>
    <td>{{mb_field object=$patient field="nationalite" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="profession"}}</th>
    <td>{{mb_field object=$patient field="profession" form=editFrm onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="matricule"}}</th>
    <td>{{mb_field object=$patient field="matricule" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="qual_beneficiaire"}}</th>
    <td>{{mb_field object=$patient field="qual_beneficiaire" onchange=showCopieIdentite()}}</td>
	</tr>
</table>	
    
  </td>
  <td style="width: 50%">
  	
<table class="form">
  <tr>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_field object=$patient field="adresse" onchange="copyAssureValues(this)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>{{mb_field object=$patient field="cp" onchange="copyAssureValues(this)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_field object=$patient field="ville" onchange="copyAssureValues(this)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="pays"}}</th>
    <td>
      {{mb_field object=$patient field="pays" size="31" onchange="copyAssureValues(this)" class="autocomplete"}}
      <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_field object=$patient field="tel" onchange="copyAssureValues(this)"}}</td>  
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_field object=$patient field="tel2" onchange="copyAssureValues(this)"}}</td>  
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="email"}}</th>
    <td>{{mb_field object=$patient field="email"}}</td>  
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="rques"}}</th>
    <td>{{mb_field object=$patient field="rques" onblur="this.form.qual_beneficiaire.value == '0' ?
           tabs.changeTabAndFocus('beneficiaire', this.form.regime_sante) :
           tabs.changeTabAndFocus('assure', this.form.assure_nom);"}}</td>
  </tr>
</table>

    </td>
  </tr>
  <tr>
    <td class="text">
    	<div class="big-info" id="copie-identite">
    	  Les champs d'identité du patient sont <strong>recopiés en temps réel</strong> vers 
    	  les champs d'identité de l'assuré
    	  car la qualité de bénéficiaire est <strong>0 (assuré)</strong>.
    	</div>
      <script type="text/javascript">
        function showCopieIdentite() {
        	$("copie-identite").setVisible($V(getForm("qual_beneficiaire")) == "0");
        }
        
        showCopieIdentite();
      </script>
    </td>
    <td class="text">
    	<div class="big-info" id="copie-coordonnees">
    	  Les champs de correspondance du patient sont <strong>systématiquement recopiés</strong> vers 
    	  les champs de correspondance de l'assuré.
    	</div>
    </td>
	</tr>
</table>