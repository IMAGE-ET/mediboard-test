{{assign var="patient" value=$consult->_ref_patient}}
{{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
    </td>
  </tr>
</table>
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%; padding-bottom: 20px;">
        <tr>
          <th class="title" colspan="4">
            <a href="javascript:window.print()">
              Consultation pré-anesthésique
            </a>
          </th>
        </tr>
        
        <tr>
          <th>Date </th>
          <td>{{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}}</td>
          <th>Anesthésiste </th>
          <td>Dr. {{$consult->_ref_chir->_view}}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="form" id="admission">
  <tr>
    <td class="halfPane">        
      <table width="100%" style="font-size: 100%; padding-bottom: 20px;">        
        <tr>
          <th class="category" colspan="2">
            Informations sur le patient
          </th>
        </tr>
        <tr>
          <th>Nom / Prenom</th>
          <td>{{$patient->_view}}</td>
        </tr>
        {{if $patient->nom_jeune_fille}}
        <tr>
          <th>Nom de jeune fille</th>
          <td>{{$patient->nom_jeune_fille}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>Date de naissance</th>
          <td>
            {{$patient->_jour}}/{{$patient->_mois}}/{{$patient->_annee}}
            <br />({{$patient->_age}} ans)
          </td>
        </tr>
        <tr>
          <th>Sexe</th>
          <td>
            {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}
          </td>
        </tr>
        <tr>
          <th>Tabac</th>
          <td>
            {{$consult->_ref_consult_anesth->tabac}}
          </td>
        </tr>
        <tr>
          <th>Oenolisme</th>
          <td>
            {{$consult->_ref_consult_anesth->oenolisme}}
          </td>
        </tr>    
        <tr>
          <td colspan="2">
            <table style="font-size: 100%;"> 
              <tr>
                <th width="25%">Poids</th>
                <td style="white-space: nowrap;">{{$consult->_ref_consult_anesth->poid}} kg</td>
                <th>IMC</th>
                <td>{{$consult->_ref_consult_anesth->_imc}}</td>
              </tr>
              <tr>
                <th>Taille</th>
                <td style="white-space: nowrap;">{{$consult->_ref_consult_anesth->taille}} cm</td>
                <th>TA</th>
                <td style="white-space: nowrap;">{{$consult->_ref_consult_anesth->tasys}} / {{$consult->_ref_consult_anesth->tadias}}</td>
              </tr> 
              <tr>
                <th>Groupe sanguin</th>
                <td style="white-space: nowrap;">{{$consult->_ref_consult_anesth->groupe}} {{$consult->_ref_consult_anesth->rhesus}}</td>
                <th>Transfusions</th>
                <td>{{$consult->_ref_consult_anesth->transfusions}}</td>
              </tr> 
              <tr>
                <th>VST</th>
                <td style="white-space: nowrap;">{{$consult->_ref_consult_anesth->_vst}} ml</td>
                <th>ASA</th>
                <td>{{$consult_anesth->ASA}}</td>
              </tr>
              <tr>
                <th>PSA</th>
                <td style="white-space: nowrap;">{{$consult->_ref_consult_anesth->_psa}} ml/GR</td>
                <td colspan="2"></td>
              </tr>
            </table>
          </td>
        </tr> 
      </table>
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%; padding-bottom: 10px;">
        <tr>
          <th class="category" colspan="2">
            Intervention
          </th>
        </tr>
        {{if $consult_anesth->operation_id}}
        <tr>
          <td colspan="2">
            Intervention le <strong>{{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}}</strong>
            <ul>
              {{foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}}
              <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}}) (coté {{$consult_anesth->_ref_operation->cote}})</li>
              {{/foreach}}
            </ul>
            <br />par le <strong>Dr. {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong><br />
            Position : {{tr}}{{$consult_anesth->position}}{{/tr}}<br />
            Admission : {{tr}}{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}{{if $consult_anesth->_ref_operation->_ref_sejour->type=="comp"}} {{$consult_anesth->_ref_operation->_ref_sejour->_duree_prevue}} jour(s){{/if}}
          </td>
        </tr>
        </table>
        <table width="100%" style="font-size: 100%; padding-bottom: 20px;">
        <tr>
          <td class="text"><strong>Anesthésie prévue</strong></td>
          <td class="text">{{$consult_anesth->_ref_operation->_lu_type_anesth}}</td>
        </tr>
        <tr>
          <td colspan="2"><strong>Techniques Complémentaires</strong></td>
        </tr>
        <tr>
          <td colspan="2">
            <ul>
              {{foreach from=$consult_anesth->_ref_techniques item=curr_tech}}
              <li>
                {{$curr_tech->technique}}
              </li>
              {{foreachelse}}
              <li>Pas de technique complémentaire</li>
              {{/foreach}}
            </ul>
        {{else}}
        <tr>
          <td colspan="2">Aucune Intervention</td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>  
    <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category">
            Antécédents / Traitements
          </th>
        </tr>
        <tr>
          <td>
            <strong>Diagnostics du patient</strong>
            <ul>
              {{foreach from=$patient->_codes_cim10 item=curr_code}}
              <li>
                {{$curr_code->code}}: {{$curr_code->libelle}}
              </li>
              {{foreachelse}}
              <li>Pas de diagnostic</li>
              {{/foreach}}
            </ul>
            <strong>Antécédents du patient</strong>
            <ul>
              {{assign var="antecedentGroup" value=""}}
              {{foreach from=$patient->_ref_antecedents item=curr_ant}}
                {{if $antecedentGroup!=$curr_ant->type}}
                  {{if $antecedentGroup!=""}}
                    </li>
                  {{/if}}
                  <li>
                  {{$curr_ant->type}}
                  {{assign var="antecedentGroup" value=$curr_ant->type}}
                {{/if}}
              <ul><li>  
                {{if $curr_ant->date|date_format:"%d/%m/%Y"}}
                  {{$curr_ant->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                <i>{{$curr_ant->rques}}</i>
              </li></ul>
              {{foreachelse}}
              <li>Pas d'antécédents</li>
              {{/foreach}}
            </ul>
            <strong>Traitements du patient</strong>
            <ul>
              {{foreach from=$patient->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}}
                {{else}}
                  Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}}
                {{/if}}
                : <i>{{$curr_trmt->traitement}}</i>
              </li>
              {{foreachelse}}
              <li>Pas de traitements</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;;">
        <tr>
          <th class="category">
            Conditions d'intubation
          </th>
        </tr>
        <tr>
          <td>
             <table width="100%" style="font-size: 100%;">
               <tr>
               {{if $consult->_ref_consult_anesth->mallampati}}
                 <td rowspan="4" class="button">
                   <img src="modules/{{$m}}/images/mallampati/{{$consult->_ref_consult_anesth->mallampati}}.gif" alt="{{tr}}{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}" />
                   <br />Mallampati<br />de {{tr}}{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}
                 </td>
               {{/if}}
                 <th>Ouverture de<br />la bouche</th>
                 <td>{{tr}}{{$consult->_ref_consult_anesth->bouche}}{{/tr}}</td>
               </tr>
               <tr>
                 <th>Distance<br />thyro-mentonnière</th>
                 <td>{{tr}}{{$consult->_ref_consult_anesth->distThyro}}{{/tr}}</td>
               </tr>
               <tr>
                 <th>Etat<br />bucco-dentaire</th>
                 <td class="text">{{$consult->_ref_consult_anesth->etatBucco}}</td>
               </tr>
               <tr>
                 <th>Conclusion</th>
                 <td class="text">{{$consult->_ref_consult_anesth->conclusion}}</td>
               </tr>
               {{if $consult->_ref_consult_anesth->_intub_difficile}}
               <tr>
                 <td colspan="3" style="text-align:center;color:#F00;"><strong>Intubation Difficile Prévisible</strong></td>
               </tr>
               {{/if}}
             </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>  
</table>
<br style="page-break-after: always;" />
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%; padding-bottom: 20px;">
        <tr>
          <th class="title" colspan="4">
            <a href="javascript:window.print()">
              Consultation pré-anesthésique
            </a>
          </th>
        </tr>
        
        <tr>
          <th>Date </th>
          <td>{{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}}</td>
          <th>Anesthésiste </th>
          <td>Dr. {{$consult->_ref_chir->_view}}</td>
        </tr>
        <tr>
          <th>Patient </th>
          <td>{{$patient->_view}}</td>
          <td colspan="2"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 100%; padding-bottom: 20px;">
        <tr>
          <th class="category" colspan="6">
            Examens Complémentaires
          </th>
        </tr>
        
        <tr>
          <th>Pouls</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->pouls}}
          </td>
          <th>RAI</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->rai}}
          </td>
          <th>Créatinine</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->creatinine}} mg/l
          </td>
        </tr>
        <tr>
          <th>Hb</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->hb}} g/dl
          </td>
          <th>
            Spo2
          </th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->spo2}}
          </td>
          <th>
            Clairance de Créatinine
          </th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->_clairance}} mg/l
          </td>
        </tr>
        <tr>
          <th>TP</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tp}} %
          </td>
          <th>Ht</th>
          <td>
            {{$consult->_ref_consult_anesth->ht}} %
          </td>
          <th>Plaquettes</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->plaquettes}}
          </td>
        </tr>
        <tr>
          <th>Na+</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->na}} mmol/l
          </td>
          <th>K+</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->k}} mmol/l
          </td>
          <th>ECBU</th>
          <td>
            {{$consult->_ref_consult_anesth->ecbu}}
          </td>
        </tr>
        <tr>
          <th>TCA</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tca|date_format:"%Mm%Ss"}}
          </td>
          <th>TS Ivy</th>
          <td style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tsivy|date_format:"%Mm%Ss"}}
          </td>
          <th>Detail ECBU</th>
          <td class="text">
            {{$consult->_ref_consult_anesth->ecbu_detail}}<br />
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" style="font-size: 100%; padding-bottom: 20px;">
        <tr>
          <th>
            Examens Complémentaires
          </th>
          <td>
            <ul>
              {{foreach from=$consult->_ref_examcomp item=curr_examcomp}}
              <li>
                {{$curr_examcomp->examen}} 
                {{if $curr_examcomp->fait}}
                  (Fait)
                {{else}}
                  (A Faire)
                {{/if}}
              </li>
              {{foreachelse}}
              <li>Pas d'examen complémentaire</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" style="font-size: 100%; padding-bottom: 20px;">
        <tr>
          <th class="category" colspan="6">
            Visite pré-anesthésique
          </th>
        </tr>
        <tr>
          <td class="text">
            {{$consult->rques|nl2br}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table class="main">
  <tr>
    <td>