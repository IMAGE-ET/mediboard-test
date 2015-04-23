{{assign var=merge_only_admin value="CAppUI::conf"|static_call:"dPpatients identitovigilance merge_only_admin":"CGroups-$g"}}

<tr class="{{if $patient->_id == $_patient->_id}}selected{{/if}} {{if $_patient->deces != null}}hatching{{/if}}">
  {{if (!$merge_only_admin || $can->admin) && $can->edit || $conf.dPpatients.CPatient.show_patient_link}}
    <td style="text-align: center;">
      <input type="checkbox" name="objects_id[]" value="{{$_patient->_id}}" class="merge"
             {{if $conf.alternative_mode}}onclick="checkOnlyTwoSelected(this)"{{/if}} />
    </td>
  {{/if}}
  {{if $_patient->_vip}}
    <td class="text" colspan="4">
      <a href="#{{$_patient->_guid}}" onclick="reloadPatient('{{$_patient->_id}}', this);">
        Patient confidentiel
      </a>
    </td>
  {{else}}
    <td>
      <div style="float: right;">
        {{mb_include module=system template=inc_object_notes object=$_patient}}
      </div>

      {{if $_patient->_id == $patVitale->_id}}
      <div style="float: right;">
        <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="Bénéficiaire associé à la carte Vitale" />
      </div>
      {{/if}}

      <div class="text noted">
        <a href="#{{$_patient->_guid}}" onclick="reloadPatient('{{$_patient->_id}}', this);">
          {{mb_value object=$_patient field="_view"}}
        </a>
      </div>

    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">
        {{mb_value object=$_patient field="naissance"}}
      </span>
    </td>
    <td class="text compact">
      <span style="white-space: nowrap;">{{$_patient->adresse|spancate:30}}</span>
      <span style="white-space: nowrap;">{{$_patient->cp}} {{$_patient->ville|spancate:20}}</span>
    </td>
    <td>
      {{if !$board}}
        <a class="button search notext" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$_patient->_id}}"
           title="Afficher le dossier complet" style="margin: -1px;">
          {{tr}}Show{{/tr}}
        </a>
      {{else}}
        <button type="button" class="right notext" onclick="TdBTamm.loadTdbPatient('{{$_patient->_id}}');">
          {{tr}}Show{{/tr}}
        </button>
      {{/if}}
    </td>
  {{/if}}
</tr>
