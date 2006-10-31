<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2ème patient</th>
    <th width="30%" class="category">Résultat</th>
  </tr>
  <tr>
    <th><label for="regime_sante" title="Regime d'assurance santé">Régime d'assurance santé</label></th>
    <td>
      <input type="radio" name="_choix_regime_sante" value="{{$patient1->regime_sante}}" checked="checked" onclick="setField(this.form.regime_sante, '{{$patient1->regime_sante|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->regime_sante}}
    </td>
    <td>
      <input type="radio" name="_choix_regime_sante" value="{{$patient2->regime_sante}}" onclick="setField(this.form.regime_sante, '{{$patient2->regime_sante|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->regime_sante}}
    </td>
    <td>
      <input tabindex="200" type="text" name="regime_sante" value="{{$finalPatient->regime_sante}}" title="{{$finalPatient->_props.regime_sante}}" />
     </td>
  </tr>
  <tr>
    <th>
      <label for="cmu" title="Couverture Mutuelle Universelle">CMU</label>
    </th>
    <td>
      <input type="radio" name="_choix_cmu" value="{{$patient1->cmu}}" checked="checked"
      onclick="setField(this.form.cmu, '{{$patient1->cmu}}'); setField(this.form._cmu_view, '{{$patient1->cmu|date_format:"%d/%m/%Y"}}')" />
      {{$patient1->cmu|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      <input type="radio" name="_choix_cmu" value="{{$patient2->cmu}}"
      onclick="setField(this.form.cmu, '{{$patient2->cmu}}'); setField(this.form._cmu_view, '{{$patient2->cmu|date_format:"%d/%m/%Y"}}')" />
      {{$patient2->cmu|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      <input type="hidden" name="cmu" value="{{$finalPatient->cmu}}" />
      <input type="text" readonly="readonly" name="_cmu_view" value="{{$finalPatient->cmu|date_format:"%d/%m/%Y"}}" />
    </td>
  </tr>
  <tr>
    <th><label for="ald" title="Affection longue Durée">ALD</label></th>
    <td class="text">
      <input type="radio" name="_choix_ald" value="{{$patient1->ald}}" checked="checked" onclick="setField(this.form.ald, '{{$patient1->ald|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->ald|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_ald" value="{{$patient2->ald}}" onclick="setField(this.form.ald, '{{$patient2->ald|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->ald|nl2br}}
    </td>
    <td class="text">
      <textarea tabindex="201" rows="3" title="{{$finalPatient->_props.ald}}" name="ald">{{$finalPatient->ald}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="incapable_majeur" title="Patient reconnu incapable majeur">Incapable majeur</label></th>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{{$patient1->incapable_majeur}}" checked="checked" onclick="setChecked(this.form.incapable_majeur, '{{$patient1->incapable_majeur|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.incapable_majeur.{{$patient1->incapable_majeur}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{{$patient2->incapable_majeur}}" onclick="setChecked(this.form.incapable_majeur, '{{$patient2->incapable_majeur|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.incapable_majeur.{{$patient2->incapable_majeur}}{{/tr}}
    </td>
    <td>
      <input tabindex="202" type="radio" name="incapable_majeur" value="1" {{if $finalPatient->incapable_majeur == "1"}} checked="checked" {{/if}} />oui
      <input tabindex="203" type="radio" name="incapable_majeur" value="0" {{if $finalPatient->incapable_majeur == "0"}} checked="checked" {{/if}} />non
    </td>
  </tr>
  <tr>
    <th><label for="ATNC" title="Patient présentant un risque ATNC">ATNC</label></th>
    <td>
      <input type="radio" name="_choix_ATNC" value="{{$patient1->ATNC}}" checked="checked" onclick="setChecked(this.form.ATNC, '{{$patient1->ATNC|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.ATNC.{{$patient1->ATNC}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_ATNC" value="{{$patient2->ATNC}}" onclick="setChecked(this.form.ATNC, '{{$patient2->ATNC|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.ATNC.{{$patient2->ATNC}}{{/tr}}
    </td>
    <td>
      <input tabindex="204" type="radio" name="ATNC" value="1" {{if $finalPatient->ATNC == "1"}} checked="checked" {{/if}} />oui
      <input tabindex="205" type="radio" name="ATNC" value="0" {{if $finalPatient->ATNC == "0"}} checked="checked" {{/if}} />non
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin_traitant" title="Choisir un médecin traitant">Medecin traitant</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin_traitant" value="{{$patient1->medecin_traitant}}" checked="checked"
      onclick="setField(this.form.medecin_traitant, '{{$patient1->medecin_traitant}}'); setField(this.form._medecin_traitant_view, '{{$patient1->_ref_medecin_traitant->_view}}')" />
      {{$patient1->_ref_medecin_traitant->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin_traitant" value="{{$patient2->medecin_traitant}}"
      onclick="setField(this.form.medecin_traitant, '{{$patient2->medecin_traitant}}'); setField(this.form._medecin_traitant_view, '{{$patient2->_ref_medecin_traitant->_view}}')" />
      {{$patient2->_ref_medecin_traitant->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin_traitant" value="{{$finalPatient->medecin_traitant}}" />
      <input type="text" readonly="readonly" name="_medecin_traitant_view" value="{{$finalPatient->_ref_medecin_traitant->_view}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin1" title="Choisir un médecin correspondant">Correspondant 1</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin1" value="{{$patient1->medecin1}}" checked="checked"
      onclick="setField(this.form.medecin1, '{{$patient1->medecin1}}'); setField(this.form._medecin1_view, '{{$patient1->_ref_medecin1->_view}}')" />
      {{$patient1->_ref_medecin1->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin1" value="{{$patient2->medecin1}}"
      onclick="setField(this.form.medecin1, '{{$patient2->medecin1}}'); setField(this.form._medecin1_view, '{{$patient2->_ref_medecin1->_view}}')" />
      {{$patient2->_ref_medecin1->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin1" value="{{$finalPatient->medecin1}}" />
      <input type="text" readonly="readonly" name="_medecin1_view" value="{{$finalPatient->_ref_medecin1->_view}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin2" title="Choisir un médecin correspondant">Correspondant 2</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin2" value="{{$patient1->medecin2}}" checked="checked"
      onclick="setField(this.form.medecin2, '{{$patient1->medecin2}}'); setField(this.form._medecin2_view, '{{$patient1->_ref_medecin2->_view}}')" />
      {{$patient1->_ref_medecin2->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin2" value="{{$patient2->medecin2}}"
      onclick="setField(this.form.medecin2, '{{$patient2->medecin2}}'); setField(this.form._medecin2_view, '{{$patient2->_ref_medecin2->_view}}')" />
      {{$patient2->_ref_medecin2->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin2" value="{{$finalPatient->medecin2}}" />
      <input type="text" readonly="readonly" name="_medecin2_view" value="{{$finalPatient->_ref_medecin2->_view}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin3" title="Choisir un médecin correspondant">Correspondant 3</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin3" value="{{$patient1->medecin3}}" checked="checked"
      onclick="setField(this.form.medecin3, '{{$patient1->medecin3}}'); setField(this.form._medecin3_view, '{{$patient1->_ref_medecin3->_view}}')" />
      {{$patient1->_ref_medecin3->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin3" value="{{$patient2->medecin3}}"
      onclick="setField(this.form.medecin3, '{{$patient2->medecin3}}'); setField(this.form._medecin3_view, '{{$patient2->_ref_medecin3->_view}}')" />
      {{$patient2->_ref_medecin3->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin3" value="{{$finalPatient->medecin3}}" />
      <input type="text" readonly="readonly" name="_medecin3_view" value="{{$finalPatient->_ref_medecin3->_view}}" />
    </td>
  </tr>
  <tr>
    <th><label for="matricule" title="Matricule valide d'assuré social (13 chiffres + 2 pour la clé)">Numéro d'assuré social</label></th>
    <td>
      <input type="radio" name="_choix_matricule" value="{{$patient1->matricule}}" checked="checked" onclick="setField(this.form.matricule, '{{$patient1->matricule|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->matricule}}
    </td>
    <td>
      <input type="radio" name="_choix_matricule" value="{{$patient2->matricule}}" onclick="setField(this.form.matricule, '{{$patient2->matricule|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->matricule}}
    </td>
    <td>
      <input tabindex="206" type="text" size="15" maxlength="15" name="matricule" title="{{$finalPatient->_props.matricule}}" value="{{$finalPatient->matricule}}" />
    </td>
  </tr>
  <tr>
    <th><label for="SHS" title="Code Administratif SHS">Code administratif</label></th>
    <td>
      <input type="radio" name="_choix_SHS" value="{{$patient1->SHS}}" checked="checked" onclick="setField(this.form.SHS, '{{$patient1->SHS|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->SHS}}
    </td>
    <td>
      <input type="radio" name="_choix_SHS" value="{{$patient2->SHS}}" onclick="setField(this.form.SHS, '{{$patient2->SHS|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->SHS}}
    </td>
    <td><input tabindex="207" type="text" size="10" maxlength="10" name="SHS" title="{{$finalPatient->_props.SHS}}" value="{{$finalPatient->SHS}}" /></td>
  </tr>
</table>