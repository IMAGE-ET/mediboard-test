<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Identité
    </th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_nom"}}</th>
    <td>{{mb_field object=$patient field="assure_nom" tabindex="401"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_prenom"}}</th>
    <td>{{mb_field object=$patient field="assure_prenom" tabindex="402"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_nom_jeune_fille"}}</th>
    <td>{{mb_field object=$patient field="assure_nom_jeune_fille" tabindex="403"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="assure_sexe"}}</th>
    <td>{{mb_field object=$patient field="assure_sexe" tabindex="404"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_naissance"}}</th>
    <td>{{mb_field object=$patient field="assure_naissance" tabindex="405"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="assure_lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="assure_lieu_naissance" tabindex="408"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_nationalite"}}</th>
    <td>{{mb_field object=$patient field="assure_nationalite" tabindex="409"}}</td>
  </tr>
  
	<tr>
    <th>{{mb_label object=$patient field="assure_profession"}}</th>
    <td>{{mb_field object=$patient field="assure_profession" tabindex="410"}}</td>
	</tr>

	<tr>
    <th>{{mb_label object=$patient field="assure_matricule"}}</th>
    <td>{{mb_field object=$patient field="assure_matricule" tabindex="411"}}</td>
  </tr>

</table>	
    
    </td>
    <td style="width: 50%">
  	
<table class="form">
	<tr>
    <th class="category" colspan="2">
      Coordonnées
    </th>
	</tr>

	<tr>
    <th>{{mb_label object=$patient field="assure_adresse"}}</th>
    <td>{{mb_field object=$patient field="assure_adresse" tabindex="451"}}</td>
	</tr>

	<tr>
    <th>{{mb_label object=$patient field="assure_cp"}}</th>
    <td>
      {{mb_field object=$patient field="assure_cp" tabindex="452" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="assure_cp_auto_complete"></div>
    </td>
	</tr>
	
	<tr>
    <th>{{mb_label object=$patient field="assure_ville"}}</th>
    <td>
      {{mb_field object=$patient field="assure_ville" tabindex="453" size="31"}}
      <div style="display:none;" class="autocomplete" id="assure_ville_auto_complete"></div>
    </td>
	</tr>
	
	<tr>
    <th>{{mb_label object=$patient field="assure_pays"}}</th>
    <td>
      {{mb_field object=$patient field="assure_pays" tabindex="454" size="31"}}
      <div style="display:none" class="autocomplete" id="assure_pays_auto_complete"></div>
    </td>
	</tr>

	<tr>
    <th>{{mb_label object=$patient field="assure_tel"}}</th>
    <td>{{mb_field object=$patient field="assure_tel" tabindex="455"}}</td>
	</tr>

	<tr>
    <th>{{mb_label object=$patient field="assure_tel2"}}</th>
    <td>{{mb_field object=$patient field="assure_tel2" tabindex="456"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="assure_rques"}}</th>
    <td>{{mb_field object=$patient field="assure_rques" tabindex="465" onblur="tabs.changeTabAndFocus('identite', this.form.nom)"}}</td>
	</tr>

</table>

    </td>
  </tr>
</table>