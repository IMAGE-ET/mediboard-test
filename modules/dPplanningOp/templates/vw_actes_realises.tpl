<script type="text/javascript">

var submitActeCCAM = function(oForm, acte_ccam_id){
  submitFormAjax(oForm, 'systemMsg', {onComplete: function() { reloadActeCCAM(acte_ccam_id) } }); 
}

var reloadActeCCAM = function(acte_ccam_id) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_reglement_ccam");
  url.addParam("acte_ccam_id", acte_ccam_id);
  url.requestUpdate('divreglement-'+acte_ccam_id, { waitingText: null } );
}

var viewCCAM = function(codeacte) {
  var url = new Url;
  url.setModuleAction("dPccam", "vw_full_code");
  url.addParam("codeacte", codeacte);
  url.popup(800, 600, "Code CCAM");
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
            Dr {{$praticien->_view}}
          </td>
        </tr>
        <tr>
          <td>
            du {{$_date_min|date_format:$dPconfig.longdate}}
          </td>
        </tr>
        <tr>
          <td>
            au {{$_date_max|date_format:$dPconfig.longdate}}
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
           <strong>Sortie réelle le {{$key|date_format:$dPconfig.longdate}}</strong>
         </td>
       </tr>
     </table>
     <table class="tbl">
  <tr>
    <th style="width: 20%">Patient</th>
    <th style="width: 05%">Total Séjour</th>
    <th style="width: 20%">Type</th>
    <th style="width: 05%">Code</th>
    <th style="width: 05%">Act.</th>
    <th style="width: 05%">Phase</th>
    <th style="width: 05%">Mod</th>
    <th style="width: 05%">ANP</th>
    <th style="width: 05%">{{mb_title class=CActeCCAM field=montant_base}}</th>
    <th style="width: 05%">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th style="width: 05%">{{mb_title class=CActeCCAM field=_montant_facture}}</th>
    <th style="width: 05%">Réglement</th>
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
    
    {{include file=inc_acte_realise.tpl codable=$sejour}}
        
    {{if $sejour->_ref_operations}}
      {{foreach from=$sejour->_ref_operations item=operation}}
	    {{include file=inc_acte_realise.tpl codable=$operation}}
      {{/foreach}}
    {{/if}}
  
  
  
  {{if $sejour->_ref_consultations}}
    {{foreach from=$sejour->_ref_consultations item=consult}}
    {{include file=inc_acte_realise.tpl codable=$consult}}
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

