{{mb_default var=show_atcd value=1}}

{{if $show_atcd}}
  {{mb_include module=soins template=inc_vw_antecedents}}
{{/if}}

{{if $dossier_medical->_id}}
  {{if $dossier_medical->_count_allergies}}
    <script>
      ObjectTooltip.modes.allergies = {
        module: "patients",
        action: "ajax_vw_allergies",
        sClass: "tooltip"
      };
    </script>
    <span class="texticon texticon-allergies-warning" onmouseover="ObjectTooltip.createEx(this, '{{$patient_guid}}', 'allergies');">Allergies</span>
  {{elseif $dossier_medical->_ref_allergies|@count}}
    <span class="texticon texticon-allergies-ok" title="Pas d'allergie connue">Allergies</span>
  {{/if}}
{{/if}}