<table class="form">

  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2ème patient</th>
    <th width="30%" class="category">Résultat</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$finalPatient field="regime_sante"}}</th>
    <td>
      <input type="radio" name="_choix_regime_sante" value="{{$patient1->regime_sante}}" checked="checked" onclick="setField(this.form.regime_sante, '{{$patient1->regime_sante|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->regime_sante}}
    </td>
    <td>
      <input type="radio" name="_choix_regime_sante" value="{{$patient2->regime_sante}}" onclick="setField(this.form.regime_sante, '{{$patient2->regime_sante|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->regime_sante}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="regime_sante" tabindex="200"}}
     </td>
  </tr>

  <tr>
    <th>
      {{mb_label object=$finalPatient field="cmu"}}
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
      {{mb_field object=$finalPatient field="cmu" hidden=1}}
      <input type="text" readonly="readonly" name="_cmu_view" value="{{$finalPatient->cmu|date_format:"%d/%m/%Y"}}" />
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$finalPatient field="notes_amo"}}</th>
    <td class="text">
      <input type="radio" name="_choix_notes_amo" value="{{$patient1->notes_amo}}" checked="checked" onclick="setField(this.form.notes_amo, '{{$patient1->notes_amo|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->notes_amo|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_notes_amo" value="{{$patient2->notes_amo}}" onclick="setField(this.form.notes_amo, '{{$patient2->notes_amo|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->notes_amo|nl2br}}
    </td>
    <td class="text">
      {{mb_field object=$finalPatient field="notes_amo" tabindex="201" rows="3"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$finalPatient field="incapable_majeur"}}</th>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{{$patient1->incapable_majeur}}" checked="checked" onclick="setCheckedValue(this.form.incapable_majeur, '{{$patient1->incapable_majeur|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.incapable_majeur.{{$patient1->incapable_majeur}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{{$patient2->incapable_majeur}}" onclick="setCheckedValue(this.form.incapable_majeur, '{{$patient2->incapable_majeur|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.incapable_majeur.{{$patient2->incapable_majeur}}{{/tr}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="incapable_majeur" tabindex="202"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$finalPatient field="ATNC"}}</th>
    <td>
      <input type="radio" name="_choix_ATNC" value="{{$patient1->ATNC}}" checked="checked" onclick="setCheckedValue(this.form.ATNC, '{{$patient1->ATNC|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.ATNC.{{$patient1->ATNC}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_ATNC" value="{{$patient2->ATNC}}" onclick="setCheckedValue(this.form.ATNC, '{{$patient2->ATNC|smarty:nodefaults|JSAttribute}}')" />
      {{tr}}CPatient.ATNC.{{$patient2->ATNC}}{{/tr}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="ATNC" tabindex="204"}}
    </td>
  </tr>

  <tr>
    <th>
      {{mb_label object=$finalPatient field="medecin_traitant"}}
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
      {{mb_field object=$finalPatient field="medecin_traitant" hidden=1 prop=""}}
      <input type="text" readonly="readonly" name="_medecin_traitant_view" value="{{$finalPatient->_ref_medecin_traitant->_view}}" />
    </td>
  </tr>

  <tr>
    <th>
      {{mb_label object=$finalPatient field="medecin1"}}
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
      {{mb_field object=$finalPatient field="medecin1" hidden=1 prop=""}}
      <input type="text" readonly="readonly" name="_medecin1_view" value="{{$finalPatient->_ref_medecin1->_view}}" />
    </td>
  </tr>

  <tr>
    <th>
      {{mb_label object=$finalPatient field="medecin2"}}
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
      {{mb_field object=$finalPatient field="medecin2" hidden=1 prop=""}}
      <input type="text" readonly="readonly" name="_medecin2_view" value="{{$finalPatient->_ref_medecin2->_view}}" />
    </td>
  </tr>

  <tr>
    <th>
      {{mb_label object=$finalPatient field="medecin3"}}
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
      {{mb_field object=$finalPatient field="medecin3" hidden=1 prop=""}}
      <input type="text" readonly="readonly" name="_medecin3_view" value="{{$finalPatient->_ref_medecin3->_view}}" />
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$finalPatient field="SHS"}}</th>
    <td>
      <input type="radio" name="_choix_SHS" value="{{$patient1->SHS}}" checked="checked" onclick="setField(this.form.SHS, '{{$patient1->SHS|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->SHS}}
    </td>
    <td>
      <input type="radio" name="_choix_SHS" value="{{$patient2->SHS}}" onclick="setField(this.form.SHS, '{{$patient2->SHS|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->SHS}}
    </td>
    <td>{{mb_field object=$finalPatient field="SHS" tabindex="208" size="10" maxlength="10"}}</td>
  </tr>

</table>