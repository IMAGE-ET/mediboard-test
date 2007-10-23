<script type="text/javascript">

var submitActeCCAM = function(oForm, acte_ccam_id){
  submitFormAjax(oForm, 'systemMsg', {onComplete: function() { reloadActeCCAM(acte_ccam_id) } }); 
}

var reloadActeCCAM = function(acte_ccam_id){
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_reglement_ccam");
  url.addParam("acte_ccam_id", acte_ccam_id);
  url.requestUpdate('divreglement-'+acte_ccam_id, { waitingText: null } );
}

</script>

<table class="main">
  <tr>
    <th colspan="2">
      <a href="#" onclick="window.print()">
        Rapport des actes codés
      </a>
    </th>
  </tr>
  <tr>
    <td class="halfpane">
      <table class="main">
        <tr>
          <td>
            Dr. {{$praticien->_view}}
          </td>
        </tr>
        <tr>
          <td>
            du {{$debut|date_format:"%A %d %B %Y"}}
          </td>
        </tr>
        <tr>
          <td>
            au {{$fin|date_format:"%A %d %B %Y"}}
          </td>
        </tr>
      </table>
    </td>
    <td class="halfpane">
      <table class="tbl">
        <tr>
          <th>Nombre d'interventions</th>
          <td>
            {{$nbOperation}}
          </td>
        </tr>
        <tr>
          <th>Nombre d'actes ccam</th>
          <td>
            {{$nbActeCCAM}}
          </td>
        </tr>
        <tr>
          <th>Total</th>
          <td>
            {{$tarifTotal|string_format:"%.2f"}} &euro;
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{if $typeVue == 1}}
  <tr>
    <td colspan="2">
    <!-- Jour de la plageop -->
      {{foreach from=$listOperations key="key" item="jour"}}
      {{assign var="jour_affiche" value=$jour}}
      <table>
        <tr>
          <th>    
            <strong>{{$key|date_format:"%A %d %B %Y"}}</strong>
          </th>
        </tr>
      </table>
      <table class="tbl">
      {{foreach from=$jour item="plage" key=key}}
        {{if $key != "urgence"}}
        <tr>
          <th class="title" colspan="13">
            Plage de {{$plage->debut|date_format:"%Hh%M"}} à {{$plage->fin|date_format:"%Hh%M"}} en {{$plage->_ref_salle->_view}}
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>Durée</th>
          <th>Code</th>
          <th>Act.</th>
          <th>Phase</th>
          <th>Tarif</th>
          <th>Mod</th>
          <th>ANP</th>
          <th>Dépassement</th>
          <th>Total</th>
          <th>Réglement</th>
          <th>Total Intervention</th>
        </tr>
        {{elseif $jour == $jour_affiche}}
        <tr>
          <th class="title" colspan="13">Urgences</th>
        </tr>
        {{assign var="jour_affiche" value="non"}}
        <tr>
          <th>Patient</th>
          <th>Durée</th>
          <th>Code</th>
          <th>Act.</th>
          <th>Phase</th>
          <th>Tarif</th>
          <th>Mod</th>
          <th>ANP</th>
          <th>Dépassement</th>
          <th>Total</th>
          <th>Réglement</th>
          <th>Total Intervention</th>
        </tr>
        {{/if}}
     
        {{if $key != "urgence"}}
        {{foreach from=$plage->_ref_operations item="operation"}}
        {{assign var=operation_id value=$operation->_id}}
      
        <!-- Ouverture de la ligne pour de debut de l'operation -->
        <tbody class="hoverable">
        <tr>
          <td {{if $nbActes.$operation_id}}rowspan="{{$nbActes.$operation_id}}"{{/if}}>
            {{$operation->_ref_sejour->_ref_patient->_view}}
          </td>
          <td {{if $nbActes.$operation_id}}rowspan="{{$nbActes.$operation_id}}"{{/if}}>
            {{$operation->_duree_interv|date_format:"%Hh%M"}}
          </td>
          {{counter start=0 skip=1 assign=curr_counter}}
          {{foreach from=$operation->_ref_actes_ccam item="acte_ccam"}}

          {{if $acte_ccam->executant_id == $chir_id}}
         
        {{if $curr_counter != 0 && $nbActes.$operation_id}}
        <tr>
        {{/if}}
          
          <td>{{$acte_ccam->code_acte}}</td>
          <td>{{$acte_ccam->code_activite}}</td>
          <td>{{$acte_ccam->code_phase}}</td>
          {{assign var=code_activite value=$acte_ccam->code_activite}}
          {{assign var=code_phase value=$acte_ccam->code_phase}}
          {{assign var=_activite value=$acte_ccam->_ref_code_ccam->activites.$code_activite}}
          {{assign var=_phase value=$_activite->phases.$code_phase}}
          {{assign var=_tarif value=$_phase->tarif}}
          <td>{{$_tarif|string_format:"%.2f"}} &euro;</td>
          <td>{{$acte_ccam->modificateurs}}</td>
          <td>{{$acte_ccam->_guess_association}}</td>
          <td>{{$acte_ccam->montant_depassement}}</td>
          <td>{{$acte_ccam->_tarif|string_format:"%.2f"}} &euro;</td>
          <td style="text-align: center">
            <div id="divreglement-{{$acte_ccam->_id}}">
            <form name="reglement-{{$acte_ccam->_id}}" method="post" action="">
            <input type="hidden" name="dosql" value="do_acteccam_aed" />
            <input type="hidden" name="m" value="dPsalleOp" />
            <input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
            {{foreach from=$acte_ccam->_modificateurs item="modificateur"}}
            <input type="hidden" name="modificateur_{{$modificateur}}" value="on" />
            {{/foreach}}

            {{if $acte_ccam->regle == 0}}
              <input type="hidden" name="regle" value="1" />
              <button class="tick notext" type="button" onclick="submitActeCCAM(this.form, {{$acte_ccam->_id}})">Régler</button>
            {{else}}
              <input type="hidden" name="regle" value="0" />
              <button class="cancel notext" type="button" onclick="submitActeCCAM(this.form, {{$acte_ccam->_id}})">Annuler</button>
            {{/if}}
            </form>
            </div>
          </td>
         
        {{if $curr_counter == 0}}
        {{assign var=operation_id value=$operation->_id}}
          <td style="text-align: center" {{if $nbActes.$operation_id}}rowspan="{{$nbActes.$operation_id}}"{{/if}}>{{$tarifOperation.$operation_id|string_format:"%.2f"}} &euro;</td>
        </tr>
        {{/if}}
       
        {{if $nbActes.$operation_id && $curr_counter != 0}}
        </tr>
        {{/if}}
        {{counter}}
        {{/if}} <!-- fin du if pour de test de l'executant -->
        {{/foreach}}
       
        </tbody>
        {{/foreach}}
       
        <tr>
          <th colspan="11" style="text-align: right">Total de la plage</th>
          <td style="text-align: center">
            {{assign var=plage_id value=$plage->_id}}
            {{$tarifPlage.$plage_id|string_format:"%.2f"}} &euro;
          </td>
        </tr>
        {{else}}
        <!-- Affichage des urgences -->
      
        {{foreach from=$plage item="operation"}}
        {{assign var=operation_id value=$operation->_id}}
      
        <!-- Ouverture de la ligne pour de debut de l'operation -->
        <tbody class="hoverable">
        <tr>
          <td {{if $nbActes.$operation_id}}rowspan="{{$nbActes.$operation_id}}"{{/if}}>{{$operation->_ref_sejour->_ref_patient->_view}}</td>
          <td {{if $nbActes.$operation_id}}rowspan="{{$nbActes.$operation_id}}"{{/if}}>{{$operation->_duree_interv}}</td>
          {{counter start=0 skip=1 assign=curr_counter}}
          {{foreach from=$operation->_ref_actes_ccam item="acte_ccam"}}
         
          {{if $acte_ccam->executant_id == $chir_id}}
         
          {{if $curr_counter != 0 && $nbActes.$operation_id}}
        <tr>
        {{/if}}
         
          <td>{{$acte_ccam->code_acte}}</td>
          <td>{{$acte_ccam->code_activite}}</td>
          <td>{{$acte_ccam->code_phase}}</td>
          {{assign var=code_activite value=$acte_ccam->code_activite}}
          {{assign var=code_phase value=$acte_ccam->code_phase}}
          {{assign var=_activite value=$acte_ccam->_ref_code_ccam->activites.$code_activite}}
          {{assign var=_phase value=$_activite->phases.$code_phase}}
          {{assign var=_tarif value=$_phase->tarif}}
          <td>{{$_tarif|string_format:"%.2f"}} &euro;</td>
          <td>{{$acte_ccam->modificateurs}}</td>
          <td>{{$acte_ccam->_guess_association}}</td>
          <td>{{$acte_ccam->montant_depassement}}</td>
          <td>{{$acte_ccam->_tarif|string_format:"%.2f"}} &euro;</td>
          <td style="text-align: center">
            <div id="divreglement-{{$acte_ccam->_id}}">
            <form name="reglement-{{$acte_ccam->_id}}" method="post" action="">
            <input type="hidden" name="dosql" value="do_acteccam_aed" />
            <input type="hidden" name="m" value="dPsalleOp" />
            <input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
            {{foreach from=$acte_ccam->_modificateurs item="modificateur"}}
              <input type="hidden" name="modificateur_{{$modificateur}}" value="on" />
            {{/foreach}}
            
            {{if $acte_ccam->regle == 0}}
              <input type="hidden" name="regle" value="1" />
              <button class="tick notext" type="button" onclick="submitActeCCAM(this.form, {{$acte_ccam->_id}})">Régler</button>
            {{else}}
              <input type="hidden" name="regle" value="0" />
              <button class="cancel notext" type="button" onclick="submitActeCCAM(this.form, {{$acte_ccam->_id}})">Annuler</button>
            {{/if}}
            </form>
            </div>
          </td>
         
          {{if $curr_counter == 0}}
          {{assign var=operation_id value=$operation->_id}}
          <td style="text-align: center" {{if $nbActes.$operation_id}}rowspan="{{$nbActes.$operation_id}}"{{/if}}>{{$tarifOperation.$operation_id|string_format:"%.2f"}} &euro;</td>
        </tr>
        {{/if}}
       
        {{if $nbActes.$operation_id && $curr_counter != 0}}
        </tr>
        {{/if}}
        {{counter}}
        {{/if}} <!-- fin du if pour de test de l'executant -->
        {{/foreach}}
       
        </tbody>
        {{/foreach}}
      
      {{/if}}
      {{/foreach}}
      </table>
      {{/foreach}}
      <table class="tbl" style="width: 100% ">
        <tr>
          <th class="title" style="text-align: right">
            Total de la sélection
          </th>
          <td style="text-align: center">
            {{$tarifTotal|string_format:"%.2f"}} &euro;
          </td>
        </tr>   
      </table>
    </td>
  </tr>
  {{/if}}
</table>