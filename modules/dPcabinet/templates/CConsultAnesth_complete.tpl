{{assign var="consult" value=$object->_ref_consultation}}
<script type="text/javascript">
newExam = function(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}
</script>

<table class="form">
  <tr>
    <th class="title" colspan="4">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$consult->_class_name}}', {{$consult->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      <a style="float:left;" href="#nothing"
        onmouseover="ObjectTooltip.create(this, '{{$consult->_class_name}}', {{$consult->_id}}, { mode: 'notes' })"
        onclick="new Note().create('{{$consult->_class_name}}', {{$consult->_id}});">
        <img src="images/icons/note_blue.png" alt="Ecrire une note" />
      </a>
      {{$consult->_view}}
    </th>
  </tr>
  <tr>
    <td colspan="2">
      <strong>Date :</strong>
      <i>le {{$object->_ref_plageconsult->date|date_format:"%d %B %Y"}} à {{$consult->heure|date_format:"%Hh%M"}}</i>
    </td>
    <td colspan="2">
      <strong>Praticien :</strong>
      <i>Dr. {{$object->_ref_plageconsult->_ref_chir->_view}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Motif :</strong>
      <i>{{$consult->motif|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Remarques :</strong>
      <i>{{$consult->rques|nl2br}}</i>
    </td>
  </tr>
  
  <tr>
    <td class="text" colspan="4">
      <strong>Intervention :</strong>
      {{if $object->operation_id}}
        le <i>{{$object->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}}</i>
        par le <i>Dr {{$object->_ref_operation->_ref_chir->_view}}</i>
        (coté {{tr}}COperation.cote.{{$object->_ref_operation->cote}}{{/tr}})
        <ul>
          {{if $object->_ref_operation->libelle}}
            <li><em>[{{$object->_ref_operation->libelle}}]</em></li>
          {{/if}}
          {{foreach from=$object->_ref_operation->_ext_codes_ccam item=curr_code}}
            <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}})</li>
          {{/foreach}}
        </ul>
      {{else}}
        Aucune Intervention
      {{/if}}
    </td>
  </tr>
  
  {{if $object->operation_id}}
  <tr>
    <td class="text" colspan="2">
      <strong>Admission : </strong>
      <i>
        {{tr}}CSejour.type.{{$object->_ref_operation->_ref_sejour->type}}{{/tr}}
        {{if $object->_ref_operation->_ref_sejour->type!="ambu" && $object->_ref_operation->_ref_sejour->type!="exte"}}
          &mdash; {{$object->_ref_operation->_ref_sejour->_duree_prevue}} jour(s)
        {{/if}}
      </i>
    </td>
    <td class="text" colspan="2">
      <strong>Anesthésie prévue :</strong>
      <i>{{$object->_ref_operation->_lu_type_anesth}}</i>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="text" colspan="2">
      <strong>Position :</strong>
      <i>{{tr}}CConsultAnesth.position.{{$object->position}}{{/tr}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Techniques Complémentaires :</strong>
      <ul>
        {{foreach from=$object->_ref_techniques item=curr_tech}}
        <li>
          <i>{{$curr_tech->technique}}</i>
        </li>
        {{foreachelse}}
          <li><i>Pas de technique complémentaire</i></li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
  
  <tr>
    <th class="title" colspan="4">
      Informations sur le patient
    </th>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Poids :</strong>
      {{if $object->poid}}<i>{{$object->poid}} kg</i>{{/if}}
    </td>
    <td class="text" rowspan="2">
      <strong>IMC :</strong>
      {{if $object->_imc}}<i>{{$object->_imc}}</i>{{/if}}
      {{if $object->_imc_valeur}}<br/><i>{{$object->_imc_valeur}}</i>{{/if}}
    </td>
    <td class="text">
      <strong>Groupe sanguin :</strong>
      <i>{{tr}}CConsultAnesth.groupe.{{$object->groupe}}{{/tr}} &nbsp;{{tr}}CConsultAnesth.rhesus.{{$object->rhesus}}{{/tr}}</i>
    </td>
    <td class="text">
      <strong>RAI :</strong>
      <i>{{tr}}CConsultAnesth.rai.{{$object->rai}}{{/tr}}</i>
    </td>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Taille :</strong>
      {{if $object->taille}}<i>{{$object->taille}} cm</i>{{/if}}
    </td>
    <td class="text">
      <strong>ASA :</strong>
      <i>{{tr}}CConsultAnesth.ASA.{{$object->ASA}}{{/tr}}</i>
    </td>
    <td class="text">
      <strong>VST :</strong>
      <i>{{if $object->_vst}}{{$object->_vst}} ml{{/if}}</i>
    </td>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Pouls :</strong>
      {{if $object->pouls}}<i>{{$object->pouls}} / min</i>{{/if}}
    </td>
    <td class="text">
      <strong>TA :</strong>
      {{if $object->tasys || $object->tadias}}
      <i>
        {{$object->tasys}} / {{$object->tadias}} cm Hg
      </i>
      {{/if}}
    </td>
    <td class="text">
      <strong>Spo2 :</strong>
      {{if $object->spo2}}<i>{{$object->spo2}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>PSA :</strong>
      {{if $object->_psa}}<i>{{$object->_psa}} ml/GR</i>{{/if}}
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Tabac :</strong>
      <i>{{$object->tabac|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Oenolisme:</strong>
      <i>{{$object->oenolisme|nl2br}}</i>
    </td>
  </tr>
  
  {{if $object->mallampati || $object->bouche ||
       $object->distThyro || $object->etatBucco || $object->conclusion}}
  <tr>
    <th class="title" colspan="4">
      Conditions d'intubation
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Mallampati :</strong>
      {{if $object->mallampati}}<i>{{tr}}CConsultAnesth.mallampati.{{$object->mallampati}}{{/tr}}</i>{{/if}}
    </td>
    <td class="text" colspan="2">
      <strong>Etat bucco-dentaire :</strong>
      <i>{{$object->etatBucco|nl2br}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Ouverture de la bouche :</strong>
      {{if $object->bouche}}<i>{{tr}}CConsultAnesth.bouche.{{$object->bouche}}{{/tr}}</i>{{/if}}
    </td>
    <td class="text" colspan="2">
      <strong>Conclusion :</strong>
      <i>{{$object->conclusion|nl2br}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Distance thyro-mentonnière :</strong>
      {{if $object->distThyro}}<i>{{tr}}CConsultAnesth.distThyro.{{$object->distThyro}}{{/tr}}</i>{{/if}}
    </td>
    <td class="text" colspan="2">
      <i>
        {{if $object->_intub_difficile}}
          Intubation Difficile Prévisible
        {{else}}
          Pas Intubation Difficile Prévisible
        {{/if}}
      </i>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th class="title" colspan="4">
      Examens Complémentaires
    </th>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Hb :</strong>
      {{if $object->hb}}<i>{{$object->hb}} g/dl</i>{{/if}}
    </td>
    <td class="text">
      <strong>Plaquettes :</strong>
      {{if $object->plaquettes}}<i>{{$object->plaquettes}}</i>{{/if}}
    </td>
    <td class="text">
      <strong>Na+ :</strong>
      {{if $object->na}}<i>{{$object->na}} mmol/l</i>{{/if}}
    </td>
    <td class="text">
      <strong>TCA :</strong>
      {{if $object->tca}}
        <i>{{$object->tca_temoin}} s / {{$object->tca}} s</i>
      {{/if}}
    </td>  
  </tr>
  <tr>
    <td class="text">
      <strong>Ht :</strong>
      {{if $object->ht}}<i>{{$object->ht}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>Créatinine :</strong>
      {{if $object->creatinine}}<i>{{$object->creatinine}} mg/l</i>{{/if}}
    </td>
    <td class="text">
      <strong>K+ :</strong>
      {{if $object->k}}<i>{{$object->k}} mmol/l</i>{{/if}}
    </td>
    <td class="text">
      <strong>TS Ivy :</strong>
      {{if $object->tsivy}}<i>{{$object->tsivy|date_format:"%Mm%Ss"}}</i>{{/if}}
    </td>  
  </tr>
  <tr>
    <td class="text">
      <strong>Ht final :</strong>
      {{if $object->ht_final}}<i>{{$object->ht_final}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>Clairance de Créatinine :</strong>
      {{if $object->_clairance}}<i>{{$object->_clairance}} ml/min</i>{{/if}}
    </td>
    <td class="text">
      <strong>TP :</strong>
      {{if $object->tp}}<i>{{$object->tp}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>ECBU :</strong>
      {{if $object->ecbu}}<i>{{tr}}CConsultAnesth.ecbu.{{$object->ecbu}}{{/tr}}</i>{{/if}}
    </td>  
  </tr>
  
  <tr>
    <td class="text" colspan="2">
      <strong>Examens Complémentaires :</strong>
      {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
      {{if $list_exams|@count}}
        <br/><i>{{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}</i>
        <ul>
          {{foreach from=$list_exams item=curr_examcomp}}
          <li>
            {{$curr_examcomp->examen}}
            {{if $curr_examcomp->fait}}
              (Fait)
            {{else}}
              (A Faire)
            {{/if}}
          </li>
          {{/foreach}}
        </ul>
      {{/if}}
      {{foreachelse}}
       <i>Pas d'examen complémentaire</i>
      {{/foreach}}
    </td>
    <td class="text" colspan="2">
      {{if $consult->_ref_exampossum->_id}}
        <strong>Score Possum :</strong>
        <i>
          Morbidité : {{mb_value object=$consult->_ref_exampossum field="_morbidite"}}% &mdash;
          Mortalité : {{mb_value object=$consult->_ref_exampossum field="_mortalite"}}%
        </i><br />
      {{/if}}
      {{if $consult->_ref_examnyha->_id}}
        <strong>Clasification NYHA :</strong>
        <i>{{mb_value object=$consult->_ref_examnyha field="_classeNyha"}}</i>   
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Prémédication :</strong>
      <i>{{$object->premedication|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Préparation pré-opératoire :</strong>
      <i>{{$object->prepa_preop|nl2br}}</i>
    </td>
  </tr>  
</table>

<table class="form">
  <tr>
    <th class="title" colspan="{{if $dPconfig.dPcabinet.addictions}}4{{else}}3{{/if}}">
      Eléments significatifs
    </th>
  </tr>
  <tr>
    <th class="title">Antécédent(s)</th>
    <th class="title">Traitement(s)</th>
    <th class="title">Diagnostic(s)</th>
    {{if $dPconfig.dPcabinet.addictions}}
    <th class="title">Addiction(s)</th>
    {{/if}}
  </tr>
  
  <tr>
    <td class="text">
      {{foreach from=$object->_ref_antecedents key=curr_type item=list_antecedent}}
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
    <td class="text">
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
    <td class="text">
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
    
    {{if $dPconfig.dPcabinet.addictions}}
    <td class="text">
      {{foreach from=$object->_ref_types_addiction key=curr_type item=list_addiction}}
      <strong>
        {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
      </strong>
      <ul>
        {{foreach from=$list_addiction item=curr_addiction}}
        <li>
          {{mb_value object=$curr_addiction field="addiction"}}
        </li>
        {{/foreach}}
      </ul>
      {{foreachelse}}
        <i>Pas d'addictions</i>
      {{/foreach}}
    </td>
    {{/if}}
  </tr>
</table>