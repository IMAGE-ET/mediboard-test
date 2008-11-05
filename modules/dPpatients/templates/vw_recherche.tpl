{{mb_include_script module="dPplanningOp" script="ccam_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}


<script type="text/javascript">

Main.add(function () {
  var tabs = new Control.Tabs('tab-resultats');  
});

</script>

<table class="main">
  <tr>  
    <td class="halfPane">
     <form name="recherche" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="new" value="1" />
        <input type="hidden" name="rechercheOpClass" value="COperation" />
        <input type="hidden" name="rechercheChir" value="{{$user_id}}" />
    
    <table class="form">
      <tr>
        <th class="category" colspan="4">Recherche d'un dossier patient</th>
      </tr>
        
      <!-- Criteres sur les patients -->  
      <tr>
        <th class="category" colspan="4">Patient</th>
      </tr>
      <tr>
        <th>{{mb_label object=$ant field="rques"}}</th>
        <td><input type="text" name="antecedent_patient" value="{{$antecedent_patient|stripslashes}}" /></td>
        <th>{{mb_label object=$trait field="traitement"}}</th>
        <td><input type="text" name="traitement_patient" value="{{$traitement_patient|stripslashes}}" /></td>
      </tr>
      <tr>
        <th>{{mb_label object=$dossierMedical field="codes_cim"}}</th>
        <td colspan="4">
          <input type="text" name="diagnostic_patient" value="{{$diagnostic_patient|stripslashes}}" />
          <button class="search notext" type="button" onclick="CIM10Selector.init()">Rechercher</button>
          <script type="text/javascript">   
            CIM10Selector.init = function(){
              this.sForm = "recherche";
              this.sView = "diagnostic_patient";
              this.sChir = "rechercheChir";
              this.pop();
            }
          </script>
        </td>
      </tr>       
         
      
      {{if $canCabinet->read}}
      <!-- Criteres sur les consultations -->   
      <tr>
        <th class="category" colspan="4">Consultation</th>
      </tr>
      <tr>
        <td colspan="4">Au moins un critère <input type="radio" name="recherche_consult" value="or" {{if $recherche_consult == "or"}}checked{{/if}} />
        Tous les critères <input type="radio" name="recherche_consult" value="and" {{if $recherche_consult == "and"}}checked{{/if}} /></td>
      </tr>
      <tr>
        <th>{{mb_label object=$consult field="motif"}}</th>
        <td><input type="text" name="motif_consult" value="{{$motif_consult|stripslashes}}"/></td>
      
        <th>{{mb_label object=$consult field="rques"}}</th>
        <td><input type="text" name="remarque_consult" value="{{$remarque_consult|stripslashes}}"/></td>
      </tr>
      <tr>
        <th>{{mb_label object=$consult field="examen"}}</th>
        <td><input type="text" name="examen_consult" value="{{$examen_consult|stripslashes}}"/></td>
      
        <th>{{mb_label object=$consult field="traitement"}}</th>
        <td><input type="text" name="traitement_consult" value="{{$traitement_consult|stripslashes}}"/></td>
      </tr>
      {{/if}}
      
      
      {{if $canPlanningOp->read}}
      <!-- Critères sur les séjours --> 
      <tr>
        <th class="category" colspan="4">Séjour</th>
      </tr>
      <tr>
        <td colspan="4">Au moins un critère <input type="radio" name="recherche_sejour" value="or" {{if $recherche_sejour == "or"}}checked{{/if}} />
        Tous les critères <input type="radio" name="recherche_sejour" value="and" {{if $recherche_sejour == "and"}}checked{{/if}} /></td>
      </tr>
      <tr>
        <th>{{mb_label object=$sejour field="type"}}</th>
        <td><input type="text" name="typeAdmission_sejour" value="{{$typeAdmission_sejour|stripslashes}}" /></td>
        <th>{{mb_label object=$sejour field="convalescence"}}</th>
        <td><input type="text" name="convalescence_sejour" value="{{$convalescence_sejour|stripslashes}}" /></td>
      </tr>
      <tr>
        <th>{{mb_label object=$sejour field="rques"}}</th>
        <td colspan="4"><input type="text" name="remarque_sejour"  value="{{$remarque_sejour|stripslashes}}" /></td>
      </tr>       

       
      <!-- Critères sur les interventions -->         
      <tr>
        <th class="category" colspan="4">Intervention</th>
      </tr>
      <tr>
        <td colspan="4">Au moins un critère <input type="radio" name="recherche_intervention" value="or" {{if $recherche_intervention == "or"}}checked{{/if}} />
        Tous les critères <input type="radio" name="recherche_intervention" value="and" {{if $recherche_intervention == "and"}}checked{{/if}} /></td>
      </tr>
      <tr>
        <!-- materiel a prevoir / examens per-op -->
        <th>{{mb_label object=$intervention field="materiel"}}</th>
        <td><input type="text" name="materiel_intervention" value="{{$materiel_intervention|stripslashes}}"/></td>
        <!-- bilan pre-op -->
        <th>{{mb_label object=$intervention field="examen"}}</th>
        <td><input type="text" name="examen_intervention" value="{{$examen_intervention|stripslashes}}"/></td>
      </tr>
      <tr>
        <th>{{mb_label object=$intervention field="rques"}}</th>
        <td><input type="text" name="remarque_intervention" value="{{$remarque_intervention|stripslashes}}"/></td>
        <th>{{mb_label object=$intervention field="libelle"}}</th>
        <td><input type="text" name="libelle_intervention" value="{{$libelle_intervention|stripslashes}}"/></td>
      </tr> 
      <tr>
        <th>Codes CCAM</th>
        <td colspan="4">
          <input type="text" name="ccam_intervention" value="{{$ccam_intervention|stripslashes}}"/>
          <button class="search notext" type="button" onclick="CCAMSelector.init()">Rechercher</button>
          
          <script type="text/javascript">   
            CCAMSelector.init = function(){
              this.sForm = "recherche";
              this.sClass = "rechercheOpClass";
              this.sChir = "rechercheChir";
              this.sView = "ccam_intervention";
              this.pop();
            }
          </script>
        
       
        </td>
      </tr>  
      {{/if}}
    
      <tr>
        <td class="button" colspan="4">
          <button class="search" type="submit">Rechercher</button>
        </td>
      </tr>
    </table>
    
    </td>
    
      <td class="halfPane">
        <ul id="tab-resultats" class="control_tabs">
          {{if $dossiersMed}}<li><a href="#diagnostic">Diagnostics ({{$dossiersMed|@count}})</a></li>{{/if}}
          {{if $traitements}}<li><a href="#traitement">Traitements ({{$traitements|@count}})</a></li>{{/if}}
          {{if $antecedents}}<li><a href="#antecedent">Antécédents ({{$antecedents|@count}})</a></li>{{/if}}
          {{if $consultations && $canCabinet->read}}<li><a href="#consultation">Consultations ({{$consultations|@count}})</a></li>{{/if}}
          {{if $interventions && $canPlanningOp->read}}<li><a href="#intervention">Interventions ({{$interventions|@count}})</a></li>{{/if}}
          {{if $sejours && $canPlanningOp->read}}<li><a href="#sejour">Séjours ({{$sejours|@count}})</a></li>{{/if}}
          {{if !$antecedents && !$traitements && !$dossiersMed && !$consultations && !$sejours && !$interventions }}
            <li><a href="#noresult">Aucun résultat pour la recherche</a></li>
          {{/if}}
        
        </ul>
        <hr class="control_tabs" />

        {{if $dossiersMed}}
        <table class="form" id="diagnostic" style="display: none;">
          {{foreach from=$dossiersMed item=curr_dossier}}
          {{assign var="patient" value=$curr_dossier->_ref_object}}
          <tr>
            <td class="text">
              <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
                 {{mb_value object=$patient field="_view"}}
              </a>
            </td>
            <td class="text">
              <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
                {{mb_value object=$patient field="naissance"}}
              </a>
            </td>
            <td class="text">
              <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
                {{mb_value object=$patient field="adresse"}}
                {{mb_value object=$patient field="cp"}}
                {{mb_value object=$patient field="ville"}}
              </a>
            </td>
          </tr>
          {{/foreach}}
        </table>
        {{/if}}
        
        {{if $traitements}}
        <table class="form" id="traitement" style="display: none;">
         {{foreach from=$traitements item=curr_traitement}}
         {{assign var="patient" value=$curr_traitement->_ref_dossier_medical->_ref_object}}
          <tr>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
              {{mb_value object=$patient field="_view"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
              {{mb_value object=$patient field="naissance"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
              {{mb_value object=$patient field="adresse"}}
              {{mb_value object=$patient field="cp"}}
              {{mb_value object=$patient field="ville"}}
            </a>
            </td>
          </tr>
          {{/foreach}} 
        </table>
        {{/if}}

        
        {{if $antecedents}}
        <table class="form" id="antecedent" style="display: none;">
         {{foreach from=$antecedents item=curr_antecedent}}
         {{assign var="patient" value=$curr_antecedent->_ref_dossier_medical->_ref_object}}
          <tr>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
              {{mb_value object=$patient field="_view"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
              {{mb_value object=$patient field="naissance"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
              {{mb_value object=$patient field="adresse"}}
              {{mb_value object=$patient field="cp"}}
              {{mb_value object=$patient field="ville"}}
            </a>
            </td>
          </tr>
          {{/foreach}} 
        </table>
        {{/if}}
         
        {{if $consultations && $canCabinet->read}}
        <table class="form" id="consultation" style="display: none;">
         {{foreach from=$consultations item=curr_consultation}}
          <tr>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_consultation->_ref_patient->_id}}&consultation_id={{$curr_consultation->_id}}">
              {{mb_value object=$curr_consultation->_ref_patient field="_view"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_consultation->_ref_patient->_id}}&consultation_id={{$curr_consultation->_id}}">
              {{mb_value object=$curr_consultation->_ref_patient field="naissance"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_consultation->_ref_patient->_id}}&consultation_id={{$curr_consultation->_id}}">
              {{mb_value object=$curr_consultation->_ref_patient field="adresse"}}
              {{mb_value object=$curr_consultation->_ref_patient field="cp"}}
              {{mb_value object=$curr_consultation->_ref_patient field="ville"}}
            </a>
            </td>
          </tr>
          {{/foreach}} 
        </table>
        {{/if}}
         
          
        {{if $interventions && $canPlanningOp->read}}
        <table class="form" id="intervention" style="display: none;">
         {{foreach from=$interventions item=curr_intervention}}
          <tr>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_intervention->_ref_sejour->_ref_patient->_id}}&operation_id={{$curr_intervention->_id}}">
              {{mb_value object=$curr_intervention->_ref_sejour->_ref_patient field="_view"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_intervention->_ref_sejour->_ref_patient->_id}}&operation_id={{$curr_intervention->_id}}">
              {{mb_value object=$curr_intervention->_ref_sejour->_ref_patient field="naissance"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_intervention->_ref_sejour->_ref_patient->_id}}&operation_id={{$curr_intervention->_id}}">
              {{mb_value object=$curr_intervention->_ref_sejour->_ref_patient field="adresse"}}
              {{mb_value object=$curr_intervention->_ref_sejour->_ref_patient field="cp"}}
              {{mb_value object=$curr_intervention->_ref_sejour->_ref_patient field="ville"}}
            </a>
            </td>
          </tr>
          {{/foreach}} 
        </table>
        {{/if}}
          
          
        {{if $sejours && $canPlanningOp->read}}
        <table class="form" id="sejour" style="display: none;">
         {{foreach from=$sejours item=curr_sejour}}
          <tr>
            <td class="text">
             <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_sejour->_ref_patient->_id}}&sejour_id={{$curr_sejour->_id}}">
               {{mb_value object=$curr_sejour->_ref_patient field="_view"}}
             </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_sejour->_ref_patient->_id}}&sejour_id={{$curr_sejour->_id}}">
              {{mb_value object=$curr_sejour->_ref_patient field="naissance"}}
            </a>
            </td>
            <td class="text">
            <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_sejour->_ref_patient->_id}}&sejour_id={{$curr_sejour->_id}}">
              {{mb_value object=$curr_sejour->_ref_patient field="adresse"}}
              {{mb_value object=$curr_sejour->_ref_patient field="cp"}}
              {{mb_value object=$curr_sejour->_ref_patient field="ville"}}
            </a>
            </td>
          </tr>
          {{/foreach}} 
        </table>
        {{/if}}
        
        
        {{if !$antecedents && !$traitements && !$dossiersMed && !$consultations && !$sejours && !$interventions }}
        <div id="noresult" style="display: none;"></div>          
        {{/if}}
    </td>
  </table>