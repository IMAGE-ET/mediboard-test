<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Main courante du {{$date|date_format:"%A %d %b %Y"}}
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
		    <th>Degré d'urgence</th>
		    <th>Patient</th>
		    <th>Arrivée du patient</th>
		    <th>Responsable</th>
		    <th>Diagnostic Infirmier</th>
		    <th>Prise en charge</th>
		  </tr>
		  {{foreach from=$listSejours item=sejour}}
		  {{assign var=rpu value=$sejour->_ref_rpu}}
		  {{assign var=patient value=$sejour->_ref_patient}}
		  {{assign var=consult value=$rpu->_ref_consult}}
		  <tr>
		    <td>
		    {{if $rpu->ccmu}}
		      {{tr}}CRPU.ccmu.{{$rpu->ccmu}}{{/tr}}
		    {{/if}}
		    </td>
		    <td>{{$sejour->_ref_patient->_view}}</td>
		    <td>{{$sejour->_entree|date_format:"%d/%m/%Y à %Hh%M"}}</td>
		    <td>{{$sejour->_ref_praticien->_view}}</td>
		    <td>{{$rpu->diag_infirmier|nl2br}}</td>
		    <td>({{$rpu->_count_consultations}})
		    {{if $consult->_id}}
		    {{$consult->_ref_plageconsult->_ref_chir->_view}}
		    {{/if}}
		    </td>
		  </tr>
		  {{/foreach}}
		</table>
  </td>
  </tr>
</table>