<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<table class="form">
  <tr>
    <th class="category" colspan="2">Identit�</th>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_field object=$patient field="nom" onchange="copyIdentiteAssureValues(this)"}}</td>
	</tr>
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_field object=$patient field="prenom" onchange="copyIdentiteAssureValues(this)"}}</td>
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
    <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="lieu_naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
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
      {{mb_field object=$patient field="pays" size="31"  onchange="copyAssureValues(this)"}}
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