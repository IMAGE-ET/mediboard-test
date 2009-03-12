<table class="tbl">
  <tr>
    <th colspan="7">Récapitulatif</th>
  </tr>
  <tr>
    <td colspan="7">
    Nombre de patients trouvés: {{$nb_patient_ok}}
    </td>
  </tr>
  <tr>
    <th colspan="7">
      Dossiers Medicaux qui ont des traitements ou des antecedents(<strong>{{$dossiers|@count}}</strong>) 
      (nombre total: {{$nb_zombies}})</th>
  </tr>
  <tr>
    <th>dossier_medical_id</th>
    <th>patient_id supprimé</th>
    <th>Nb antecedents</th>
    <th>Nb traitements</th>
    <th>Codes CIM</th>
    <th>Consultations</th>
    <th>Patient (avec log merge)</th>
  </tr>
  {{foreach from=$dossiers item=_dossier}}
  {{assign var=dossier_id value=$_dossier->_id}}
  <tr>
    <td>{{$dossier_id}}</td>
    <td>
	    <a style="float:right;" href="#" onclick="view_log('CPatient','{{$_dossier->object_id}}')">
	      {{$_dossier->object_id}}
	    </a>
    </td>
    <td>{{$_dossier->_count.antecedents}}</td>
    <td>{{$_dossier->_count.traitements}}</td>
    <td>{{$_dossier->codes_cim}}</td>
    <td>
    
    {{if @is_array($consultations.$dossier_id)}}
      <ul>
      {{foreach from=$consultations.$dossier_id item=_consult}}
        <li>consultation_id: {{$_consult->_id}} / patient_id: {{$_consult->patient_id}}</li>
      {{/foreach}}
      </ul>
      {{/if}}
    
    </td>
    <td>
      {{if @is_array($patients.$dossier_id)}}
      <ul>
      {{foreach from=$patients.$dossier_id item=_patient_id}}
        <li>{{$_patient_id}}</li>
      {{/foreach}}
      </ul>
      {{/if}}
      
       {{if @$test.$dossier_id}}
         <strong>{{$test.$dossier_id}}</strong>
       {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>