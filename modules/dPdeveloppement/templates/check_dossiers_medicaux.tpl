<script type="text/javascript">

function repair(patient_id, dossier_medical_id) {  
  var url = new Url;
  url.setModuleAction("dPdeveloppement", "repair_zombie_dossier_medical");
  url.addParam("patient_id", patient_id);
  url.addParam("dossier_medical_id", dossier_medical_id);
  url.requestUpdate("repair"+dossier_medical_id);
}

</script>

<table class="tbl">
  <tr>
    <th colspan="8">Récapitulatif</th>
  </tr>
  <tr>
    <td colspan="8">
    Nombre de patients trouvés: {{$nb_patient_ok}}
    </td>
  </tr>
  <tr>
    <th colspan="8">
      Dossiers Medicaux qui ont des traitements ou des antecedents(<strong>{{$dossiers|@count}}</strong>) 
      (nombre total: {{$nb_zombies}})</th>
  </tr>
  <tr>
    <th>dossier_medical_id</th>
    <th>patient_id supprimé</th>
    <th>Nb antecedents</th>
    <th>Nb traitements</th>
    <th>Codes CIM</th>
    <th>Patient (avec log merge)</th>
    <th>Date du merge</th>
    <th>Réparer</th>
    
    
  </tr>
  {{foreach from=$dossiers item=_dossier}}
  {{assign var=dossier_id value=$_dossier->_id}}
  <tr>
    <td>
      <a class="tooltip-trigger" href="#nothing" onmouseover="ObjectTooltip.createEx(this, '{{$_dossier->_guid}}')">
        {{$dossier_id}}
      </a>
    </td>
    <td>
	    <a href="#" onclick="view_log('CPatient','{{$_dossier->object_id}}')">
	      {{$_dossier->object_id}}
	    </a>
    </td>
    <td>{{$_dossier->_count.antecedents}}</td>
    <td>{{$_dossier->_count.traitements}}</td>
    <td>{{$_dossier->codes_cim}}</td>
    <td>
      {{if @$test.$dossier_id.patient_id}}
        <strong>{{$test.$dossier_id.patient_id}}</strong>
      {{/if}}
   </td>
   <td>
      {{if @$test.$dossier_id.merge_date}}
       <strong>{{$test.$dossier_id.merge_date|date_format:$dPconfig.datetime}}</strong>
     {{/if}}
   </td>
   <td class="button">
     {{if $test.$dossier_id.patient_id}}
     <div id="repair{{$dossier_id}}">
       <button type="button" onclick="repair({{$test.$dossier_id.patient_id}}, '{{$dossier_id}}');">Réparer</button>
     </div>
     {{/if}}
   </td>
  </tr>
  {{/foreach}}
</table>