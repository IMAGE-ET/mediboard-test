<tr {{if $patient->_id == $_patient->_id}}class="selected"{{/if}}>
  {{if (!$dPconfig.dPpatients.CPatient.merge_only_admin || $can->admin) && $can->edit}}
  <td style="text-align: center;"><input type="checkbox" name="objects_id[]" value="{{$_patient->_id}}" /></td>
  {{/if}}
  {{if $_patient->vip && $can->admin}}
  <td class="text" colspan="4">
    <a href="#{{$_patient->_guid}}" onclick="reloadPatient('{{$_patient->_id}}', this);">
      Patient confidentiel
    </a>
  </td>
  {{else}}
  <td class="text">
    <div style="float:right;">
      {{mb_include module=system template=inc_object_notes object=$_patient}}
    </div>
    {{if $_patient->_id == $patVitale->_id}}
    <div style="float:right;">
      <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="Bénéficiaire associé à la carte Vitale" />
    </div>
    {{/if}}

    <a href="#{{$_patient->_guid}}" onclick="reloadPatient('{{$_patient->_id}}', this);">
      {{mb_value object=$_patient field="_view"}}
    </a>
    
  </td>
  <td class="text">
  	<span onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">
      {{mb_value object=$_patient field="naissance"}}
  	</span>
  </td>
  <td class="text">
    {{$_patient->adresse|spancate:30}} -
    {{$_patient->cp}}
    {{$_patient->ville|spancate:20}}
  </td>
  <td>
    <a class="button search notext" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$_patient->_id}}" title="Afficher le dossier complet">
      {{tr}}Show{{/tr}}
    </a>
  </td>
  {{/if}}
</tr>
