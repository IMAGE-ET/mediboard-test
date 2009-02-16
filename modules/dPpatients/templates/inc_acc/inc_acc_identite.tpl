<script type="text/javascript">

togglePrenomsList = function(element) {
	var list = $("patient_identite").select('.prenoms_list').invoke('toggle');
	Element.classNames(element).flip('up', 'down');
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
    <td>{{mb_field object=$patient field="nom" onchange="copyIdentiteAssureValues(this)"}}</td>
    {{if $patient->_id}}
    <td rowspan="11" style="width: 0.1%; text-align: center;" id="{{$patient->_guid}}-identity">
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
    <td>{{mb_field object=$patient field="sexe" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_field object=$patient field="naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
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
    <th class="category" colspan="2">Coordonnées</th>
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
    	  Les champs d'identité du patient sont <strong>recopiés en temps réel</strong> vers 
    	  les champs d'identités de l'assuré
    	  car le rang de bénéficiaire est <strong>01 (assuré)</strong>.
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
    	  Les champs de correspondance du patient sont <strong>systématiquement recopiés</strong> vers 
    	  les champs de correspondance de l'assuré.
    	</div>
    </td>
	</tr>
</table>