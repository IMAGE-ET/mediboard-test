    </td>
  </tr>
</table>

{{assign var="patient" value=$consult->_ref_patient}}
{{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
{{assign var="sejour" value=$consult_anesth->_ref_operation->_ref_sejour}}
{{assign var="operation" value=$consult_anesth->_ref_operation}}

{{assign var=const_med value=$patient->_ref_constantes_medicales}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=ant value=$dossier_medical->_ref_antecedents}}
<table class="form" id="admission">
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
          <td style="white-space: nowrap;">{{$patient->_age}} ans</td>
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
              {{if $consult_anesth->chir_id}}
                Dr {{$consult_anesth->_ref_chir->_view}} -
              {{/if}}
              {{if $consult_anesth->date_interv}}
                le {{$consult_anesth->date_interv|date_format:"%d/%m/%Y"}}
              {{/if}}
              {{if $consult_anesth->libelle_interv}}
                <em>[{{$consult_anesth->libelle_interv}}]</em>
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
                {{if $curr_examcomp->fait}}
                  (Fait)
                {{else}}
                  (A Faire)
                {{/if}}
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
                  Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
                {{elseif $curr_trmt->debut}}
                  Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
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
					          {{$_line_med->_view}}
					          {{if $_line_med->_ref_prises|@count}}
						          ({{foreach from=$_line_med->_ref_prises item=_prise name=foreach_prise}}
						            {{$_prise->_view}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
						          {{/foreach}})
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
          {{if $dossier_medical->_ref_antecedents && array_key_exists('alle', $dossier_medical->_ref_antecedents)}}
            {{foreach from=$dossier_medical->_ref_antecedents.alle item=currAnt}}
              <ul>
                <li> 
                  {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                    {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
          <th class="category">Code ASA : {{tr}}CConsultAnesth.ASA.{{$consult_anesth->ASA}}{{/tr}}</th>
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
            {{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}
            <br />
            <strong>Ouverture de la bouche :</strong>
            {{tr}}CConsultAnesth.bouche.{{$consult_anesth->bouche}}{{/tr}}
            <br />
            <strong>Distance thyro-mentonière :</strong>
            {{tr}}CConsultAnesth.distThyro.{{$consult_anesth->distThyro}}{{/tr}}
            <br />
            <strong>Etat bucco-dentaire :</strong>
            {{$consult_anesth->etatBucco|nl2br}}
            <br />
            {{if $etatDents}}
              {{$etatDents|nl2br}}
            {{/if}}
            <strong>Conclusion :</strong>
            {{$consult_anesth->conclusion|nl2br}}
            <br />
            {{if $consult_anesth->_intub_difficile}}
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
            {{$consult_anesth->premedication|nl2br}}
          </td>
        </tr>
        <tr>
          <th class="category">Techniques complémentaires</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$consult_anesth->_ref_techniques item=curr_tech}}
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
            {{if $dossier_medical->_ref_antecedents && array_key_exists('chir', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.chir item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
            {{if $dossier_medical->_ref_antecedents && array_key_exists('anesth', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.anesth item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
                  {{if $consult->_ref_consult_anesth->groupe!="?" || $consult->_ref_consult_anesth->rhesus!="?"}}
                    Groupe sanguin&nbsp;:&nbsp;{{tr}}CConsultAnesth.groupe.{{$consult->_ref_consult_anesth->groupe}}{{/tr}}&nbsp;{{tr}}CConsultAnesth.rhesus.{{$consult->_ref_consult_anesth->rhesus}}{{/tr}}
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->rai && $consult->_ref_consult_anesth->rai!="?"}}
                    RAI&nbsp;:&nbsp;{{tr}}CConsultAnesth.rai.{{$consult->_ref_consult_anesth->rai}}{{/tr}}
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->hb}}
                    Hb&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->hb}}&nbsp;g/dl
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->ht}}
                    Ht&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->na}}&nbsp;%
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->ht}}
                    Ht&nbsp;final&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->ht_final}}&nbsp;%
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->_psa}}
                    PSA&nbsp;final&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->_psa}}&nbsp;ml/mg<br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->plaquettes}}
                    Plaquettes&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->plaquettes}}&nbsp;(x1000)&nbsp;/mm3
                  {{/if}}
                </td>
                <td style="width: 50%">
                  {{if $consult->_ref_consult_anesth->creatinine}}
                    Créatinine&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->na}}&nbsp;mg/l
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->_clairance}}
                    Créatinine&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->_clairance}}&nbsp;ml/min
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->na}}
                    Na+&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->na}}&nbsp;mmol/l
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->k}}
                    K+&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->k}}&nbsp;mmol/l<br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->tp}}
                    TP&nbsp;final&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->tp}}&nbsp;%
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->tca}}
                    TCA&nbsp:&nbsp;{{$consult->_ref_consult_anesth->tca_temoin}}&nbsp;s&nbsp;/&nbsp;{{$consult->_ref_consult_anesth->tca}}&nbsp;s
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->tsivy && $consult->_ref_consult_anesth->tsivy != "00:00:00"}}
                    TS Ivy&nbsp;:&nbsp;{{$consult->_ref_consult_anesth->tsivy|date_format:"%M min %S s"}}
                    <br />
                  {{/if}}
                  {{if $consult->_ref_consult_anesth->ecbu && $consult->_ref_consult_anesth->ecbu!="?"}}
                    ECBU&nbsp;:&nbsp;{{tr}}CConsultAnesth.ecbu.{{$consult->_ref_consult_anesth->ecbu}}{{/tr}}
                    <br />
                  {{/if}}
                </td>
              </tr>
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
            {{if $const_med->ta}}
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
            {{$consult_anesth->examenCardio}}
            {{if $dossier_medical->_ref_antecedents && array_key_exists('cardio', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.cardio item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
            {{$consult_anesth->examenPulmo}}
            {{if $dossier_medical->_ref_antecedents && array_key_exists('pulmo', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.pulmo item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Uro-nephrologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('uro', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.uro item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
            {{if $dossier_medical->_ref_antecedents && array_key_exists('neuropsy', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.neuropsy item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
            {{if $dossier_medical->_ref_antecedents && array_key_exists('endocrino', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.endocrino item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
            {{if $dossier_medical->_ref_antecedents && array_key_exists('gyn', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.gyn item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
              {{foreach from=$dossier_medical->_ref_antecedents key=type_name item=curr_type}}
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
                  {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                    {{$currAnt->date|date_format:"%d/%m/%Y"}} :
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
  <!-- Non impression de la biologie pour l'instant
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
  -->
</table>

<table class="main">
  <tr>
    <td>