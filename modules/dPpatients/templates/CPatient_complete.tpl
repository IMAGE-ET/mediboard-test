<table class="form">
  <tr>
    <th class="title" colspan="2">
      <form name="actionPat" action="./index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$object->patient_id}}" />
      {{$object->_view}}
      <button type="button" class="print" onclick="printPatient({{$object->patient_id}})">
        Imprimer
      </button>
      {{if $canEdit}}
        <button type="button" class="modify" onclick="editPatient()">
          Modifier
        </button>
      {{/if}}
      </form>
    </th>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="nom"}}</strong>
      <i>{{mb_value object=$object field="nom"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="adresse"}}</strong>
      <i>{{mb_value object=$object field="adresse"}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="prenom"}}</strong>
      <i>{{mb_value object=$object field="prenom"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="cp"}}</strong>
      <i>{{mb_value object=$object field="cp"}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="nom_jeune_fille"}}</strong>
      <i>{{mb_value object=$object field="nom_jeune_fille"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="ville"}}</strong>
      <i>{{mb_value object=$object field="ville"}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="naissance"}}</strong>
      <i>{{mb_value object=$object field="naissance"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="tel"}}</strong>
      <i>{{mb_value object=$object field="tel"}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="sexe"}}</strong>
      <i>{{mb_value object=$object field="sexe"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="tel2"}}</strong>
      <i>{{mb_value object=$object field="tel2"}}</i>
    </td>
  </tr>
  {{if $object->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{mb_label object=$object field="rques"}}</strong>
      <i>{{mb_value object=$object field="rques"}}</i>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th class="title" colspan="2">
      <a style="float:right;" href="#nothing" onclick="view_history_patient({{$object->patient_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
      Informations médicales
    </th>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="matricule"}}</strong>
      <i>{{mb_value object=$object field="matricule"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="cmu"}}</strong>
      <i>
        {{if $object->cmu}}
          jusqu'au
        {{/if}}
        {{mb_value object=$object field="cmu"}}
      </i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="regime_sante"}}</strong>
      <i>{{mb_value object=$object field="regime_sante"}}</i>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="ald"}}</strong>
      <i>{{mb_value object=$object field="ald"}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="medecin_traitant"}}</strong>
      {{if $object->medecin_traitant}}
        <br /><i>Dr. {{mb_value object=$object->_ref_medecin_traitant field="_view"}}<br/>
        {{mb_value object=$object->_ref_medecin_traitant field="adresse"}}
        - {{mb_value object=$object->_ref_medecin_traitant field="cp"}} {{mb_value object=$object->_ref_medecin_traitant field="ville"}}
        {{if $object->_ref_medecin_traitant->tel}}<br />{{mb_value object=$object->_ref_medecin_traitant field="tel"}}{{/if}}
        </i>
      {{/if}}
    </td>
    <td class="text">
      <strong>Médecins correspondants</strong>
      {{if $object->medecin1}}
      <i>
        <br />Dr. {{mb_value object=$object->_ref_medecin1 field="_view"}}
        {{if $object->_ref_medecin1->tel}}
          ({{mb_value object=$object->_ref_medecin1 field="tel"}})
        {{/if}}
      </i>
      {{/if}}
      
      {{if $object->medecin2}}
      <i>
        <br />Dr. {{mb_value object=$object->_ref_medecin2 field="_view"}}
        {{if $object->_ref_medecin2->tel}}
          ({{mb_value object=$object->_ref_medecin2 field="tel"}})
        {{/if}}
      </i>
      {{/if}}
      
      {{if $object->medecin3}}
      <i>
        <br />Dr. {{mb_value object=$object->_ref_medecin3 field="_view"}}
        {{if $object->_ref_medecin3->tel}}
          ({{mb_value object=$object->_ref_medecin3 field="tel"}})
        {{/if}}
      </i>
      {{/if}}
    </td>
  </tr>
</table>

<table class="form">
  <tr>
    <th class="title">Antécédent(s)</th>
    <th class="title">Traitement(s)</th>
    <th class="title">Diagnostic(s)</th>
  </tr>
  
  <tr>
    <td>
      {{foreach from=$object->_ref_types_antecedent key=curr_type item=list_antecedent}}
      <strong>
        {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
      </strong>
      <ul>
        {{foreach from=$list_antecedent item=curr_antecedent}}
        <li>
          {{mb_value object=$curr_antecedent field="date"}}
          {{mb_value object=$curr_antecedent field="rques"}}
        </li>
        {{/foreach}}
      </ul>
      {{foreachelse}}
        <i>Pas d'antécédents</i>
      {{/foreach}}
    </td>
    <td>
      {{if $object->_ref_traitements|@count}}<ul>{{/if}}
      {{foreach from=$object->_ref_traitements item=curr_traitement}}
        <li>
          {{if $curr_traitement->fin}}
            Du {{mb_value object=$curr_traitement field="debut"}}
            au {{mb_value object=$curr_traitement field="fin"}} :
          {{elseif $curr_traitement->debut}}
            Depuis le {{mb_value object=$curr_traitement field="debut"}} :
          {{/if}}
          {{mb_value object=$curr_traitement field="traitement"}}
        </li>
      {{foreachelse}}
        <i>Pas de traitements</i>
      {{/foreach}}
      {{if $object->_ref_traitements|@count}}</ul>{{/if}}
    </td>
    <td>
      {{if $object->_ref_traitements|@count}}<ul>{{/if}}
      {{foreach from=$object->_codes_cim10 item=curr_code}}
        <li>
          <strong>{{$curr_code->code}}:</strong> {{$curr_code->libelle}}
        </li>
      {{foreachelse}}
        <i>Pas de diagnostics</i>
      {{/foreach}}
      {{if $object->_ref_traitements|@count}}</ul>{{/if}}
    </td>
  </tr>
</table>