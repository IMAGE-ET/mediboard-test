<table class="{{$tbl_class}}">
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th class="title" colspan="4">
            <a href="#" onclick="window.print()">
              Consultation pré-anesthésique
            </a>
          </th>
        </tr>
        <tr>
          <th>Date </th>
          <td style="font-size: 1.3em;">{{$consult->_ref_plageconsult->date|date_format:$dPconfig.longdate}}</td>
          <th>Anesthésiste </th>
          <td style="font-size: 1.3em;">Dr {{$consult->_ref_chir->_view}}</td>
        </tr>
        <tr>
          <th>Patient </th>
          <td style="font-size: 1.3em;">{{$patient->_view}}</td>
					{{if $consult_anesth->operation_id}}
					  <th>{{mb_label object=$consult_anesth->_ref_operation field=chir_id}}</th>
            <td style="font-size: 1.3em;">{{mb_value object=$consult_anesth->_ref_operation field=chir_id}}</td>
          {{else}}
					  <th>{{mb_label object=$consult_anesth field=chir_id}}</th>
						<td style="font-size: 1.3em;">{{mb_value object=$consult_anesth field=chir_id}}</td>
					{{/if}}
        </tr>
        <tr>
          <th>
            Intervention
          </th>
          <td colspan="3" style="font-size: 1.3em;">
            {{if $consult_anesth->operation_id}}
							le {{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%A %d/%m/%Y"}}
	            {{if $consult_anesth->_ref_operation->libelle}}
	              - {{$consult_anesth->_ref_operation->libelle}}
	            {{/if}}
						{{else}}
						  le {{$consult_anesth->date_interv|date_format:"%A %d/%m/%Y"}}
              {{if $consult_anesth->libelle_interv}}
							  - {{$consult_anesth->libelle_interv}}
							{{/if}}
						{{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>