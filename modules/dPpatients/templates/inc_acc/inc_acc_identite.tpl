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
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_field object=$patient field="nom" tabindex="101" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_field object=$patient field="prenom" tabindex="102" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_field object=$patient field="nom_jeune_fille" tabindex="103" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_field object=$patient field="sexe" tabindex="104" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="naissance" defaultFor="_jour"}}</th>
    <td>
      {{mb_field object=$patient field="_jour" tabindex="105" onkeyup="followUp(event)" onchange="copyIdentiteAssureValues(this)"}}
      {{mb_field object=$patient field="_mois" tabindex="106" onkeyup="followUp(event)" onchange="copyIdentiteAssureValues(this)"}}
      {{mb_field object=$patient field="_annee" tabindex="107" onblur="copyIdentiteAssureValues(this)"}}
    </td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="rang_naissance"}}</th>
    <td>{{mb_field object=$patient field="rang_naissance" tabindex="108" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="lieu_naissance" tabindex="109" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="nationalite"}}</th>
    <td>{{mb_field object=$patient field="nationalite" tabindex="110" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="profession"}}</th>
    <td>{{mb_field object=$patient field="profession" tabindex="111" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="matricule"}}</th>
    <td>{{mb_field object=$patient field="matricule" tabindex="112" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$patient field="rang_beneficiaire"}}</th>
    <td>{{mb_field object=$patient field="rang_beneficiaire" tabindex="113" onchange=showCopieIdentite()}}</td>
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
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_field object=$patient field="adresse" tabindex="151" onchange="copyAssureValues(this)"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>
      {{mb_field object=$patient field="cp" tabindex="152" size="31" maxlength="5" onchange="copyAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>
      {{mb_field object=$patient field="ville" tabindex="153" size="31" onchange="copyAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="pays"}}</th>
    <td>
      {{mb_field object=$patient field="pays" tabindex="154" size="31"  onchange="copyAssureValues(this)"}}
      <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_field object=$patient field="tel" tabindex="161" onchange="copyAssureValues(this)"}}</td>  
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_field object=$patient field="tel2" tabindex="162" onchange="copyAssureValues(this)"}}</td>  
  </tr>
    
  <tr>
    <th>{{mb_label object=$patient field="rques"}}</th>
    <td>{{mb_field object=$patient field="rques" tabindex="170" onblur="this.form.rang_beneficiaire.value == '01' ?
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