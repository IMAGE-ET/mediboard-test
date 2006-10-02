<table class="form">
  <tr>
    <th><label for="regime_sante" title="Regime d'assurance santé">Régime d'assurance santé</label></th>
    <td>
      <input tabindex="201" type="text" size="40" maxlength="40" name="regime_sante" title="{{$patient->_props.regime_sante}}" value="{{$patient->regime_sante}}" />
    </td>
    <th><label for="matricule" title="Matricule valide d'assuré social (13 chiffres + 2 pour la clé)">Numéro d'assuré social</label></th>
    <td colspan="2">
      <input tabindex="251" type="text" size="17" maxlength="15" name="matricule" title="{{$patient->_props.matricule}}" value="{{$patient->matricule}}" />
    </td>
  </tr>
  
  <tr>
    <th><label for="cmu" title="Couverture Mutuelle Universelle">CMU</label></th>
    <td class="date">
      <div id="editFrm_cmu_da">{{$patient->cmu|date_format:"%d/%m/%Y"}}</div>
      <input type="hidden" name="cmu" title="date" value="{{$patient->cmu}}" />
      <img tabindex="202" id="editFrm_cmu_trigger" src="./images/calendar.gif" alt="calendar"/>
      <button class="cancel notext" type="button" onclick="delCmu()"></button>
    </td>
    <th><label for="SHS" title="Code Administratif SHS">Code administratif</label></th>
    <td colspan="2">
      <input tabindex="252" type="text" size="8" maxlength="8" name="SHS" title="{{$patient->_props.SHS}}" value="{{$patient->SHS}}" />
    </td>
  </tr>
  
  <tr>
    <th rowspan="2"><label for="ald" title="Affection longue Durée">ALD</label></th>
    <td rowspan="2">
      <textarea tabindex="203" title="{{$patient->_props.ald}}" name="ald">{{$patient->ald}}</textarea>
    </td>
    <th>
      <label for="medecin_traitant" title="Choisir un médecin traitant">Medecin traitant</label>
      <input type="hidden" name="medecin_traitant" title="{{$patient->_props.medecin_traitant}}" value="{{$patient->medecin_traitant}}" />
    </th>
    <td class="readonly">
      <input type="text" name="_medecin_traitant_name" size="30" value="Dr. {{$patient->_ref_medecin_traitant->_view}}" ondblclick="popMed('_traitant')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('_traitant')"></button>
    </td>
    <td class="button"><button class="search" tabindex="253" type="button" onclick="popMed('_traitant')">Choisir un médecin</button></td>
  </tr>
  
  <tr>
    <th>
      <label for="medecin1" title="Choisir un médecin correspondant">Médecin correspondant 1</label>
      <input type="hidden" name="medecin1" value="{{$patient->_ref_medecin1->medecin_id}}" title="{{$patient->_props.medecin1}}" />
    </th>
    <td class="readonly">
      <input type="text" name="_medecin1_name" size="30" value="Dr. {{$patient->_ref_medecin1->_view}}" ondblclick="popMed('1')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('1')"></button>
    </td>
    <td class="button"><button class="search" tabindex="254" type="button" onclick="popMed('1')">Choisir un médecin</button></td>
  </tr>
  
  <tr>
    <th><label for="incapable_majeur" title="Patient reconnu incapable majeur">Incapable majeur</label></th>
    <td>
      <input tabindex="204" type="radio" name="incapable_majeur" value="o" {{if $patient->incapable_majeur == "o"}} checked="checked" {{/if}} />oui
      <input tabindex="205" type="radio" name="incapable_majeur" value="n" {{if $patient->incapable_majeur == "n"}} checked="checked" {{/if}} />non
    </td>
    <th>
      <input type="hidden" name="medecin2" value="{{$patient->_ref_medecin2->medecin_id}}" title="{{$patient->_props.medecin2}}" />
      <label for="medecin2" title="Choisir un second médecin correspondant">Médecin correspondant 2</label>
    </th>
    <td class="readonly">
      <input type="text" name="_medecin2_name" size="30" value="{{if ($patient->_ref_medecin2)}}Dr. {{$patient->_ref_medecin2->_view}}{{/if}}" ondblclick="popMed('2')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('2')"></button>
    </td>
    <td class="button"><button class="search" tabindex="255" type="button" onclick="popMed('2')">Choisir un médecin</button></td>
  </tr>
  
  <tr>
    <th><label for="ATNC" title="Patient présentant un risque ATNC">ATNC </label></th>
    <td>
      <input tabindex="206" type="radio" name="ATNC" value="o" {{if $patient->ATNC == "o"}} checked="checked" {{/if}} />oui
      <input tabindex="207" type="radio" name="ATNC" value="n" {{if $patient->ATNC == "n"}} checked="checked" {{/if}} />non
    </td>
    <th>
      <input type="hidden" name="medecin3" value="{{$patient->_ref_medecin3->medecin_id}}" title="{{$patient->_props.medecin3}}" />
      <label for="medecin3" title="Choisir un troisième médecin correspondant">Médecin correspondant 3</label>
    </th>
    <td class="readonly">
      <input type="text" name="_medecin3_name" size="30" value="{{if ($patient->_ref_medecin3)}}Dr. {{$patient->_ref_medecin3->_view}}{{/if}}" ondblclick="popMed('3')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('3')"></button>
    </td>
    <td class="button"><button class="search" tabindex="256" type="button" onclick="popMed('3')">Choisir un médecin</button></td>
  </tr>

</table>