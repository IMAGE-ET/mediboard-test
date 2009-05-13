<script type="text/javascript">

var noChangeCivilite = false;

togglePrenomsList = function(element) {
	var list = $("patient_identite").select('.prenoms_list').invoke('toggle');
	Element.classNames(element).flip('up', 'down');
}

disableOptions = function (select, list) {
	$A(select.options).each(function (o) {
		o.disabled = (list.indexOf(o.value) != -1);
	});
}

changeCiviliteForSexe = function(element) {
	if (!noChangeCivilite) {
		var valueSexe = $V(element);
		if(valueSexe == 'm') {
			$V("editFrm_civilite", 'm');
			disableOptions($('editFrm_civilite'), ['mme', 'melle', 'vve']);
		} else {
			$V("editFrm_civilite", 'mme');
			disableOptions($('editFrm_civilite'), ['m']);
		} 
    noChangeCivilite = false;
	}
}

changeCiviliteForDate = function(element) {
  if (!noChangeCivilite && $V(element)) {
	  var date = new Date();
	  var naissance = $V(element).split('/')[2];
	  if (((date.getFullYear()-15) <= naissance) && (naissance <= (date.getFullYear()))) {
		  $V("editFrm_civilite", "enf");
	  } else {
		  noChangeCivilite = false;
		  changeCiviliteForSexe(element.form.sexe);
	  }
	  noChangeCivilite = false;
  }
}

Main.add(function() {
  var i, 
      list = $("patient_identite").select(".prenoms_list input"),
      button = $("patient_identite").select("button.down.notext");
  for (i = 0; i < list.length; i++) {
	  var input = list[i];
    if ($V(input)) {
    	togglePrenomsList(button[0]);
      break;
    }
  }
  var oForm = document.editFrm;
  var oElement = oForm.civilite;
  changeCiviliteForSexe(oElement);
}); 
</script>

<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<table class="form" id="patient_identite">
  <tr>
    <th class="category" colspan="3">Identit�</th>
	</tr>
  <tr>
    <th style="width:30%">{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_field object=$patient field="nom" onchange="copyIdentiteAssureValues(this)"}}</td>
    {{if $patient->_id}}
    <td rowspan="13" style="width: 0.1%; text-align: center;" id="{{$patient->_guid}}-identity">
      {{include file=inc_vw_photo_identite.tpl mode="edit"}}
    </td>
    {{/if}}
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>
	    {{mb_field object=$patient field="prenom" onchange="copyIdentiteAssureValues(this)"}} 
	    <button type="button" class="down notext" onclick="togglePrenomsList(this)">{{tr}}Add{{/tr}}</button> 
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
      <select name="civilite" onchange="copyIdentiteAssureValues(this); noChangeCivilite=true">
        {{foreach from=$civilite_locales->_locales key=key item=curr_civilite}} 
        <option value="{{$key}}" {{if $key == $patient->civilite}}selected="selected"{{/if}}> {{tr}}CPatient.civilite.{{$key}}-long{{/tr}} - ({{$curr_civilite}}) </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="rang_naissance"}}</th>
    <td>{{mb_field object=$patient field="rang_naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="cp_naissance"}}</th>
    <td>
      {{mb_field object=$patient field="cp_naissance" maxlength="5" onchange="copyIdentiteAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="cp_naissance_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
    <td>
      {{mb_field object=$patient field="lieu_naissance" onchange="copyIdentiteAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="lieu_naissance_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="_pays_naissance_insee"}}</th>
    <td>{{mb_field object=$patient field="_pays_naissance_insee" onchange="copyIdentiteAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="_pays_naissance_insee_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="nationalite"}}</th>
    <td>{{mb_field object=$patient field="nationalite" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="profession"}}</th>
    <td>{{mb_field object=$patient field="profession" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="matricule"}}</th>
    <td>{{mb_field object=$patient field="matricule" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="rang_beneficiaire"}}</th>
    <td>{{mb_field object=$patient field="rang_beneficiaire" onchange=showCopieIdentite()}}</td>
	</tr>
</table>	
    
  </td>
  <td style="width: 50%">
  	
<table class="form">
  <tr>
    <th class="category" colspan="2">Coordonn�es</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_field object=$patient field="adresse" onchange="copyAssureValues(this)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>
      {{mb_field object=$patient field="cp" size="31" maxlength="5" onchange="copyAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>
      {{mb_field object=$patient field="ville" size="31" onchange="copyAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="pays"}}</th>
    <td>
      {{mb_field object=$patient field="pays" size="31" onchange="copyAssureValues(this)"}}
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
    <td>{{mb_field object=$patient field="rques" onblur="this.form.rang_beneficiaire.value == '01' ?
           tabs.changeTabAndFocus('beneficiaire', this.form.regime_sante) :
           tabs.changeTabAndFocus('assure', this.form.assure_nom);"}}</td>
  </tr>
</table>

    </td>
  </tr>
  <tr>
    <td class="text">
    	<div class="big-info" id="copie-identite">
    	  Les champs d'identit� du patient sont <strong>recopi�s en temps r�el</strong> vers 
    	  les champs d'identit�s de l'assur�
    	  car le rang de b�n�ficiaire est <strong>01 (assur�)</strong>.
    	</div>
      <script type="text/javascript">
        function showCopieIdentite() {
        	$("copie-identite")[document.editFrm.rang_beneficiaire.value == "01" ? "show" : "hide"]();
        }
        
        showCopieIdentite();
      </script>
    </td>
    <td class="text">
    	<div class="big-info" id="copie-coordonnees">
    	  Les champs de correspondance du patient sont <strong>syst�matiquement recopi�s</strong> vers 
    	  les champs de correspondance de l'assur�.
    	</div>
    </td>
	</tr>
</table>