{literal}
<script type="text/javascript">

function setField(oField, sValue) {
  oField.value = sValue;
}

function setChecked(oField, sValue) {
  for (i=0; i < oField.length; i++){
    if (oField[i].value == sValue)
      oField[i].checked = true;
  }
}

</script>
{/literal}

<form name="editFrm" action="index.php?m={$m}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_patients_fusion" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="patient1_id" value="{$patient1->patient_id}" />
<input type="hidden" name="patient2_id" value="{$patient2->patient_id}" />

<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2ème patient</th>
    <th width="30%" class="category">Résultat</th>
  <tr>
    <th><label for="nom" title="Nom du patient. Obligatoire">Nom:</label></th>
    <td>
      <input type="radio" name="_choix_nom" value="{$patient1->nom}" checked="checked" onclick="setField(this.form.nom, '{$patient1->nom|escape:javascript}')" />
      {$patient1->nom}
    </td>
    <td>
      <input type="radio" name="_choix_nom" value="{$patient2->nom}" onclick="setField(this.form.nom, '{$patient2->nom|escape:javascript}')" />
      {$patient2->nom}
    </td>
    <td>
      <input tabindex="1" type="text" name="nom" value="{$finalPatient->nom}" title="{$finalPatient->_props.nom}" />
     </td>
  </tr>
  <tr>
    <th><label for="prenom" title="Prénom du patient. Obligatoire">Prénom:</label></th>
    <td>
      <input type="radio" name="_choix_prenom" value="{$patient1->prenom}" checked="checked" onclick="setField(this.form.prenom, '{$patient1->prenom|escape:javascript}')" />
      {$patient1->prenom}
    </td>
    <td>
      <input type="radio" name="_choix_prenom" value="{$patient2->prenom}" onclick="setField(this.form.prenom, '{$patient2->prenom|escape:javascript}')" />
      {$patient2->prenom}
    </td>
    <td>
      <input tabindex="2" type="text" name="prenom" value="{$finalPatient->prenom}" title="{$finalPatient->_props.prenom}" /></td>
  </tr>
  <tr>
    <th><label for="nom_jeune_fille" title="Nom de jeune fille d'une femme mariée">Nom de jeune fille:</label></th>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{$patient1->nom_jeune_fille}" checked="checked" onclick="setField(this.form.nom_jeune_fille, '{$patient1->nom_jeune_fille|escape:javascript}')" />
      {$patient1->nom_jeune_fille}
    </td>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{$patient2->nom_jeune_fille}" onclick="setField(this.form.nom_jeune_fille, '{$patient2->nom_jeune_fille|escape:javascript}')" />
      {$patient2->nom_jeune_fille}
    </td>
    <td>
      <input tabindex="3" type="text" name="nom_jeune_fille" title="{$finalPatient->_props.nom_jeune_fille}" value="{$finalPatient->nom_jeune_fille}" />
    </td>
  </tr>
  <tr>
    <th><label for="_jour" title="Date de naissance du patient, au format JJ-MM-AAAA">Date de naissance:</label></th>
    <td>
      <input type="radio" name="_choix_naissance" value="{$patient1->naissance}" checked="checked"
      onclick="setField(this.form._jour, '{$patient1->_jour}'); setField(this.form._mois, '{$patient1->_mois}'); setField(this.form._annee, '{$patient1->_annee}');" />
      {$patient1->_naissance}
    </td>
    <td>
      <input type="radio" name="_choix_naissance" value="{$patient2->naissance}"
      onclick="setField(this.form._jour, '{$patient2->_jour}'); setField(this.form._mois, '{$patient2->_mois}'); setField(this.form._annee, '{$patient2->_annee}');" />
      {$patient2->_naissance}
    </td>
    <td>
      <input tabindex="4" type="text" name="_jour"  size="2" maxlength="2" value="{if $finalPatient->_jour != "00"}{$finalPatient->_jour}{/if}" onkeyup="followUp(this, '_mois', 2)" /> -
      <input tabindex="5" type="text" name="_mois"  size="2" maxlength="2" value="{if $finalPatient->_mois != "00"}{$finalPatient->_mois}{/if}" onkeyup="followUp(this, '_annee', 2)" /> -
      <input tabindex="6" type="text" name="_annee" size="4" maxlength="4" value="{if $finalPatient->_annee != "0000"}{$finalPatient->_annee}{/if}" />
    </td>
  </tr>
  <tr>
    <th><label for="sexe" title="Sexe du patient">Sexe:</label></th>
    <td>
      <input type="radio" name="_choix_sexe" value="{$patient1->sexe}" checked="checked" onclick="setField(this.form.sexe, '{$patient1->sexe}')" />
      {if $patient1->sexe == "m"}
        masculin
      {elseif $patient1->sexe == "f"}
        féminin
      {elseif $patient1->sexe == "j"}
        femme célibataire
      {/if}
    </td>
    <td>
      <input type="radio" name="_choix_sexe" value="{$patient2->sexe}" onclick="setField(this.form.sexe, '{$patient2->sexe}')" />
      {if $patient2->sexe == "m"}
        masculin
      {elseif $patient2->sexe == "f"}
        féminin
      {elseif $patient2->sexe == "j"}
        femme célibataire
      {/if}
    </td>
    <td>
      <select tabindex="7" name="sexe" title="{$finalPatient->_props.sexe}">
        <option value="m" {if $finalPatient->sexe == "m"} selected="selected" {/if}>masculin</option>
        <option value="f" {if $finalPatient->sexe == "f"} selected="selected" {/if}>féminin</option>
        <option value="j" {if $finalPatient->sexe == "j"} selected="selected" {/if}>femme célibataire</option>
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="adresse" title="Adresse du patient">Adresse:</label></th>
    <td>
      <input type="radio" name="_choix_adresse" value="{$patient1->adresse}" checked="checked" onclick="setField(this.form.adresse, '{$patient1->adresse|escape:javascript}')" />
      {$patient1->adresse}
    </td>
    <td>
      <input type="radio" name="_choix_adresse" value="{$patient2->adresse}" onclick="setField(this.form.adresse, '{$patient2->adresse|escape:javascript}')" />
      {$patient2->adresse}
    </td>
    <td>
      <textarea tabindex="8" name="adresse" title="{$finalPatient->_props.adresse}" rows="1">{$finalPatient->adresse}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="cp" title="Code postal">Code Postal:</label></th>
    <td>
      <input type="radio" name="_choix_cp" value="{$patient1->cp}" checked="checked" onclick="setField(this.form.cp, '{$patient1->cp|escape:javascript}')" />
      {$patient1->cp}
    </td>
    <td>
      <input type="radio" name="_choix_cp" value="{$patient2->cp}" onclick="setField(this.form.cp, '{$patient2->cp|escape:javascript}')" />
      {$patient2->cp}
    </td>
    <td>
      <input tabindex="9" type="text" name="cp" value="{$finalPatient->cp}" title="{$finalPatient->_props.cp}" onclick="setField(this.form.cp, '{$patient2->cp|escape:javascript}')" />
    </td>
  </tr>
  <tr>
    <th><label for="ville" title="Ville du patient">Ville:</label></th>
    <td>
      <input type="radio" name="_choix_ville" value="{$patient1->ville}" checked="checked" onclick="setField(this.form.ville, '{$patient1->ville|escape:javascript}')" />
      {$patient1->ville}
    </td>
    <td>
      <input type="radio" name="_choix_ville" value="{$patient2->ville}" onclick="setField(this.form.ville, '{$patient2->ville|escape:javascript}')" />
      {$patient2->ville}
    </td>
    <td>
      <input tabindex="10" type="text" name="ville" value="{$finalPatient->ville}" title="{$finalPatient->_props.ville}" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel1" title="Numéro de téléphone filaire">Téléphone:</label></th>
    <td>
      <input type="radio" name="_choix_tel" value="{$patient1->tel}" checked="checked"
      onclick="setField(this.form._tel1, '{$patient1->_tel1}'); setField(this.form._tel2, '{$patient1->_tel2}');
      setField(this.form._tel3, '{$patient1->_tel3}'); setField(this.form._tel4, '{$patient1->_tel4}'); setField(this.form._tel5, '{$patient1->_tel5}');" />
      {$patient1->tel}
    </td>
    <td>
      <input type="radio" name="_choix_tel" value="{$patient2->tel}"
      onclick="setField(this.form._tel1, '{$patient2->_tel1}'); setField(this.form._tel2, '{$patient2->_tel2}');
      setField(this.form._tel3, '{$patient2->_tel3}'); setField(this.form._tel4, '{$patient2->_tel4}'); setField(this.form._tel5, '{$patient2->_tel5}');" />
      {$patient2->tel}
    </td>
    <td>
      <input tabindex="11" type="text" name="_tel1" size="2" maxlength="2" value="{$finalPatient->_tel1}" title="num|length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
      <input tabindex="12" type="text" name="_tel2" size="2" maxlength="2" value="{$finalPatient->_tel2}" title="num|length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
      <input tabindex="13" type="text" name="_tel3" size="2" maxlength="2" value="{$finalPatient->_tel3}" title="num|length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
      <input tabindex="14" type="text" name="_tel4" size="2" maxlength="2" value="{$finalPatient->_tel4}" title="num|length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
      <input tabindex="15" type="text" name="_tel5" size="2" maxlength="2" value="{$finalPatient->_tel5}" title="num|length|2" onkeyup="followUp(this, '_tel21', 2)" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel21" title="Numéro de téléphone portable">Portable:</label></th>
    <td>
      <input type="radio" name="_choix_tel2" value="{$patient1->tel2}" checked="checked"
      onclick="setField(this.form._tel21, '{$patient1->_tel21}'); setField(this.form._tel22, '{$patient1->_tel22}');
      setField(this.form._tel23, '{$patient1->_tel23}'); setField(this.form._tel24, '{$patient1->_tel24}'); setField(this.form._tel25, '{$patient1->_tel25}');" />
      {$patient1->tel2}
    </td>
    <td>
      <input type="radio" name="_choix_tel2" value="{$patient2->tel2}"
      onclick="setField(this.form._tel21, '{$patient2->_tel21}'); setField(this.form._tel22, '{$patient2->_tel22}');
      setField(this.form._tel23, '{$patient2->_tel23}'); setField(this.form._tel24, '{$patient2->_tel24}'); setField(this.form._tel25, '{$patient2->_tel25}');" />
      {$patient2->tel2}
    </td>
    <td>
      <input tabindex="16" type="text" name="_tel21" size="2" maxlength="2" value="{$finalPatient->_tel21}" title="num|length|2" onkeyup="followUp(this, '_tel22', 2)" /> - 
      <input tabindex="17" type="text" name="_tel22" size="2" maxlength="2" value="{$finalPatient->_tel22}" title="num|length|2" onkeyup="followUp(this, '_tel23', 2)" /> -
      <input tabindex="18" type="text" name="_tel23" size="2" maxlength="2" value="{$finalPatient->_tel23}" title="num|length|2" onkeyup="followUp(this, '_tel24', 2)" /> -
      <input tabindex="19" type="text" name="_tel24" size="2" maxlength="2" value="{$finalPatient->_tel24}" title="num|length|2" onkeyup="followUp(this, '_tel25', 2)" /> -
      <input tabindex="20" type="text" name="_tel25" size="2" maxlength="2" value="{$finalPatient->_tel25}" title="num|length|2" />
    </td>
  </tr>
  <tr>
    <th><label for="incapable_majeur" title="Patient reconnu incapable majeur">Incapable majeur:</label></th>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{$patient1->incapable_majeur}" checked="checked" onclick="setChecked(this.form.incapable_majeur, '{$patient1->incapable_majeur|escape:javascript}')" />
      {$patient1->incapable_majeur}
    </td>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{$patient2->incapable_majeur}" onclick="setChecked(this.form.incapable_majeur, '{$patient2->incapable_majeur|escape:javascript}')" />
      {$patient2->incapable_majeur}
    </td>
    <td>
      <input tabindex="21" type="radio" name="incapable_majeur" value="o" {if $finalPatient->incapable_majeur == "o"} checked="checked" {/if} />oui
      <input tabindex="22" type="radio" name="incapable_majeur" value="n" {if $finalPatient->incapable_majeur == "n"} checked="checked" {/if} />non
    </td>
  </tr>
  <tr>
    <th><label for="ATNC" title="Patient présentant un risque ATNC">ATNC:</label></th>
    <td>
      <input type="radio" name="_choix_ATNC" value="{$patient1->ATNC}" checked="checked" onclick="setChecked(this.form.ATNC, '{$patient1->ATNC|escape:javascript}')" />
      {$patient1->ATNC}
    </td>
    <td>
      <input type="radio" name="_choix_ATNC" value="{$patient2->ATNC}" onclick="setChecked(this.form.ATNC, '{$patient2->ATNC|escape:javascript}')" />
      {$patient2->ATNC}
    </td>
    <td>
      <input tabindex="23" type="radio" name="ATNC" value="o" {if $finalPatient->ATNC == "o"} checked="checked" {/if} />oui
      <input tabindex="24" type="radio" name="ATNC" value="n" {if $finalPatient->ATNC == "n"} checked="checked" {/if} />non
    </td>
  </tr>
  <tr>
    <th><label for="matricule" title="Matricule valide d'assuré social (13 chiffres + 2 pour la clé)">Numéro d'assuré social:</label></th>
    <td>
      <input type="radio" name="_choix_matricule" value="{$patient1->matricule}" checked="checked" onclick="setField(this.form.matricule, '{$patient1->matricule|escape:javascript}')" />
      {$patient1->matricule}
    </td>
    <td>
      <input type="radio" name="_choix_matricule" value="{$patient2->matricule}" onclick="setField(this.form.matricule, '{$patient2->matricule|escape:javascript}')" />
      {$patient2->matricule}
    </td>
    <td>
      <input tabindex="25" type="text" size="15" maxlength="15" name="matricule" title="{$finalPatient->_props.matricule}" value="{$finalPatient->matricule}" />
    </td>
  </tr>
  <tr>
    <th><label for="SHS" title="Code Administratif SHS">Code administratif:</label></th>
    <td>
      <input type="radio" name="_choix_SHS" value="{$patient1->SHS}" checked="checked" onclick="setField(this.form.SHS, '{$patient1->SHS|escape:javascript}')" />
      {$patient1->SHS}
    </td>
    <td>
      <input type="radio" name="_choix_SHS" value="{$patient2->SHS}" onclick="setField(this.form.SHS, '{$patient2->SHS|escape:javascript}')" />
      {$patient2->SHS}
    </td>
    <td><input tabindex="26" type="text" size="10" maxlength="10" name="SHS" title="{$finalPatient->_props.SHS}" value="{$finalPatient->SHS}" /></td>
  </tr>
  <tr>
    <th><label for="rques" title="Remarques générales concernant le patient">Remarques:</label></th>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{$patient1->rques}" checked="checked" onclick="setField(this.form.rques, '{$patient1->rques|escape:javascript}')" />
      {$patient1->rques}
    </td>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{$patient2->rques}" onclick="setField(this.form.rques, '{$patient2->rques|escape:javascript}')" />
      {$patient2->rques}
    </td>
    <td class="text" rowspan="5">
      <textarea tabindex="27" rows="7" title="{$finalPatient->_props.rques}" name="rques">{$finalPatient->rques}</textarea>
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin_traitant" title="Choisir un médecin traitant">Medecin traitant:</label>
      <input type="hidden" name="medecin_traitant" value="{$patient1->medecin_traitant}" />
    </th>
    <td>
      <input type="radio" name="_choix_medecin_traitant" value="{$patient1->medecin_traitant}" checked="checked" onclick="setField(this.form.medecin_traitant, '{$patient1->medecin_traitant}')" />
      {$patient1->_ref_medecin_traitant->_view}
    </td>
    <td>
      <input type="radio" name="_choix_medecin_traitant" value="{$patient2->medecin_traitant}" onclick="setField(this.form.medecin_traitant, '{$patient2->medecin_traitant}')" />
      {$patient2->_ref_medecin_traitant->_view}
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin1" title="Choisir un médecin correspondant">Médecin correspondant 1:</label>
      <input type="hidden" name="medecin1" value="{$patient1->medecin1}" />
    </th>
    <td>
      <input type="radio" name="_choix_medecin1" value="{$patient1->medecin1}" checked="checked" onclick="setField(this.form.medecin1, '{$patient1->medecin1}')" />
      {$patient1->_ref_medecin1->_view}
    </td>
    <td>
      <input type="radio" name="_choix_medecin1" value="{$patient2->medecin1}" onclick="setField(this.form.medecin1, '{$patient2->medecin1}')" />
      {$patient2->_ref_medecin1->_view}
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin2" title="Choisir un médecin correspondant">Médecin correspondant 2:</label>
      <input type="hidden" name="medecin2" value="{$patient1->medecin2}" />
    </th>
    <td>
      <input type="radio" name="_choix_medecin2" value="{$patient1->medecin2}" checked="checked" onclick="setField(this.form.medecin2, '{$patient1->medecin2}')" />
      {$patient1->_ref_medecin2->_view}
    </td>
    <td>
      <input type="radio" name="_choix_medecin2" value="{$patient2->medecin2}"  onclick="setField(this.form.medecin2, '{$patient2->medecin2}')"/>
      {$patient2->_ref_medecin2->_view}
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin3" title="Choisir un médecin correspondant">Médecin correspondant 3:</label>
      <input type="hidden" name="medecin3" value="{$patient1->medecin3}" />
    </th>
    <td>
      <input type="radio" name="_choix_medecin3" value="{$patient1->medecin3}" checked="checked" onclick="setField(this.form.medecin3, '{$patient1->medecin3}')" />
      {$patient1->_ref_medecin3->_view}
    </td>
    <td>
      <input type="radio" name="_choix_medecin3" value="{$patient2->medecin3}" onclick="setField(this.form.medecin3, '{$patient2->medecin3}')" />
      {$patient2->_ref_medecin3->_view}
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4"><button type="submit">Fusionner</button></td>
  </tr>
</table>

</form>