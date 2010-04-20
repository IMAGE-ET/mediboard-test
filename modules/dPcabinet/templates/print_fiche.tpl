{{if !@$offline}}
{{if $print}}
	<script type="text/javascript">
	Main.add(window.print);
	</script> 
{{/if}}

  </td>
  </tr>
</table>

{{assign var=tbl_class value="print"}}
{{else}}
{{assign var=tbl_class value="main form"}}
{{/if}}

{{assign var="patient" value=$consult->_ref_patient}}
{{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}  
{{assign var="operation" value=$consult_anesth->_ref_operation}}
  
{{mb_include module=dPcabinet template=inc_header_fiche_anesth}}

<table class="{{$tbl_class}}" style="page-break-after: always;">
  <tr>
    <td colspan="2">
      <table width="100%">
				<tr>
					<th class="category" colspan="2">Intervention</th>
				</tr>
				<tr>
          <td colspan="2">
            {{if $consult_anesth->operation_id}}
              {{if $consult_anesth->_ref_operation->_ref_sejour}}
              Admission en {{tr}}CSejour.type.{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}
              le <strong>{{$consult_anesth->_ref_operation->_ref_sejour->_entree|date_format:"%A %d/%m/%Y à %Hh%M"}}</strong>
              pour <strong>{{$consult_anesth->_ref_operation->_ref_sejour->_duree_prevue}} nuit(s)</strong>
              <br />
              {{/if}}
            {{else}}
              Intervention le <strong>{{$consult_anesth->date_interv|date_format:"%A %d/%m/%Y"}}</strong>
              par le <strong>Dr {{$consult_anesth->_ref_chir->_view}}</strong>
              <br />
              {{$consult_anesth->libelle_interv}}
            {{/if}}
          </td>
        </tr>
				{{if $consult_anesth->operation_id}}
				<tr>
          <td colspan="2">
            Intervention le <strong>{{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%A %d/%m/%Y"}}</strong>
            {{if $consult_anesth->_ref_operation->libelle}}
              - {{$consult_anesth->_ref_operation->libelle}}
            {{/if}}
            <ul>
              {{if $consult_anesth->_ref_operation->libelle}}
                <li><em>[{{$consult_anesth->_ref_operation->libelle}}]</em></li>
              {{/if}}
              {{foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}}
              <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}}) (coté {{tr}}COperation.cote.{{$consult_anesth->_ref_operation->cote}}{{/tr}})</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
				{{/if}}
        <tr>
          <td class="halfPane">
            {{if $consult_anesth->operation_id}}
	            <table>
	              <tr>
	                <th style="font-weight: normal;">Anesthésie prévue</th>
	                <td style="font-weight: bold;">
	                  {{$consult_anesth->_ref_operation->_lu_type_anesth}}
	                </td>
	              </tr>
	              <tr>
	                <th style="font-weight: normal;">Position</th>
	                <td style="font-weight: bold;">
	                  {{tr}}CConsultAnesth.position.{{$consult_anesth->position}}{{/tr}}
	                </td>
	              </tr>
	            </table>
            {{elseif $consult_anesth->position}}
            Position : <strong>{{tr}}CConsultAnesth.position.{{$consult_anesth->position}}{{/tr}}</strong>
            {{/if}}
          </td>
          <td class="halfPane">
            <strong>Techniques Complémentaires</strong>
            <ul>
              {{foreach from=$consult_anesth->_ref_techniques item=curr_tech}}
              <li>
                {{$curr_tech->technique}}
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
  
  {{assign var=const_med value=$patient->_ref_constantes_medicales}}
  {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
  {{assign var=ant value=$dossier_medical->_ref_antecedents}}
  {{if !$ant}}
    {{assign var=no_alle value=0}}
  {{else}}
    {{assign var=no_alle value=$ant&&!array_key_exists("alle",$ant)}}
  {{/if}}
  <tr>
    <td class="halfPane" {{if $no_alle}}colspan="2"{{/if}}>
      <table width="100%">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
        </tr>
        <tr>
          <td colspan="2">{{$patient->_view}}</td>
        </tr>
        {{if $patient->nom_jeune_fille}}
        <tr>
          <th>Nom de jeune fille</th>
          <td>{{$patient->nom_jeune_fille}}</td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2">
            Né{{if $patient->sexe != "m"}}e{{/if}} le {{mb_value object=$patient field=naissance}}
            ({{$patient->_age}} ans)
            - sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}<br />
            {{if $patient->profession}}Profession : {{$patient->profession}}<br />{{/if}} 
            {{if $const_med->poids}}<strong>{{$const_med->poids}} kg</strong> - {{/if}}
            {{if $const_med->taille}}<strong>{{$const_med->taille}} cm</strong> - {{/if}}
            {{if $const_med->_imc}}IMC : <strong>{{$const_med->_imc}}</strong>
              {{if $const_med->_imc_valeur}}({{$const_med->_imc_valeur}}){{/if}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <table>
              {{if $consult->_ref_consult_anesth->groupe!="?" || $consult->_ref_consult_anesth->rhesus!="?"}}
              <tr>
                <th style="font-weight: normal;">Groupe sanguin</th>
                <td style="font-weight: bold; white-space: nowrap; font-size:130%;">&nbsp;{{tr}}CConsultAnesth.groupe.{{$consult->_ref_consult_anesth->groupe}}{{/tr}} &nbsp;{{tr}}CConsultAnesth.rhesus.{{$consult->_ref_consult_anesth->rhesus}}{{/tr}}</td>
              </tr>
              {{/if}}
              {{if $consult->_ref_consult_anesth->rai && $consult->_ref_consult_anesth->rai!="?"}}
              <tr>
                <th style="font-weight: normal;">RAI</th>
                <td style="font-weight: bold; white-space: nowrap; font-size:130%;">&nbsp;{{tr}}CConsultAnesth.rai.{{$consult->_ref_consult_anesth->rai}}{{/tr}}</td>
              </tr>
              {{/if}}
              <tr>
                <th style="font-weight: normal;">ASA</th>
                <td style="font-weight: bold;">{{tr}}CConsultAnesth.ASA.{{$consult_anesth->ASA}}{{/tr}}</td>
              </tr>
              <tr>
                <th style="font-weight: normal;">VST</th>
                <td style="font-weight: bold; white-space: nowrap;">
                  {{if $const_med->_vst}}{{$const_med->_vst}} ml{{/if}}
                </td>
              </tr>
              {{if $consult->_ref_consult_anesth->_psa}}
              <tr>
                <th style="font-weight: normal;">PSA</th>
                <td style="font-weight: bold; white-space: nowrap;">
                  {{$consult->_ref_consult_anesth->_psa}} ml/GR
                </td>
                <td colspan="2"></td>
              </tr>
              {{/if}}
            </table>
          </td>
        </tr>
      </table>
    </td>
    {{if !$no_alle}}
    <td class="halfPane">
      <table width="100%">
        <tr>
          <th class="category" colspan="2">Allergies</th>
        </tr>
        <tr>
          <td style="font-weight: bold; white-space: normal; font-size:130%;">
          {{if $dossier_medical->_ref_antecedents}}
            {{foreach from=$dossier_medical->_ref_antecedents.alle item=currAnt}}
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
      </table>
    </td>
    {{/if}}
  </tr>
  <tr>
    <td class="halfPane" rowspan="2">
      <table width="100%">
        <tr>
          <th class="category">Antécédents</th>
        </tr>
        <tr>
          <td>
          {{if $dossier_medical->_ref_antecedents}}
            {{foreach from=$dossier_medical->_ref_antecedents key=keyAnt item=currTypeAnt}}
              {{if $currTypeAnt}}
                <strong>{{tr}}CAntecedent.type.{{$keyAnt}}{{/tr}}</strong>
                {{foreach from=$currTypeAnt item=currAnt}}
                <ul>
                  <li> 
									  {{if $currAnt->appareil}}<strong>{{tr}}CAntecedent.appareil.{{$currAnt->appareil}}{{/tr}}</strong>{{/if}} 
                    {{if $currAnt->date}}
                      {{mb_value object=$currAnt field=date}} :
                    {{/if}}
                    {{$currAnt->rques}} 
                  </li>
                </ul>
                {{/foreach}}
              {{/if}}
            {{/foreach}}
          {{else}}
            <ul>
            <li>Pas d'antécédents</li>
            </ul>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  	
    {{if is_array($dossier_medical->_ref_traitements) || $dossier_medical->_ref_prescription}}
    <!-- Traitements -->
    <td class="halfPane">
      <table width="100%">
        <tr>
          <th class="category">Traitements</th>
        </tr>
        {{if is_array($dossier_medical->_ref_traitements)}}
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
        {{/if}}
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
      </table>
    </td>
    {{/if}}
    </tr>
    <tr>
    
    <!-- Examens cliniques -->
    <td class="halfPane">
      <table width="100%">
        <tr>
          <th class="category" colspan="6">Examens Clinique</th>
        </tr>
        <tr>
          <th style="font-weight: normal;">Pouls</th>
          <td style="font-weight: bold; white-space: nowrap;">
            {{if $const_med->pouls}}
            {{$const_med->pouls}} / min
            {{else}}
            ?
            {{/if}}
          </td>
          <th style="font-weight: normal;">TA</th>
          <td style="font-weight: bold; white-space: nowrap;">
            {{if $const_med->ta}}
              {{$const_med->_ta_systole}} / {{$const_med->_ta_diastole}} cm Hg
            {{else}}
            ?
            {{/if}}
          </td>
          <th style="font-weight: normal;">Spo2</th>
          <td style="font-weight: bold; white-space: nowrap;">
            {{if $const_med->spo2}}
            {{$const_med->spo2}} %
            {{else}}
            ?
            {{/if}}
          </td>
        </tr>
				
				<tr>
          <th style="font-weight: normal;">Examen cardiovasculaire</th>
          <td style="font-weight: bold;" class="text">{{$consult->_ref_consult_anesth->examenCardio}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Examen pulmonaire</th>
          <td style="font-weight: bold;" class="text">{{$consult->_ref_consult_anesth->examenPulmo}}</td>
        </tr>
				
        {{if $consult->examen}}
        <tr>
          <th style="font-weight: normal;">Examens</th>
          <td style="font-weight: bold;" colspan="5" class="text">{{$consult->examen|nl2br}}</td>
        </tr>
        {{/if}}
      </table>
    </td>
    
  </tr>
</table>

{{mb_include module=dPcabinet template=inc_header_fiche_anesth}}

<table class="{{$tbl_class}}">
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th colspan="3" class="category">Conditions d'intubation</th>
        </tr>
    
          
        {{if !$dPconfig.dPcabinet.CConsultAnesth.show_mallampati}}
				<tr>
					<th style="font-weight: normal;">Mallampati</th>
          <td style="font-weight: bold;">
           {{mb_value object=$consult->_ref_consult_anesth field="mallampati"}}
          </td>
				</tr>	
				{{/if}}
        <tr>
        	{{if $consult->_ref_consult_anesth->mallampati && $dPconfig.dPcabinet.CConsultAnesth.show_mallampati}}
          <td rowspan="4" class="button" style="white-space: nowrap;">
            <img src="images/pictures/{{$consult->_ref_consult_anesth->mallampati}}.png" alt="{{tr}}CConsultAnesth.mallampati.{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}" />
            <br />Mallampati<br />de {{tr}}CConsultAnesth.mallampati.{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}
          </td>
          {{/if}}
				  <th style="font-weight: normal;">Ouverture de la bouche</th>
          <td style="font-weight: bold;">
            {{tr}}CConsultAnesth.bouche.{{$consult->_ref_consult_anesth->bouche}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Distance thyro-mentonnière</th>
          <td style="font-weight: bold;">{{tr}}CConsultAnesth.distThyro.{{$consult->_ref_consult_anesth->distThyro}}{{/tr}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Etat bucco-dentaire</th>
          <td style="font-weight: bold;" class="text">{{$consult->_ref_consult_anesth->etatBucco}}
				  <br />
	          {{if $etatDents}}
	            {{$etatDents|nl2br}}
	          {{/if}}
					</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Conclusion</th>
          <td style="font-weight: bold;" class="text">{{$consult->_ref_consult_anesth->conclusion}}</td>
        </tr>
        <tr>
        {{if $consult->_ref_consult_anesth->_intub_difficile}}
          <td colspan="3" style="font-weight: bold; text-align:center; color:#F00;">
            Intubation difficile prévisible
          </td>
        {{else}}
          <td colspan="3" style="font-weight: bold; text-align:center;">
            Pas d'intubation difficile prévisible
          </td>        
        {{/if}}
        </tr>
      </table>    

      <table width="100%">
        <tr>
          <th class="category" colspan="3">
            Examens Complémentaires
          </th>
        </tr>
        
        <tr>
        {{foreach from=$listChamps item=aChamps}}
          <td>
            <table>
            {{foreach from=$aChamps item=champ}}
              {{assign var="donnees" value=$unites.$champ}}
              <tr>
                <th style="font-weight: normal;">{{$donnees.nom}}</th>
                <td style="font-weight: bold; white-space: nowrap;">
                  {{if $champ=="tca"}}
                    {{$consult->_ref_consult_anesth->tca_temoin}} s / {{$consult->_ref_consult_anesth->tca}}
                  {{elseif $champ=="tsivy"}}
                    {{$consult->_ref_consult_anesth->tsivy|date_format:"%Mm%Ss"}}
                  {{elseif $champ=="ecbu"}}
                    {{tr}}CConsultAnesth.ecbu.{{$consult->_ref_consult_anesth->ecbu}}{{/tr}}
                  {{elseif $champ == "date_analyse"}}
									  {{mb_value object=$consult->_ref_consult_anesth field=date_analyse}}
									{{else}}
                    {{$consult->_ref_consult_anesth->$champ}}
                  {{/if}}
                  {{$donnees.unit}}
                </td>
              </tr>
            {{/foreach}}
            </table>
          </td>
        {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%">
        {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
        {{if $list_exams|@count}}
        <tr>
          <th>
            Examens Complémentaires : {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
          </th>
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
      </table>
      
      <table width="100%">
      {{if $consult->_ref_exampossum->_id}}
        <tr>
          <th>Score Possum</th>
          <td>
            Morbidité : {{mb_value object=$consult->_ref_exampossum field="_morbidite"}}%<br />
            Mortalité : {{mb_value object=$consult->_ref_exampossum field="_mortalite"}}%
          </td>
        </tr>
      {{/if}}
      
      {{if $consult->_ref_examnyha->_id}}
        <tr>
          <th>Clasification NYHA</th>
          <td>{{mb_value object=$consult->_ref_examnyha field="_classeNyha"}}</td>
        </tr>   
      {{/if}}
      
      {{if $consult->rques}}
        <tr>
          <th>
            Remarques
          </th>
          <td>
            {{$consult->rques|nl2br}}
          </td>
        </tr>
      {{/if}}
      </table>

      <table width="100%" style="padding-bottom: 10px;">
        <tr>
          <th class="category">
            Liste des Documents Edités
          </th>
        </tr>
        <tr>
          <td>
            <ul>
            {{foreach from=$consult->_ref_documents item=currDoc}}
              <li>{{$currDoc->nom}}<br />
            {{foreachelse}}
            Aucun Document
            {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
      
      {{if $consult->_ref_consult_anesth->premedication}}
      <table width="100%" style="padding-bottom: 10px;">
        <tr>
          <th class="category">
            Prémédication
          </th>
        </tr>
        <tr>
          <td>
            {{$consult->_ref_consult_anesth->premedication|nl2br}}
          </td>
        </tr>
      </table>
      {{/if}}
      
      {{if $consult->_ref_consult_anesth->prepa_preop}}
      <table width="100%">
        <tr>
          <th class="category">
            Préparation pré-opératoire
          </th>
        </tr>
        <tr>
          <td>
            {{$consult->_ref_consult_anesth->prepa_preop|nl2br}}
          </td>
        </tr>
      </table>
      {{/if}}
      
      {{if $dossier_medical->_ext_codes_cim}}
      <table width="100%">
        <tr>
          <th class="category">Diagnostics PMSI du patient</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
              <li>
                {{$curr_code->code}}: {{$curr_code->libelle}}
              </li>
              {{foreachelse}}
              <li>Pas de diagnostic</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>

  {{if $dPconfig.dPcabinet.CConsultAnesth.show_facteurs_risque}}
  <tr>
  	<td>
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
     </table>
	 </td>
	</tr>
  {{/if}}
	
	<tr>
		<th class="category">Visite de pré-anesthésie {{if $operation->date_visite_anesth}}- {{$operation->date_visite_anesth|date_format:$dPconfig.datetime}}{{/if}}</th>
	</tr>
	{{if $operation->date_visite_anesth}}
	<tr>
		<td>
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
    <td>
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