{{mb_include module=soins template=inc_vw_antecedents}}

{{if $dossier_medical->_id}}
  {{if $dossier_medical->_count_allergies}}
    <script>
      ObjectTooltip.modes.allergies = {
        module: "patients",
        action: "ajax_vw_allergies",
        sClass: "tooltip"
      };
    </script>
    <img src="images/icons/allergies_warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$patient_guid}}', 'allergies');" >

  {{elseif $dossier_medical->_ref_allergies|@count}}
    <img src="images/icons/allergies_ok.png" title="Pas d'allergie connue">
  {{/if}}
{{/if}}