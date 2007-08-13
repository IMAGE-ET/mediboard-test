<script type="text/javascript">

function pageMain() {
  regFieldCalendar("recherche", "date_debut");
  
  var oAccord = new Rico.Accordion($('accordionExamen'), { 
    panelHeight: 450,
    showDelay: 50, 
    showSteps: 3 
  } );
        
}

</script>


<form name="recherche" action="./index.php" method="get">
  <table class="main">
    <tr>  
      <td class="halfPane">
 
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <input type="hidden" name="new" value="1" />
      
       
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
        <th>{{mb_label object=$pat_diag field="listCim10"}}</th>
        <td colspan="4"><input type="text" name="diagnostic_patient" value="{{$diagnostic_patient|stripslashes}}" /></td>
      </tr>       
         
      
         
      <!-- Criteres sur les consultations -->   
      <tr>
        <th class="category" colspan="4">Consultation</th>
      </tr>
      <tr>
        <td colspan="4">Au moins un critère <input type="radio" name="recherche_consult" value="or" {{if $recherche_consult == "or"}}checked{{/if}} >
        Tous les critères <input type="radio" name="recherche_consult" value="and" {{if $recherche_consult == "and"}}checked{{/if}}></td>
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
      
      
      
      
      <!-- Critères sur les séjours --> 
      <tr>
        <th class="category" colspan="4">Séjour</th>
      </tr>
      <tr>
        <td colspan="4">Au moins un critère <input type="radio" name="recherche_sejour" value="or" {{if $recherche_sejour == "or"}}checked{{/if}} >
        Tous les critères <input type="radio" name="recherche_sejour" value="and" {{if $recherche_sejour == "and"}}checked{{/if}}></td>
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
        <td colspan="4">Au moins un critère <input type="radio" name="recherche_intervention" value="or" {{if $recherche_intervention == "or"}}checked{{/if}} >
        Tous les critères <input type="radio" name="recherche_intervention" value="and" {{if $recherche_intervention == "and"}}checked{{/if}}></td>
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
        <td colspan="4"><input type="text" name="remarque_intervention" value="{{$remarque_intervention|stripslashes}}"/></td>
      </tr>   
      
      
      
      <tr>
        <td class="button" colspan="4">
          <button class="search" type="submit">Rechercher</button>
        </td>
      </tr>
    </table>
    
    </td>
    
      <td class="halfPane">
        {{assign var="board" value=$board}}
        <div class="accordionMain" id="accordionExamen">
          
          {{if $patients_ant}}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Résultats par Antécédents ({{$patients_ant|@count}})
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              {{assign var="tab_recherche" value=$patients_ant}}
              {{include file="inc_list_patient_acc.tpl"}}
            </div>
          </div>
          {{/if}}
          
          {{if $patients_trait}}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Résultats par Traitements ({{$patients_trait|@count}})
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              {{assign var="tab_recherche" value=$patients_trait}}
              {{include file="inc_list_patient_acc.tpl"}}            
            </div>
          </div>
          {{/if}}
          
          {{if $patients_diag}}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Résultats par Diagnostics ({{$patients_diag|@count}})
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              {{assign var="tab_recherche" value=$patients_diag}}
              {{include file="inc_list_patient_acc.tpl"}}
            </div>
          </div>
          {{/if}}
          
          {{if $patients_consult}}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Résultats par Consultations ({{$patients_consult|@count}})
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              {{assign var="tab_recherche" value=$patients_consult}}
              {{include file="inc_list_patient_acc.tpl"}}
            </div>
          </div>
          {{/if}}
          
          {{if $patients_sejour}}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Résultats par Séjours ({{$patients_sejour|@count}})
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              {{assign var="tab_recherche" value=$patients_sejour}}
              {{include file="inc_list_patient_acc.tpl"}}
            </div>
          </div>
          {{/if}}
          
          {{if $patients_intervention}}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Résultats par Interventions ({{$patients_intervention|@count}})
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              {{assign var="tab_recherche" value=$patients_intervention}}
              {{include file="inc_list_patient_acc.tpl"}}
            </div>
          </div>
          {{/if}}
          
          {{if !$patients_ant && !$patients_trait && !$patients_diag && !$patients_consult && !$patients_sejour && !$patients_intervention }}
          <div id="acc_antecedent">
            <div  class="accordionTabTitleBar" id="IdentiteHeader">
              Aucun résultat pour la recherche
            </div>
            <div class="accordionTabContentBox" id="IdentiteContent"  >
              
            </div>
          </div>          
          {{/if}}
        </div>    
    </td>
  </table>
</form>