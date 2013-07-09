<!-- $Id$ -->

<script type="text/javascript">
printFicheAnesth = function(dossier_anesth_id, operation_id) {
  var url = new Url("cabinet", "print_fiche");
  url.addParam("dossier_anesth_id", dossier_anesth_id);
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheAnesth");
}

chooseAnesthCallback = function() {
  loadSejour({{$sejour->_id}}); 
}

printFicheBloc = function(operation_id) {
  var url = new Url("salleOp", "print_feuille_bloc");
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheBloc");
}

refreshListIntervs = function() {
  {{if !$sejour->_id}}
    return false;
  {{else}}
    var url = new Url("planningOp", "ajax_vw_operations_sejour");
    url.addParam("sejour_id", {{$sejour->_id}});
    url.requestUpdate("intervs-sejour-{{$sejour->_guid}}");
  {{/if}}
}
</script>

{{mb_default var=offline value=0}}

{{if $sejour->_canRead}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="{{if @$modules.brancardage->_can->read}}5{{else}}4{{/if}}">
        {{if $sejour->_ref_consult_anesth->_id && !$sejour->_ref_consult_anesth->operation_id}}
          <button style="float: right" class="print" type="button" onclick="printFicheAnesth('{{$sejour->_ref_consult_anesth->_id}}');">
            Fiche d'anesthésie
          </button>
        {{/if}}
        {{tr}}CSejour-back-operations{{/tr}}
      </th>
    </tr>
    <tr>
      <th>{{tr}}COperation-chir_id{{/tr}}</th>
      <th>{{tr}}Date{{/tr}}</th>
      <th>{{tr}}COperation-_ext_codes_ccam{{/tr}}</th>
      {{if @$modules.brancardage->_can->read}}
        <th>{{tr}}CBrancardage{{/tr}}</th>
      {{/if}}
      <th></th>
    </tr>
    <tbody id="intervs-sejour-{{$sejour->_guid}}">
      {{mb_include module=planningOp template=inc_info_list_operations}}
    </tbody>
  </table>
{{elseif $sejour->_id}}
  <div class="small-info">Vous n'avez pas accès au détail des interventions.</div>
{{/if}}