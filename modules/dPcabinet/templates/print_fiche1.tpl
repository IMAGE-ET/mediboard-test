{{if !@$offline || @$multi}}
      </td>
    </tr>
  </table>
  
  {{if $print && !@$multi}}
    <script>
    Main.add(window.print);
    </script> 
  {{/if}}
  
  {{assign var=tbl_class value="print"}}
{{else}}
  {{assign var=tbl_class value="main form"}}
{{/if}}

{{assign var="consult"   value=$dossier_anesth->_ref_consultation}}
{{assign var="patient"   value=$consult->_ref_patient}}
{{assign var="operation" value=$dossier_anesth->_ref_operation}}
{{assign var="sejour"    value=$operation->_ref_sejour}}

{{assign var=const_med       value=$patient->_ref_constantes_medicales}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=ant             value=$dossier_medical->_ref_antecedents_by_type}}

<table class="{{$tbl_class}}">
  <tr>
    <td colspan="2">
      <!-- Bordereau d'en-tête -->
      <table width="100%">
        <tr>
          <th class="title" colspan="6">
            <a href="#" onclick="window.print()">
              Dossier d'anesthésie de {{$patient->_view}}
            </a>
          </th>
        </tr>
        <tr>
          <th>Telephone</th>
          <td style="white-space: nowrap;">{{mb_value object=$patient field="tel"}}</td>
          <th>Age</th>
          <td style="white-space: nowrap;">{{$patient->_age}}</td>
          <th>C.A.</th>
          <td>
            Dr {{$consult->_ref_chir->_view}}
            - le {{mb_value object=$consult->_ref_plageconsult field="date"}}</td>
        </tr>
        <tr>
          <th>Mobile</th>
          <td style="white-space: nowrap;">{{mb_value object=$patient field="tel2"}}</td>
          <th>Taille</th>
          <td style="white-space: nowrap;">{{if $const_med->taille}}{{$const_med->taille}} cm{{else}}-{{/if}}</td>
          <th>Séjour</th>
          <td>
            {{if $sejour->_id}}
            {{mb_value object=$sejour field="type"}}
            du {{mb_value object=$sejour field="_entree"}}
            au {{mb_value object=$sejour field="_sortie"}}
            {{else}}
            -
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Profession</th>
          <td>{{$patient->profession}}</td>
          <th>Poids</th>
          <td style="white-space: nowrap;">{{if $const_med->poids}}{{$const_med->poids}} kg{{else}}-{{/if}}</td>
          <th>Intervention</th>
          <td>
            {{if $operation->_id}}
              Dr {{$operation->_ref_chir->_view}} - le {{$operation->_datetime|date_format:"%d/%m/%Y"}}
              {{if $operation->libelle}}
                <em>[{{$operation->libelle}}]</em>
              {{/if}}
            {{else}}
              {{if $dossier_anesth->chir_id}}
                Dr {{$dossier_anesth->_ref_chir}} -
              {{/if}}
              {{if $dossier_anesth->date_interv}}
                le {{$dossier_anesth->date_interv|date_format:"%d/%m/%Y"}}
              {{/if}}
              {{if $dossier_anesth->libelle_interv}}
                <em>[{{$dossier_anesth->libelle_interv}}]</em>
              {{/if}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <!-- Examens complémentaires / Traitements / Allergies / Code ASA-->
      <table width="100%">
        <tr>
          <th class="category">Examens complémentaires</th>
        </tr>
        {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
        {{if $list_exams|@count}}
        <tr>
          <td>
            {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
          </td>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$list_exams item=curr_examcomp}}
              <li>
                {{$curr_examcomp->examen}}
              </li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
       {{/if}}
       {{foreachelse}}
       <tr>
        <td>
          Pas d'examen complémentaire
        </td>
      </tr>
      {{/foreach}}
        <tr>
          <th class="category">Traitements</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Depuis {{mb_value object=$curr_trmt field=debut}} 
                  jusqu'à {{mb_value object=$curr_trmt field=fin}} :
                {{elseif $curr_trmt->debut}}
                  Depuis {{mb_value object=$curr_trmt field=debut}} :
                {{/if}}
                <i>{{$curr_trmt->traitement}}</i>
              </li>
              {{foreachelse}}
              {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
              <li>Pas de traitements</li>
              {{/if}}
              {{/foreach}}
            </ul>
          </td>
        </tr>
        <tr>
          <td>
            <ul>
              {{if $dossier_medical->_ref_prescription}}
                {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line_med}}
                  <li>
                    {{$_line_med->_ucd_view}}
                    {{if $_line_med->_ref_prises|@count}}
                      ({{foreach from=$_line_med->_ref_prises item=_prise name=foreach_prise}}
                        {{$_prise->_view}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
                      {{/foreach}})
                    {{/if}}
                    {{if $_line_med->debut || $_line_med->fin}}
                      <span class="compact">({{mb_include module=system template=inc_interval_date from=$_line_med->debut to=$_line_med->fin}})</span>
                    {{/if}}
                  </li>
                {{/foreach}}
              {{/if}}
            </ul>
          </td>
        </tr>
        <tr>
          <th class="category">Allergies</th>
        </tr>
        <tr>
          <td style="font-weight: bold; white-space: normal; font-size:130%;">
          {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('alle', $dossier_medical->_ref_antecedents_by_type)}}
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.alle item=currAnt}}
              <ul>
                <li> 
                  {{if $currAnt->date}}
                    {{mb_value object=$currAnt field=date}} :
                  {{/if}}
                  {{$currAnt->rques}} 
                </li>
              </ul>
            {{/foreach}}
          {{else}}
            <ul>
              <li>Pas d'allergie saisie</li>
            </ul>
          {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Code ASA : {{if $operation->_id}}{{mb_value object=$operation field=ASA}}{{else}}{{mb_value object=$dossier_anesth field=ASA}}{{/if}}</th>
        </tr>
      </table>
    </td>
    <td width="50%">
      <!-- Docs édités / Intubation / Prémédication / Techniques complémentaires-->
      <table width="100%">
        <tr>
          <th class="category">Documents édités</th>
        </tr>
        <tr>
          <td>
            {{foreach from=$consult->_ref_documents item=currDoc}}
              {{$currDoc->nom}} ;
            {{foreachelse}}
            Aucun Document
            {{/foreach}}
            </ul>
          </td>
        </tr>
        <tr>
          <th class="category">Intubation</th>
        </tr>
        <tr>
          <td>
            <strong>Mallampati :</strong>
            {{tr}}CConsultAnesth.mallampati.{{$dossier_anesth->mallampati}}{{/tr}}
            <br />
            <strong>Ouverture de la bouche :</strong>
            {{tr}}CConsultAnesth.bouche.{{$dossier_anesth->bouche}}{{/tr}}
            <br />
            <strong>Distance thyro-mentonière :</strong>
            {{tr}}CConsultAnesth.distThyro.{{$dossier_anesth->distThyro}}{{/tr}}
            <br />
            <strong>Mobilité cervicale :</strong>
            {{tr}}CConsultAnesth.mob_cervicale.{{$dossier_anesth->mob_cervicale}}{{/tr}}
            <br />
            <strong>Etat bucco-dentaire :</strong>
            {{$dossier_anesth->etatBucco|nl2br}}
            <br />
            {{if $etatDents}}
              {{$etatDents|nl2br}}
            {{/if}}
            <strong>Conclusion :</strong>
            {{$dossier_anesth->conclusion|nl2br}}
            <br />
            {{if $dossier_anesth->_intub_difficile}}
            <span style="font-weight: bold; text-align:center; color:#F00;">
              Intubation difficile prévisible
            </span>
            {{else}}
            <span style="font-weight: bold; text-align:center;">
              Pas d'intubation difficile prévisible
            </span>        
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Prémédication</th>
        </tr>
        <tr>
          <td>
            {{$dossier_anesth->premedication|nl2br}}
          </td>
        </tr>
        {{assign var=prescription value =$sejour->_ref_prescription_sejour}}
        <tr>
          <td>
            <ul>
              {{foreach from=$lines item=_line}}
                {{if $_line instanceof CPrescriptionLineMedicament}}
                  {{mb_include module="prescription" template="inc_print_medicament" med=$_line print=0 dci=0}}
                {{/if}}
                {{if $_line instanceof CPrescriptionLineMix}}
                  {{mb_include module="prescription" template="inc_print_prescription_line_mix" perf=$_line print=0 dci=0}}
                {{/if}}
                {{if $_line instanceof CPrescriptionLineElement}}
                  {{mb_include module="prescription" template="inc_print_element" elt=$_line}}
                {{/if}}
              {{/foreach}}
            </ul>
          </td>
        </tr>

        {{if $lines_per_op|@count}}
            <tr>
              <th class="category">
                Per-op
              </th>
            </tr>
            {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
            <tr>
              <td>
                <ul>
                  {{foreach from=$lines_per_op item=_line_per_op}}
                    {{if $_line_per_op instanceof CPrescriptionLineMedicament}}
                      {{mb_include module="prescription" template="inc_print_medicament" med=$_line_per_op print=0 dci=0}}
                    {{/if}}
                    {{if $_line_per_op instanceof CPrescriptionLineMix}}
                      {{mb_include module="prescription" template="inc_print_prescription_line_mix" perf=$_line_per_op print=0 dci=0}}
                    {{/if}}
                    {{if $_line_per_op instanceof CPrescriptionLineElement}}
                      {{mb_include module="prescription" template="inc_print_element" elt=$_line_per_op}}
                    {{/if}}
                  {{/foreach}}
                </ul>
              </td>
            </tr>
        {{/if}}

        <tr>
          <th class="category">Techniques complémentaires</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$dossier_anesth->_ref_techniques item=curr_tech}}
              <li>
                {{$curr_tech->technique}}<br />
              </li>
              {{foreachelse}}
              <li>Pas de technique complémentaire prévu</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2"><hr /></td>
  </tr>
  <tr>
    <td>
      <!-- Atcd chirurgicaux / anesthésiques / Biologie-->
      <table width="100%">
        <tr>
          <th class="category">ATCD Chirurgicaux</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('chir', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.chir item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">ATCD Anesthésiques</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('anesth', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.anesth item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Biologie</th>
        </tr>
        <tr>
          <td>
            <table>
              <tr>
                <td style="width: 50%">
                  {{if $dossier_anesth->date_analyse}}
                    {{mb_label object=$dossier_anesth field=date_analyse}} {{mb_value object=$dossier_anesth field=date_analyse}}
                    <br />
                  {{/if}}
                  {{if $dossier_medical->groupe_sanguin!="?" || $dossier_medical->rhesus!="?"}}
                    Groupe sanguin&nbsp;:&nbsp;{{tr}}CDossierMedical.groupe_sanguin.{{$dossier_medical->groupe_sanguin}}{{/tr}}&nbsp;{{tr}}CDossierMedical.rhesus.{{$dossier_medical->rhesus}}{{/tr}}
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->rai && $dossier_anesth->rai!="?"}}
                    RAI&nbsp;:&nbsp;{{tr}}CConsultAnesth.rai.{{$dossier_anesth->rai}}{{/tr}}
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->hb}}
                    Hb&nbsp;:&nbsp;{{$dossier_anesth->hb}}&nbsp;g/dl
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->ht}}
                    Ht&nbsp;:&nbsp;{{$dossier_anesth->ht}}&nbsp;%
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->ht_final}}
                    Ht&nbsp;final&nbsp;:&nbsp;{{$dossier_anesth->ht_final}}&nbsp;%
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->_psa}}
                    PSA&nbsp;final&nbsp;:&nbsp;{{$dossier_anesth->_psa}}&nbsp;ml/mg<br />
                  {{/if}}
                  {{if $dossier_anesth->plaquettes}}
                    Plaquettes&nbsp;:&nbsp;{{$dossier_anesth->plaquettes}}&nbsp;(x1000)&nbsp;/mm3
                  {{/if}}
                </td>
                <td style="width: 50%">
                  {{if $dossier_anesth->creatinine}}
                    Créatinine&nbsp;:&nbsp;{{$dossier_anesth->creatinine}}&nbsp;mg/l
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->_clairance}}
                    Créatinine&nbsp;:&nbsp;{{$dossier_anesth->_clairance}}&nbsp;ml/min
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->na}}
                    Na+&nbsp;:&nbsp;{{$dossier_anesth->na}}&nbsp;mmol/l
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->k}}
                    K+&nbsp;:&nbsp;{{$dossier_anesth->k}}&nbsp;mmol/l<br />
                  {{/if}}
                  {{if $dossier_anesth->tp}}
                    TP&nbsp;final&nbsp;:&nbsp;{{$dossier_anesth->tp}}&nbsp;%
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->tca}}
                    TCA&nbsp:&nbsp;{{$dossier_anesth->tca_temoin}}&nbsp;s&nbsp;/&nbsp;{{$dossier_anesth->tca}}&nbsp;s
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->tsivy && $dossier_anesth->tsivy != "00:00:00"}}
                    TS Ivy&nbsp;:&nbsp;{{$dossier_anesth->tsivy|date_format:"%M min %S s"}}
                    <br />
                  {{/if}}
                  {{if $dossier_anesth->ecbu && $dossier_anesth->ecbu!="?"}}
                    ECBU&nbsp;:&nbsp;{{tr}}CConsultAnesth.ecbu.{{$dossier_anesth->ecbu}}{{/tr}}
                    <br />
                  {{/if}}
                </td>
              </tr>
              {{if $dossier_anesth->result_com}}
                <tr>
                  <td colspan="2">
                    {{mb_label object=$dossier_anesth field=result_com}}&nbsp;:&nbsp;{{$dossier_anesth->result_com}}&nbsp;<br />
                  </td>
                </tr>
              {{/if}}
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td>
      <!-- Atcd Cardio / Uro-Nephro / NeuroPsy / Endoc / Gyneco / Autres -->
      <table width="100%">
        <tr>
          <th class="category">Examen Cardiovasculaire</th>
        </tr>
        <tr>
          <td>
            Tension artérielle :
            {{if $const_med->ta_gauche}}
              {{$const_med->_ta_gauche_systole}} / {{$const_med->_ta_gauche_diastole}} cm Hg
            {{elseif $const_med->ta_droit}}
              {{$const_med->_ta_droit_systole}} / {{$const_med->_ta_droit_diastole}} cm Hg
            {{elseif $const_med->ta}}
              {{$const_med->_ta_systole}} / {{$const_med->_ta_diastole}} cm Hg
            {{else}}
              ?
            {{/if}}
            -
            Pouls :
            {{if $const_med->pouls}}
            {{$const_med->pouls}} / min
            {{else}}
            ?
            {{/if}}
            <br />
            {{$dossier_anesth->examenCardio}}
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('cardio', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.cardio item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th class="category">Examen Pulmonaire</th>
        </tr>
        <tr>
          <td>
            {{$dossier_anesth->examenPulmo}}
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('pulmo', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.pulmo item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th class="category">Examen Digestif</th>
        </tr>
        <tr>
          <td>
            {{$dossier_anesth->examenDigest}}
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('digestif', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.digestif item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        
        {{if $dossier_anesth->examenAutre}}
        <tr>
          <th class="category">Examen Autre</th>
        </tr>
        <tr>
          <td>
            {{$dossier_anesth->examenAutre}}
          </td>
        </tr>
        {{/if}}
        
        <tr>
          <th class="category">Uro-nephrologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('uro', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.uro item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Neuro-psychiatrie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('neuropsy', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.neuropsy item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Endocrinologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('endocrino', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.endocrino item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Gynécologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents_by_type && array_key_exists('gyn', $dossier_medical->_ref_antecedents_by_type)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.gyn item=currAnt}}
              <li> 
                {{if $currAnt->date}}
                  {{mb_value object=$currAnt field=date}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Autres</th>
        </tr>
        <tr>
          <td>
            {{$consult->examen}}
            <ul>
              {{foreach from=$dossier_medical->_ref_antecedents_by_type key=type_name item=curr_type}}
              {{if $type_name != 'alle'
                && $type_name != 'chir'
                && $type_name != 'anesth'
                && $type_name != 'cardio'
                && $type_name != 'uro'
                && $type_name != 'neuropsy'
                && $type_name != 'endocrino'
                && $type_name != 'gyn'}}
                {{foreach from=$curr_type item=currAnt}}
                <li> 
                  {{if $currAnt->date}}
                    {{mb_value object=$currAnt field=date}} :
                  {{/if}}
                  {{$currAnt->rques}} 
                </li>
                {{/foreach}}
              {{/if}}
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{* Non impression de la biologie pour l'instant
  <tr>
    <td colspan="2">
      <!-- Biologie -->
      <table width="100%">
        <tr>
          <th class="category">Biologie</th>
        </tr>
      
      </table>
    </td>
  </tr>
  *}}

  {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
  <tr>
    <td colspan="2">
      <table style="width: 100%">
        <tr>
          <th class="category" colspan="3">Facteurs de risque</th>
        </tr>
        <tr>
          <th class="category">Facteur</th>
          <th class="category">Patient</th>
          <th class="category">Chirurgie</th>
        </tr>
        <tr>
          <th>Maladie thrombo-embolique</th>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical field="risque_thrombo_patient"}}
          </td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_thrombo_chirurgie"}}
          </td> 
        </tr>
        <tr>
          <th>MCJ</th>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical field="risque_MCJ_patient"}}
          </td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_MCJ_chirurgie"}}
          </td> 
        </tr>
        <tr>
          <th>Risque Anesthesique - Antibioprophylaxie</th>
          <td style="text-align: center;">&mdash;</td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_antibioprophylaxie"}}
          </td> 
        </tr>
        <tr>
          <th>Risque Anesthesique - Prophylaxie</th>
          <td style="text-align: center;">&mdash;</td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_prophylaxie"}}
         </td>  
        </tr>
        <tr>
          <th>{{tr}}{{mb_label object=$dossier_medical field="facteurs_risque"}}{{/tr}}</th>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical field="facteurs_risque"}}
          </td>
          <td></td>
        </tr>
     </table>
   </td>
  </tr>
  {{/if}}
  
  <tr>
    <th class="category" colspan="2">Visite de pré-anesthésie {{if $operation->date_visite_anesth}}- {{$operation->date_visite_anesth|date_format:$conf.date}}{{/if}}</th>
  </tr>
  {{if $operation->date_visite_anesth}}
  <tr>
    <td colspan="2">
      <table>
        <tr>
          <th>{{mb_label object=$operation field="prat_visite_anesth_id"}}</th>
          <td>{{mb_value object=$operation field="prat_visite_anesth_id"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field="rques_visite_anesth"}}</th>
          <td>{{mb_value object=$operation field="rques_visite_anesth"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field="autorisation_anesth"}}</th>
          <td>{{mb_value object=$operation field="autorisation_anesth"}}</td>
        </tr>
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <td colspan="2">
      <table>
        <tr>
          <th>{{mb_label object=$operation field="prat_visite_anesth_id"}}</th>
          <td></td>
        </tr>
        <tr style="height: 4em;">
          <th>{{mb_label object=$operation field="rques_visite_anesth"}}</th>
          <td></td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field="autorisation_anesth"}}</th>
          <td>Oui - Non</td>
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>

{{if !@$offline}}
<table class="main">
  <tr>
    <td>
{{/if}}
    