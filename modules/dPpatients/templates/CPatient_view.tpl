{{assign var="patient" value=$object}}

<table class="tbl">
  <tr>
    <th colspan="2" class="title text">
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}

      {{mb_include module=system template=inc_object_history object=$patient}}

      {{mb_include module=system template=inc_object_notes object=$patient}}
      
      {{$patient}}
    </th>
  </tr>
  <tr>
    <th>Identité</th>
    <th>Coordonnées</th>
  </tr>
  <tr>
    <td>
      {{mb_include module=system template=inc_field_view prop=nom}}
      {{mb_include module=system template=inc_field_view prop=nom_jeune_fille}}
      {{mb_include module=system template=inc_field_view prop=prenom}}
      {{mb_include module=system template=inc_field_view prop=prenom_2}}
      {{mb_include module=system template=inc_field_view prop=prenom_3}}
      {{mb_include module=system template=inc_field_view prop=prenom_4}}

      {{mb_include module=system template=inc_field_view prop=sexe}}
      {{mb_include module=system template=inc_field_view prop=naissance}}
      {{mb_include module=system template=inc_field_view prop=_age}}
      {{mb_include module=system template=inc_field_view prop=profession}}
      {{mb_include module=system template=inc_field_view prop=matricule}}
    </td>
    <td>
      {{mb_include module=system template=inc_field_view prop=adresse}}
      {{mb_include module=system template=inc_field_view prop=cp}}
      {{mb_include module=system template=inc_field_view prop=ville}}
      {{mb_include module=system template=inc_field_view prop=tel}}
      {{mb_include module=system template=inc_field_view prop=tel2}}
      {{mb_include module=system template=inc_field_view prop=rques}}
    </td>
  </tr>
	
	<tr>
		<td colspan="10" class="button">
			{{mb_include_script module="dPpatients" script="patient" ajax="true"}}

      {{if $can->edit}}
      <button type="button" class="edit" onclick="Patient.edit('{{$patient->_id}}')">
        {{tr}}Modify{{/tr}}
      </button>
      {{/if}}

      <button type="button" class="search" onclick="Patient.view('{{$patient->_id}}')">
        Dossier Complet
      </button>
      
      <button type="button" class="print" onclick="Patient.print('{{$patient->_id}}')">
        {{tr}}Print{{/tr}}
      </button>
		</td>
	</tr>
</table>