{{mb_default var=vw_traitement_texte_libre value=1}}
{{mb_default var=addform                   value=""}}
{{mb_default var=callback                  value=""}}
{{mb_default var=gestion_tp                value=""}}
{{mb_default var=sejour_id                 value=""}}
{{mb_default var=reload                    value=""}}
{{mb_default var=type_see                  value=""}}
{{mb_default var=dossier_anesth_id         value=""}}

{{mb_script module="prescription" script="prescription" ajax=1}}
{{mb_script module="dPmedicament" script="medicament_selector" ajax=1}}

<script>
  Main.add(function() {
    if (!DossierMedical.patient_id) {
      DossierMedical.sejour_id  = '{{$sejour_id}}';
      {{if isset($_is_anesth|smarty:nodefaults)}}
        DossierMedical._is_anesth = '{{$_is_anesth}}';
      {{/if}}
      DossierMedical.patient_id = '{{$patient->_id}}';
      DossierMedical.dossier_anesth_id = '{{$dossier_anesth_id}}';
    }
    {{if $reload}}
    DossierMedical.reloadDossierPatient('{{$reload}}', '{{$type_see}}');
    {{/if}}

    if ($('tab_traitements_perso{{$addform}}')) {
      Control.Tabs.create('tab_traitements_perso{{$addform}}', false);
    }
  });
</script>

<div id="legend_actions_tp" style="display: none;">
  <table class="form">
    <tr>
      <th colspan="2" class="title">
        Légende
      </th>
    </tr>
    <tr>
      <td style="height: 40px"><button class="stop">Arrêter</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>Prescription de son arrêt</li>
      </td>
    </tr>
    <tr>
      <td class="text" style="height: 40px"><button class="edit">Represcrire en modifiant</button></td>
      <td>
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>ouverture de la ligne pour modification</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="height: 40px"><button class="right">Poursuivre</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel sans modification</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="height: 50px"><button class="hslip">Relai</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>Prescription de son arrêt</li>
          <li>Prescription d'un autre produit pour relai</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="height: 40px"><button class="pause">Pause</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>Prescription de son arrêt</li>
          <li>Represcription du même produit pour une date ultérieure</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="cancel" onclick="Control.Modal.close()">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>

{{assign var=traitement_enabled value="CAppUI::conf"|static_call:"dPpatients CTraitement enabled":"CGroups-$g"}}

<fieldset id="inc_ant_consult_fieldset_trt_perso{{$addform}}">
  <legend>Traitements personnels</legend>
  <table class="layout main">
    <tr>
      <td class="text">
        <ul id="tab_traitements_perso{{$addform}}" class="control_tabs small">
          {{if "dPprescription"|module_active && "dPprescription show_chapters med"|conf:"CGroups-$g"}}
            <li><a href="#tp_base_med{{$addform}}">Base de données de médicaments</a></li>
          {{/if}}
          {{if $traitement_enabled && $vw_traitement_texte_libre}}
            <li><a href="#tp_texte_simple{{$addform}}">Texte libre</a></li>
          {{/if}}
          {{if "dPprescription"|module_active && "dPprescription CPrescription show_element_tp"|conf:"CGroups-$g"}}
            <li><a href="#tp_nomenclature{{$addform}}">Nomenclature des éléments</a></li>
          {{/if}}
        </ul>
      </td>
    </tr>

    {{if "dPprescription"|module_active && "dPprescription show_chapters med"|conf:"CGroups-$g"}}
      <tr id="tp_base_med{{$addform}}">
        <td class="text">
          {{mb_include module=cabinet template=inc_antecedent_bdm}}
        </td>
      </tr>
    {{/if}}

    <!-- Traitements -->
    {{if $traitement_enabled && $vw_traitement_texte_libre}}
      <tr id="tp_texte_simple{{$addform}}">
        <td class="text">
          {{mb_include module=cabinet template=inc_antecedent_texte_libre}}
        </td>
      </tr>
    {{/if}}

    {{if "dPprescription"|module_active && "dPprescription CPrescription show_element_tp"|conf:"CGroups-$g"}}
      {{mb_include module=prescription template=vw_add_line_element_tp}}
    {{/if}}
  </table>
</fieldset>