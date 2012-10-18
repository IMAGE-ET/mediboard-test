{{mb_script module=planningOp script=prestations ajax=1}}
{{mb_script module=hospi script=modele_etiquette ajax=1}}
{{assign var=sejour       value=$object}}
{{assign var=patient      value=$object->_ref_patient}}
{{assign var=operations   value=$object->_ref_operations}}
{{assign var=affectations value=$object->_ref_affectations}}


<script type="text/javascript">
  popEtatSejour = function(sejour_id) {
    var url = new Url('hospi', 'vw_parcours');
    url.addParam('sejour_id', sejour_id);
    url.requestModal(700, 550);
  }
  
  ModeleEtiquette.nb_printers = {{$sejour->_nb_printers|@json}};
</script>

<form name="download_etiq_{{$object->_class}}_{{$sejour->_id}}" style="display: none;" action="?" target="_blank" method="get" class="prepared">
  <input type="hidden" name="m" value="dPhospi" />
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
      </th>
    </tr>
  {{/if}}
  
  <tr>
    <td class="button">
      {{mb_script module=planningOp script=sejour ajax=true}}

      {{if $can->edit}}
      <button type="button" class="edit" onclick="Sejour.edit('{{$sejour->_id}}');">
        {{tr}}Modify{{/tr}}
      </button>
      {{if $conf.dPhospi.systeme_prestations == "expert"}}
        <button type="button" class="search" onclick="Prestations.edit('{{$sejour->_id}}')">Prestations</button>
      {{/if}}
      {{/if}}
      
      {{if $sejour->type != "urg" && @$modules.dPadmissions->_can->read}}
      <button type="button" class="search" onclick="Sejour.admission('{{$sejour->_date_entree_prevue}}');">
        {{tr}}Admission{{/tr}}
      </button>
      {{/if}}
      
      {{if $sejour->type != "urg" && $sejour->type != "ssr" && @$modules.soins->_can->read}}
      <button type="button" class="search" onclick="Sejour.showDossierSoins('{{$sejour->_id}}')">
        {{tr}}module-soins-court{{/tr}}
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
    
      {{if @$modules.brancardage->_can->read}}
        {{mb_script module=brancardage script=creation_brancardage ajax=true}}
        <button type="button" class="edit" onclick="CreationBrancard.edit('{{$sejour->_id}}');" style="width:80px;">
        {{tr}}module-Brancardage-long{{/tr}}
        </button>
      {{/if}}
      
      {{if @$modules.dPhospi->_can->read}}
        <br />
        <button type="button" class="print"
          {{if $sejour->_count_modeles_etiq > 1}}
            onclick="ModeleEtiquette.print('{{$sejour->_class}}', '{{$sejour->_id}}');"
          {{else}}
            onclick="ModeleEtiquette.chooseModele('{{$sejour->_class}}', '{{$sejour->_id}}')"
          {{/if}}>
          {{tr}}CModeleEtiquette.print_labels{{/tr}}
        </button>
      {{/if}}
      
      <button type="button" class="lookup" onclick="popEtatSejour('{{$sejour->_id}}');">État du séjour</button>
      
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe notext="" _sejour=$sejour}}
      {{/if}}
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
