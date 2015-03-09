{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_script module=planningOp script=prestations ajax=1}}
{{mb_script module=hospi script=modele_etiquette ajax=1}}

{{if @$modules.dPpmsi->_can->edit}}
  {{mb_script module=pmsi script=pmsi ajax=true}}
  {{mb_script module=planningOp script=cim10_selector ajax=true}}
  {{mb_script module=patients script=pat_selector ajax=true}}
{{/if}}

{{assign var=sejour       value=$object}}
{{assign var=patient      value=$object->_ref_patient}}
{{assign var=operations   value=$object->_ref_operations}}
{{assign var=affectations value=$object->_ref_affectations}}
{{unique_id var=unique_id}}

<script>
  popEtatSejour = function(sejour_id) {
    var url = new Url('hospi', 'vw_parcours');
    url.addParam('sejour_id', sejour_id);
    url.requestModal(700, 550);
  };

  printDossierSejour = function(sejour_id) {
    var url = new Url('planningOp', "view_planning");
    url.addParam("sejour_id", sejour_id);
    url.popup(700, 800);
  };


  afterValideSortie = function(form) {
    form.up('div').hide().update();
    if (window.refreshMouvements) {
      refreshMouvements(loadNonPlaces);
    }
  };

  ModeleEtiquette.nb_printers = {{$sejour->_nb_printers|@json}};
</script>

<form name="download_etiq_{{$object->_class}}_{{$sejour->_id}}_{{$unique_id}}" style="display: none;" target="_blank" method="get" class="prepared">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="a" value="print_etiquettes" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="object_class" value="{{$sejour->_class}}" />
  <input type="hidden" name="modele_etiquette_id" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="dialog" value="1" />
</form>

<table class="tbl tooltip">
  {{mb_include module=dPplanningOp template=inc_sejour_affectation_view}}
</table>

<table class="tbl tooltip">
  <tr>
    <th class="category {{if $sejour->sortie_reelle}}arretee{{/if}}" colspan="4">
      {{tr}}CSejour-_etat.{{$sejour->_etat}}{{/tr}}
      {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
    </th>
  </tr>
  
  {{if $sejour->annule == 1}}
    <tr>
      <th class="category cancelled" colspan="4">
        {{tr}}CSejour-annule{{/tr}}
        {{if $sejour->recuse == 1}}
          ({{tr}}CSejour.recuse.1{{/tr}})
        {{/if}}
      </th>
    </tr>
  {{/if}}
  
  <tr>
    <td class="button">
      {{mb_script module=planningOp script=sejour ajax=true}}
      {{mb_script module=dPadmissions script=admissions ajax=true}}

      {{if $object->_can->edit}}
      <button type="button" class="edit" onclick="Sejour.editModal('{{$sejour->_id}}');">
        {{tr}}Modify{{/tr}}
      </button>

      {{if !$sejour->entree_reelle}}
        <button class="tick" type="button"
                onclick='Admissions.validerEntree("{{$sejour->_id}}", false);'>
          Valider l'entrée
        </button>
      {{else}}
        <button class="cancel" type="button"
                onclick='Admissions.validerEntree("{{$sejour->_id}}", false);'>
          Valider l'entrée
        </button>
      {{/if}}

      {{if !$sejour->sortie_reelle && $sejour->entree_reelle}}
        <button class="tick" type="button"
                onclick='Admissions.validerSortie("{{$sejour->_id}}", false);'>
          Valider la sortie
        </button>
      {{/if}}

      {{if $conf.dPhospi.systeme_prestations == "expert"}}
        <button type="button" class="search" onclick="Prestations.edit('{{$sejour->_id}}')">Prestations</button>
      {{/if}}
      {{/if}}
      
      {{if $sejour->type != "urg" && @$modules.dPadmissions->_can->read}}
      <button type="button" class="search" onclick="Sejour.admission('{{$sejour->_date_entree_prevue}}');">
        {{tr}}Admission{{/tr}}
      </button>
      {{/if}}
      
      {{if @$modules.soins->_can->read}}
      <button type="button" class="search" onclick="Sejour.showDossierSoinsModal('{{$sejour->_id}}')">
        {{tr}}module-soins-court{{/tr}}
      </button>
      {{/if}}

      {{if @$modules.dPpmsi->_can->edit}}
        <button type="button" class="search" onclick="Sejour.showDossierPmsi('{{$sejour->_id}}', '{{$patient->_id}}');">
          {{tr}}mod-dPpmsi-tab-vw_dossier_pmsi{{/tr}}
        </button>
      {{/if}}

      {{if $sejour->type == "ssr" && @$modules.ssr->_can->read}}
      <button type="button" class="search" onclick="Sejour.showSSR('{{$sejour->_id}}');">
        {{tr}}module-ssr-long{{/tr}}
      </button>
      {{/if}}

      {{if $sejour->type == "urg" && @$modules.dPurgences->_can->read}}
      <button type="button" class="search" onclick="Sejour.showUrgences('{{$sejour->_id}}');">
        {{tr}}module-dPurgences-long{{/tr}}
      </button>
      {{/if}}

      <br />
      <button type="button" class="print" onclick="printDossierSejour('{{$sejour->_id}}');">{{tr}}Print{{/tr}}</button>
      
      {{if @$modules.dPhospi->_can->read}}
        <button type="button" class="print"
          {{if $sejour->_count_modeles_etiq == 1}}
            onclick="ModeleEtiquette.print('{{$sejour->_class}}', '{{$sejour->_id}}', null, '{{$unique_id}}');"
          {{else}}
            onclick="ModeleEtiquette.chooseModele('{{$sejour->_class}}', '{{$sejour->_id}}', '{{$unique_id}}')"
          {{/if}}>
          {{tr}}CModeleEtiquette.print_labels{{/tr}}
        </button>
      {{/if}}
      
      <button type="button" class="lookup" onclick="popEtatSejour('{{$sejour->_id}}');">État du séjour</button>
      
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe notext="" _sejour=$sejour}}
      {{/if}}

      {{if "forms"|module_active}}
        {{mb_include module=forms template=inc_widget_ex_class_register object=$sejour event_name=modification cssStyle="display: inline-block;"}}
      {{/if}}
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
