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
<table class="form" id="admission" style="page-break-after: always;">
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
            {{mb_value object=$sejour field="type"}}
            du {{mb_value object=$sejour field="_entree"}}
            au {{mb_value object=$sejour field="_sortie"}}
          </td>
        </tr>
        <tr>
          <th>Profession</th>
          <td>{{$patient->profession}}</td>
          <th>Poids</th>
          <td style="white-space: nowrap;">{{if $const_med->poids}}{{$const_med->poids}} kg{{else}}-{{/if}}</td>
          <th>Intervention</th>
          <td>Dr {{$operation->_ref_chir->_view}} - le {{mb_value object=$operation->_ref_plageop field="date"}}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <!-- Examens complémentaires / Traitements / Allergies -->
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
              <li>Pas de traitements</li>
              {{/foreach}}
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
      </table>
    </td>
    <td width="50%">
      <!-- Docs édités / Intubation / Prémédication -->
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
            {{tr}}CConsultAnesth.mallampati.{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}
            <br />
            <strong>Ouverture de la bouche :</strong>
            {{tr}}CConsultAnesth.bouche.{{$consult->_ref_consult_anesth->bouche}}{{/tr}}
            <br />
            <strong>Distance thyro-mentonière :</strong>
            {{tr}}CConsultAnesth.distThyro.{{$consult->_ref_consult_anesth->distThyro}}{{/tr}}
            <br />
            <strong>Etat bucco-dentaire :</strong>
            {{$consult->_ref_consult_anesth->etatBucco|nl2br}}
            <br />
            <strong>Conclusion :</strong>
            {{$consult->_ref_consult_anesth->conclusion|nl2br}}
            <br />
            {{if $consult->_ref_consult_anesth->_intub_difficile}}
            <span style="font-weight: bold; text-align:center; color:#F00;">
              Intubation Difficile Prévisible
            </span>
            {{else}}
            <span style="font-weight: bold; text-align:center;">
              Pas Intubation Difficile Prévisible
            </span>        
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Prémédication</th>
        </tr>
        <tr>
          <td>
            {{$consult->_ref_consult_anesth->premedication|nl2br}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table width="100%">
        <tr>
          <th class="category">Antécédents</th>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <!-- Atcd chirurgicaux / anesthésiques -->
      <table width="100%">
        <tr>
          <th class="category">Chirurgicaux</th>
        </tr>
        <tr>
          <th class="category">Anesthésiques</th>
        </tr>
      </table>
    </td>
    <td>
      <!-- Atcd Cardio / Uro-Nephro / NeuroPsy / Endoc / Gyneco / Autres -->
      <table width="100%">
        <tr>
          <th class="category">Cardiovasculaires</th>
        </tr>
        <tr>
          <th class="category">Uro-nephrologiques</th>
        </tr>
        <tr>
          <th class="category">Neuro-psychiatriques</th>
        </tr>
        <tr>
          <th class="category">Endocrinologiques</th>
        </tr>
        <tr>
          <th class="category">Gynécologiques</th>
        </tr>
        <tr>
          <th class="category">Autres</th>
        </tr>
      </table>
    </td>
  </tr>
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
</table>

<table class="main">
  <tr>
    <td>