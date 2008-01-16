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
    <td>
      <table class="main">
        <tr>
          <td>
            Dr. {{$praticien->_view}}
          </td>
        </tr>
        <tr>
          <td>
            du {{$_date_min|date_format:"%A %d %B %Y"}}
          </td>
        </tr>
        <tr>
          <td>
            au {{$_date_max|date_format:"%A %d %B %Y"}}
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>Nombre de séjours</th>
          <td>
            {{$nbActes|@count}}
          </td>
        </tr>
        <tr>
          <th>Nombre d'actes ccam</th>
          <td>
            {{$totalActes}}
          </td>
        </tr>
        <tr>
          <th>Total</th>
          <td>
            {{$montantTotalActes|string_format:"%.2f"}} &euro;
          </td>
        </tr>
      </table>
    </td>
  </tr>
  
{{if $typeVue == 1}}
<!-- Parcours de jours -->
{{foreach from=$sejours key="key" item="jour"}}
  <tr>
    <td colspan="2">
      <table>
       <tr> 
         <td>
           <strong>Sortie réelle le {{$key|date_format:"%A %d %B %Y"}}</strong>
         </td>
       </tr>
     </table>
     <table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Total Séjour</th>
    <th>Type</th>
    <th>Code</th>
    <th>Act.</th>
    <th>Phase</th>
    <th>Tarif</th>
    <th>Mod</th>
    <th>ANP</th>
    <th>Dépassement</th>
    <th>Total</th>
    <th>Réglement</th>
  </tr>
  
  
  <!-- Parcours des sejours -->
  {{foreach from=$jour item="sejour"}}
    {{assign var="sejour_id" value=$sejour->_id}}
    
   <tbody class="hoverable">
   <tr>
    <td rowspan="{{$nbActes.$sejour_id}}">{{$sejour->_ref_patient->_view}} {{if $sejour->_ref_patient->_age}}({{$sejour->_ref_patient->_age}} ans){{/if}}</td>
    <td rowspan="{{$nbActes.$sejour_id}}">
      {{$montantSejour.$sejour_id|string_format:"%.2f"}} &euro;
    </td>
         
    {{if $sejour->_ref_actes_ccam|@count}}
    <td rowspan="{{$sejour->_ref_actes_ccam|@count}}">Séjour du {{$sejour->entree_reelle|date_format:"%d %B %Y"}} au {{$sejour->sortie_reelle|date_format:"%d %B %Y"}}</td>
    
    {{counter start=0 skip=1 assign=curr_counterSej}}
    {{foreach from=$sejour->_ref_actes_ccam item="acte_ccam"}}
      {{if $curr_counterSej != 0}}
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
        <td>{{$acte_ccam->code_association}}</td>
        <td>{{$acte_ccam->montant_depassement}}</td>
        <td>{{$acte_ccam->_tarif|string_format:"%.2f"}} &euro;</td>
        <td style="text-align: center">
          <div id="divreglement-{{$acte_ccam->_id}}">
          <form name="reglement-{{$acte_ccam->_id}}" method="post" action="">
          <input type="hidden" name="dosql" value="do_acteccam_aed" />
          <input type="hidden" name="m" value="dPsalleOp" />
          <input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
          <input type="hidden" name="_check_coded" value="0" />
	        
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
      
      </tr>
      {{counter}}
    {{/foreach}}
    {{/if}}
    
    {{if $sejour->_ref_operations}}
      {{counter start=0 skip=1 assign=curr_counterOp}}
      {{foreach from=$sejour->_ref_operations item="operation"}}
      
      <td rowspan="{{$operation->_ref_actes_ccam|@count}}">Intervention du {{$operation->_datetime|date_format:"%d %B %Y"}}{{if $operation->libelle}}: {{$operation->libelle}}{{/if}}</td>
      {{foreach from=$operation->_ref_actes_ccam key="key" item="acte_ccam"}}
        {{if $curr_counterOp != 0}}
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
          <td>{{$acte_ccam->code_association}}</td>
          <td>{{$acte_ccam->montant_depassement}}</td>
          <td>{{$acte_ccam->_tarif|string_format:"%.2f"}} &euro;</td>
          <td style="text-align: center">
          <div id="divreglement-{{$acte_ccam->_id}}">
          <form name="reglement-{{$acte_ccam->_id}}" method="post" action="">
          <input type="hidden" name="dosql" value="do_acteccam_aed" />
          <input type="hidden" name="m" value="dPsalleOp" />
          <input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
                    <input type="hidden" name="_check_coded" value="0" />
          
          
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

      </tr>
        {{counter}}
      {{/foreach}}
      {{/foreach}}
    {{/if}}
  
  
  
  {{if $sejour->_ref_consultations}}
    {{counter start=0 skip=1 assign=curr_counterCons}}
    {{if $curr_counterCons != 0}}
    <tr>
    {{/if}}
    {{foreach from=$sejour->_ref_consultations item="consult"}}
     <td rowspan="{{$consult->_ref_actes_ccam|@count}}">{{$consult->_view}}</td>
      {{foreach from=$consult->_ref_actes_ccam item="acte_ccam"}}
        {{if $curr_counterCons != 0}}
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
          <td>{{$acte_ccam->code_association}}</td>
          <td>{{$acte_ccam->montant_depassement}}</td>
          <td>{{$acte_ccam->_tarif|string_format:"%.2f"}} &euro;</td>
          <td style="text-align: center">
	          <div id="divreglement-{{$acte_ccam->_id}}">
	          <form name="reglement-{{$acte_ccam->_id}}" method="post" action="">
	          <input type="hidden" name="dosql" value="do_acteccam_aed" />
	          <input type="hidden" name="m" value="dPsalleOp" />
	          <input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
	          <input type="hidden" name="_check_coded" value="0" />
	    
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
        
      </tr>
        {{counter}}
      {{/foreach}}
    {{/foreach}}
  {{/if}}
  
  {{/foreach}}
  
  </tbody>
  </table>
</td>
</tr>

{{/foreach}}

{{/if}}


</table>

