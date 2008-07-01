<table class="tbl" style="margin: auto; width: 50%">
  <tr>
    <th class="title" colspan="2">
    	Médecins traitants les plus prescripteurs
    	(max. {{$max}})
    </th>
  </tr>

  <tr>
    <th>{{mb_label class=CPatient field=medecin_traitant}}</th>
    <th>Nombre de patients</th>
  </tr>

	{{foreach from=$prescripteurs key=medecin_id item=nb_patients}}
  <tr>
    <td>
      {{assign var=medecin value=$medecins.$medecin_id}}
	      <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$medecin->_class_name}}', object_id: {{$medecin->_id}} } })">
	      {{$medecin->_view}}
	      </span>
      </td>
    <td>{{$nb_patients}}</td>
  </tr>
	{{foreachelse}}
	<tr>
		<td colspan="2"><em>Aucun</em></td>
	</tr>
	
	{{/foreach}}
</table>

