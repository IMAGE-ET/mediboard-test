<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<table class="form">
  <tr>
    <th class="category" colspan="2">Identit�</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_nom"}}</th>
    <td>{{mb_field object=$patient field="assure_nom"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_prenom"}}</th>
    <td>{{mb_field object=$patient field="assure_prenom"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_prenom_2"}}</th>
    <td>{{mb_field object=$patient field="assure_prenom_2"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_prenom_3"}}</th>
    <td>{{mb_field object=$patient field="assure_prenom_3"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_prenom_4"}}</th>
    <td>{{mb_field object=$patient field="assure_prenom_4"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_nom_jeune_fille"}}</th>
    <td>{{mb_field object=$patient field="assure_nom_jeune_fille"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_sexe"}}</th>
    <td>{{mb_field object=$patient field="assure_sexe"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_naissance"}}</th>
    <td>{{mb_field object=$patient field="assure_naissance"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_cp_naissance"}}</th>
    <td>
      {{mb_field object=$patient field="assure_cp_naissance" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="assure_cp_naissance_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_lieu_naissance"}}</th>
    <td>
      {{mb_field object=$patient field="assure_lieu_naissance"}}
      <div style="display:none;" class="autocomplete" id="assure_lieu_naissance_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="_assure_pays_naissance_insee"}}</th>
    <td> 
      {{mb_field object=$patient field="_assure_pays_naissance_insee"}}
      <div style="display:none;" class="autocomplete" id="_assure_pays_naissance_insee_auto_complete"></div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_nationalite"}}</th>
    <td>{{mb_field object=$patient field="assure_nationalite"}}</td>
  </tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_profession"}}</th>
    <td>{{mb_field object=$patient field="assure_profession"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_matricule"}}</th>
    <td>{{mb_field object=$patient field="assure_matricule"}}</td>
  </tr>
</table>

  </td>
  <td style="width: 50%">
  	
<table class="form">
	<tr>
    <th class="category" colspan="2">Coordonn�es</th>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_adresse"}}</th>
    <td>{{mb_field object=$patient field="assure_adresse"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_cp"}}</th>
    <td>
      {{mb_field object=$patient field="assure_cp" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="assure_cp_auto_complete"></div>
    </td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_ville"}}</th>
    <td>
      {{mb_field object=$patient field="assure_ville" size="31"}}
      <div style="display:none;" class="autocomplete" id="assure_ville_auto_complete"></div>
    </td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_pays"}}</th>
    <td>
      {{mb_field object=$patient field="assure_pays" size="31"}}
      <div style="display:none;" class="autocomplete" id="assure_pays_auto_complete"></div>
    </td>
  </tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_tel"}}</th>
    <td>{{mb_field object=$patient field="assure_tel"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_tel2"}}</th>
    <td>{{mb_field object=$patient field="assure_tel2"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_rques"}}</th>
    <td>{{mb_field object=$patient field="assure_rques" onblur="tabs.changeTabAndFocus('identite', this.form.nom)"}}</td>
	</tr>
</table>

    </td>
  </tr>
</table>