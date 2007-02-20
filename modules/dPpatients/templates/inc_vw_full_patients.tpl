      <table class="form">
        <tr id="mainInfo-trigger">
          <th class="title" colspan="4">
            {{$patient->_view}} ({{$patient->_age}} ans)
          </th>
        </tr>

        <tbody class="patientEffect" style="display: none" id="mainInfo">
        <tr>
          <th class="category" colspan="2">
            <a style="float:right;" href="#" onclick="view_history_patient({{$patient->patient_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Identité
          </th>
          <th class="category" colspan="2">
            {{if $patient->_canRead}}
            <div style="float:right;">
              <a href="#" onclick="setObject( {
                objClass: 'CPatient', 
                keywords: '', 
                id: {{$patient->patient_id|smarty:nodefaults|JSAttribute}}, 
                view: '{{$patient->_view|smarty:nodefaults|JSAttribute}}' })"
                title="{{$patient->_nb_files_docs}} doc(s)">
                {{$patient->_nb_files_docs}}
                <img align="top" src="images/icons/{{if !$patient->_nb_files_docs}}next_red.png{{else}}next.png{{/if}}" title="{{$patient->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />                
              </a>
            </div>
            {{/if}} 
            Coordonnées
          </th>
        </tr>
        <tr>
          <th>Nom</th>
          <td>{{$patient->nom}}</td>
          <th>Adresse</th>
          <td class="text">{{$patient->adresse|nl2br}}</td>
        </tr>
        <tr>
          <th>Prénom</th>
          <td>{{$patient->prenom}}</td>
          <th>Code Postal</th>
          <td>{{$patient->cp}}</td>
        </tr>
        <tr>
          <th>Nom de naissance</th>
          <td>{{$patient->nom_jeune_fille}}</td>
          <th>Ville</th>
          <td>{{$patient->ville}}</td>
        </tr>
        <tr>
          <th>Date de naissance</th>
          <td>{{$patient->_naissance}}</td>
          <th>Téléphone</th>
          <td>{{$patient->_tel1}} {{$patient->_tel2}} {{$patient->_tel3}} {{$patient->_tel4}} {{$patient->_tel5}}</td>
        </tr>
        <tr>
          <th>Sexe</th>
          <td>
            {{tr}}CPatient.sexe.{{$patient->sexe}}{{/tr}}
          </td>
          <th>Portable</th>
          <td>{{$patient->_tel21}} {{$patient->_tel22}} {{$patient->_tel23}} {{$patient->_tel24}} {{$patient->_tel25}}</td>
        </tr>
        {{if $patient->rques}}
        <tr>
          <th class="category" colspan="4">Remarques</th>
        </tr>
        <tr>
          <td colspan="4" class="text">{{$patient->rques|nl2br}}</td>
        </tr>
        {{/if}}
        <tr>
          <th colspan="2" class="category">Infos médicales</th>
          <th colspan="2" class="category">Médecins</th>
        </tr>
        <tr>
          <th>N° SS</th>
          <td>{{$patient->matricule}}</td>
          <th>Traitant</th>
          <td>
            {{if $patient->medecin_traitant}}
            Dr. {{$patient->_ref_medecin_traitant->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Régime de santé</th>
          <td>{{$patient->regime_sante}}</td>
          <th rowspan="3">Correspondants</th>
          <td>
            {{if $patient->medecin1}}
            Dr. {{$patient->_ref_medecin1->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>CMU</th>
          <td>
            {{if $patient->cmu}}
            jusqu'au {{$patient->cmu|date_format:"%d/%m/%Y"}}
            {{else}}
            -
            {{/if}}
          </td>
          <td>
            {{if $patient->medecin2}}
            Dr. {{$patient->_ref_medecin2->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>ALD</th>
          <td>
            {{if $patient->ald}}
            {{$patient->ald|nl2br}}
            {{else}}
            -
            {{/if}}
          </td>
          <td>
            {{if $patient->medecin3}}
            Dr. {{$patient->_ref_medecin3->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            <form name="actionPat" action="./index.php" method="get">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="tab" value="vw_idx_patients" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
            <button type="button" class="print" onclick="printPatient({{$patient->patient_id}})">
              Imprimer
            </button>
            {{if $canEdit}}
            <button type="button" class="modify" onclick="editPatient()">
              Modifier
            </button>
            {{/if}}
            </form>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="4">Planifier</th>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <a class="buttonnew" href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
              Intervention
            </a>
            <a class="buttonnew" href="index.php?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
              Urgence
            </a>
            <a class="buttonnew" href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->patient_id}}&amp;sejour_id=0">
              Séjour
            </a>
            <a class="buttonnew" href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
              Consultation
            </a>
          </td>
        </tr>
        {{if $listPrat|@count && $canEditCabinet}}
        <tr><th class="category" colspan="4">Consultation immédiate</th></tr>
        <tr>
          <td class="button" colspan="4">
            <form name="addConsFrm" action="index.php?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_consult_now" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="patient_id" title="notNull refMandatory" value="{{$patient->patient_id}}" />
            <label for="prat_id" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>
            <select name="prat_id" title="notNull refMandatory">
              <option value="">&mdash; Choisir un praticien</option>
              {{foreach from=$listPrat item=curr_prat}}
                <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
                  {{$curr_prat->_view}}
                </option>
              {{/foreach}}
            </select>
            <button class="new" type="submit">Consulter</button>
            </form>
          </td>
        </tr>
  		{{/if}}
        </tbody>
      </table>
      

      <table class="form">

		<!-- Antécédants -->
        <tr id="antecedents-trigger">
          <th colspan="2" class="title">{{$patient->_ref_antecedents|@count}} antécédent(s)</th>
        </tr>
        
        <tbody class="patientEffect" style="display: none" id="antecedents">
          {{foreach from=$patient->_ref_types_antecedent key=curr_type item=list_antecedent}}
          <tr>
            <th class="category" colspan="2">{{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}</th>
          </tr>
          {{foreach from=$list_antecedent item=curr_antecedent}}
          <tr>
            <td class="text" colspan="2">
              {{if $curr_antecedent->date}}
                {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
              {{/if}}
              {{$curr_antecedent->rques|nl2br}}
            </td>
          </tr>
          {{/foreach}}
          {{foreachelse}}
          <tr><td colspan="2"><em>Pas d'antécédents</em></td></tr>
          {{/foreach}}
        </tbody>
        
		<!-- Traitements -->
        <tr id="traitements-trigger">
          <th colspan="2" class="title">{{$patient->_ref_traitements|@count}} traitement(s)</th>
        </tr>
        
        <tbody class="patientEffect" style="display: none" id="traitements">
          {{foreach from=$patient->_ref_traitements item=curr_traitement}}
          <tr>
            <td class="text" colspan="2">
              {{if $curr_traitement->fin}}
              Du {{$curr_traitement->debut|date_format:"%d/%m/%Y"}}
              au {{$curr_traitement->fin|date_format:"%d/%m/%Y"}} :
              {{elseif $curr_traitement->debut}}
              Depuis le {{$curr_traitement->debut|date_format:"%d/%m/%Y"}} :
              {{/if}}
              {{$curr_traitement->traitement}}
            </td>
          </tr>
          {{foreachelse}}
          <tr><td colspan="2"><em>Pas de traitements</em></td></tr>
          {{/foreach}}
        </tbody>
        
		<!-- Diagnostics -->
        <tr id="diagnostics-trigger">
          <th colspan="2" class="title">{{$patient->_codes_cim10|@count}} diagnostic(s)</th>
        </tr>
        
        <tbody class="patientEffect" style="display: none" id="diagnostics">
          {{foreach from=$patient->_codes_cim10 item=curr_code}}
          <tr>
            <td class="text" colspan="2">
              {{$curr_code->code}}: {{$curr_code->libelle}}
             </td>
          </tr>
          {{foreachelse}}
          <tr><td colspan="2"><em>Pas de diagnostics</em></td></tr>
          {{/foreach}}
        </tbody>
        
		<!-- Séjours -->
        <tr id="sejours-trigger">
          <th colspan="2" class="title">{{$patient->_ref_sejours|@count}} séjour(s)</th>
        </tr>
        
        <tbody class="patientEffect" style="display: none" id="sejours">
        {{foreach from=$patient->_ref_sejours item=curr_sejour}}
        <tr>
          <td>
            <a title="Modifier le séjour" href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
              <img src="images/icons/edit.png" alt="Planifier"/>
            </a>
            <a href="#"
              onmouseover="viewItem('CSejour', {{$curr_sejour->sejour_id}})"
              onmouseout="hideItem('CSejour', {{$curr_sejour->sejour_id}})"
              onclick="viewCompleteItem('CSejour', {{$curr_sejour->_id}})">
              Du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
              au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
              - Dr. {{$curr_sejour->_ref_praticien->_view}}
            </a>
            <div id="CSejour{{$curr_sejour->sejour_id}}" class="tooltip" style="display: none;">
            </div>
          </td>
          <td style="text-align:right;">
          {{if $curr_sejour->_canRead}}
            <a href="#" onclick="setObject( {
              objClass: 'CSejour', 
              keywords: '', 
              id: {{$curr_sejour->sejour_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_sejour->_view|smarty:nodefaults|JSAttribute}}'} )"
              title="{{$curr_sejour->_nb_files_docs}} doc(s)">
              {{$curr_sejour->_nb_files_docs}}
              <img align="top" src="images/icons/next{{if !$curr_sejour->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_sejour->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
            </a>
            {{/if}}         
          </td>
        </tr>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <tr>
          <td style="padding-left: 10px;">
            <a title="Modifier l'intervention" href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              <img src="images/icons/edit.png" alt="Planifier"/>
            </a>
          
            <a href="#"
              onmouseover="viewItem('COperation', {{$curr_op->_id}})"
              onmouseout="hideItem('COperation', {{$curr_op->_id}})"
              onclick="viewCompleteItem('COperation', {{$curr_op->_id}})">
              {{$curr_op->_datetime|date_format:"%d/%m/%Y"}} - Intervention du Dr. {{$curr_op->_ref_chir->_view}}
            </a>
            <div id="COperation{{$curr_op->operation_id}}" class="tooltip" style="display: none;">
            </div>
          </td>
          <td style="text-align:right;">
          {{if $curr_op->_canRead}}
            <a href="#" onclick="setObject( {
              objClass: 'COperation', 
              keywords: '', 
              id: {{$curr_op->operation_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_op->_view|smarty:nodefaults|JSAttribute}}'} )"
              title="{{$curr_op->_nb_files_docs}} doc(s)">
              {{$curr_op->_nb_files_docs}}
              <img align="top" src="images/icons/next{{if !$curr_op->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_op->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
            </a>
            {{/if}} 
          </td>
        </tr>
        {{/foreach}}
        {{foreachelse}}
        <tr><td><em>Pas de séjours</em></td></tr>
        {{/foreach}}
        </tbody>
  
        <!-- Consultations -->
        <tr id="consultations-trigger">
          <th colspan="2" class="title">{{$patient->_ref_consultations|@count}} consultation(s)</th>
        </tr>

        <tbody class="patientEffect" style="display: none" id="consultations">
        {{foreach from=$patient->_ref_consultations item=curr_consult}}
        <tr>
          <td>
            {{if $curr_consult->annule}}
            [ANNULE]<br />
            {{else}}
            <a href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
              <img src="images/icons/planning.png" alt="modifier" title="modifier" />
            </a>
            {{/if}}
            <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}"
              onmouseover="viewItem(
                'CConsultation',
                {{$curr_consult->consultation_id}})"
              onmouseout="hideItem(
                'CConsultation',
                {{$curr_consult->consultation_id}})">
              {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}} - Dr. {{$curr_consult->_ref_chir->_view}}
            </a>
            <div id="CConsultation{{$curr_consult->consultation_id}}" class="tooltip" style="display: none;">
            </div>
          </td>
          <td style="text-align:right;">
          {{if $curr_consult->_canRead}}
            <a href="#" title="{{$curr_consult->_nb_files_docs}} doc(s)"
              onclick="setObject( {
                objClass: 'CConsultation', 
                keywords: '', 
                id: {{$curr_consult->consultation_id}}, 
                view: '{{$curr_consult->_view|smarty:nodefaults|JSAttribute}}'} )">
              {{$curr_consult->_nb_files_docs}}
              <img align="top" src="images/icons/next{{if !$curr_consult->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_consult->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
            </a>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        </tbody>

        {{if $diagnosticsInstall}}
        <tr>
          <th colspan="4" class="title" onclick="view_labo()">
            Laboratoires
          </th>
        </tr>
        {{/if}}

      </table>
      
<script type="text/javascript">      
PairEffect.initGroup("patientEffect", { 
  bStoreInCookie: true
});
</script>