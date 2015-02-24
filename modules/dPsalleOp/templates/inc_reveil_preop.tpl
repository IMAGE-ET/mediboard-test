{{assign var=use_poste value=$conf.dPplanningOp.COperation.use_poste}}

{{if $require_check_list}}
  <table class="main layout">
    <tr>
      {{foreach from=$daily_check_lists item=check_list}}
        <td>
          <h2>{{$check_list->_ref_list_type->title}}</h2>
          {{if $check_list->_ref_list_type->description}}
            <p>{{$check_list->_ref_list_type->description}}</p>
          {{/if}}

          {{mb_include module=salleOp template=inc_edit_check_list
          check_list=$check_list
          check_item_categories=$check_list->_ref_list_type->_ref_categories
          list_chirs=$listChirs
          list_anesths=$listAnesths
          personnel=$personnels}}
        </td>
      {{/foreach}}
    </tr>
  </table>
  {{mb_return}}
{{/if}}

<script>
  submitPrepaForm = function(oFormPrepa) {
    onSubmitFormAjax(oFormPrepa, refreshTabsReveil);
  };
  
  Main.add(function() {
    Control.Tabs.setTabCount("preop", "{{$listOperations|@count}}");

    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  orderTabpreop = function(col, way) {
    orderTabReveil(col, way, 'preop');
  };
</script>

<table class="tbl">
  <tr>
    <th>{{mb_colonne class="COperation" field="time_operation" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    <th>{{mb_colonne class="COperation" field="salle_id" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    <th>{{mb_colonne class="COperation" field="chir_id" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    <th>{{mb_colonne class="COperation" field="_patient" order_col=$order_col order_way=$order_way function=orderTabpreop}} <input type="text" name="_seek_patient_preop" value="" class="seek_patient" onkeyup="seekPatient(this);" onchange="seekPatient(this);" /> </th>
    {{if $use_poste}}
      <th>{{mb_colonne class="COperation" field="poste_preop_id" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    {{/if}}
    <th class="narrow">Dossier</th>
    <th>{{mb_colonne class="COperation" field="libelle" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    <th>{{mb_colonne class="COperation" field="cote" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    {{if @$modules.brancardage->_can->read}}
      <th>{{tr}}CBrancardage{{/tr}}</th>
    {{/if}}
    <th class="narrow">{{mb_colonne class="COperation" field="debut_prepa_preop" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    <th class="narrow">{{mb_colonne class="COperation" field="fin_prepa_preop" order_col=$order_col order_way=$order_way function=orderTabpreop}}</th>
    <th class="narrow"></th>
  </tr>
  {{foreach from=$listOperations item=_operation}}
    {{assign var=patient value=$_operation->_ref_patient}}
    {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
    {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
    {{assign var=sejour_id value=$_operation->sejour_id}}
    {{assign var=_operation_id value=$_operation->_id}}
    <tr>
      <td class="text">
        {{if $_operation->rank}}
          {{$_operation->_datetime|date_format:$conf.time}}
        {{else}}
          NP
        {{/if}}
      </td>
      <td>{{$_operation->_ref_salle->_shortview}}</td>
      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
      </td>
      <td class="text">
          <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}} CPatient-view"
                onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}');">
            {{$_operation->_ref_patient}}
          </span>
      </td>
      {{if $use_poste}}
        <td>
          {{mb_include module=dPsalleOp template=inc_form_toggle_poste_preop}}
        </td>
      {{/if}}
      <td>
        <button class="button soins notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}');">
          Dossier séjour
        </button>
        {{if $isImedsInstalled}}
          <button class="labo button notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}','Imeds');">Labo</button>
          {{mb_include module=Imeds template=inc_sejour_labo link="#1" sejour=$_operation->_ref_sejour float="none"}}
        {{/if}}
        <button type="button" class="injection notext" onclick="Operation.dossierBloc('{{$_operation->_id}}', true);">Dossier de bloc</button>
        {{mb_include module=soins template=inc_antecedents_allergies patient_guid=$_operation->_ref_patient->_guid show_atcd=0}}

      </td>
      <td class="text">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
          {{if $_operation->libelle}}
            {{$_operation->libelle}}
          {{else}}
            {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
              {{$curr_code->code}}
            {{/foreach}}
          {{/if}}
        </span>
      </td>
      <td class="text">{{mb_value object=$_operation field="cote"}}</td>
      {{if @$modules.brancardage->_can->read}}
        <td>
           <span id="demande_brancard-{{$_operation->sejour_id}}">
             {{mb_include module=brancardage template=inc_exist_brancard colonne="demande_brancard" reveil="preop" destination="CBlocOperatoire"
             destination_guid="CBlocOperatoire-$bloc_id" date_brancard=$_operation->_datetime|date_format:"%Y-%m-%d"}}
           </span>
        </td>
      {{/if}}
      <td style="text-align: center;">
        {{if $modif_operation}}
          <form name="editDebutPreopFrm{{$_operation->_id}}" action="?" method="post">
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            {{mb_key object=$_operation}}
            <input type="hidden" name="del" value="0" />
            {{if $_operation->debut_prepa_preop}}
              {{mb_field object=$_operation field=debut_prepa_preop form="editDebutPreopFrm$_operation_id" onchange="submitPrepaForm(this.form);"}}
            {{else}}
              <input type="hidden" name="debut_prepa_preop" value="now" />
              <button class="tick notext" type="button" onclick="submitPrepaForm(this.form);">{{tr}}Modify{{/tr}}</button>
            {{/if}}
          </form>
        {{else}}
          {{mb_value object=$_operation field="debut_prepa_preop"}}
        {{/if}}

        {{mb_include module=forms template=inc_widget_ex_class_register object=$_operation event_name=preop cssStyle="display: inline-block;"}}
      </td>
      <td class="button">
        {{if $modif_operation}}
          <form name="editFinPreopFrm{{$_operation->_id}}" action="?" method="post">
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            {{mb_key object=$_operation}}
            <input type="hidden" name="del" value="0" />
            {{if $_operation->fin_prepa_preop}}
              {{mb_field object=$_operation field=fin_prepa_preop form="editFinPreopFrm$_operation_id" onchange="submitPrepaForm(this.form);"}}
            {{else}}
              <input type="hidden" name="fin_prepa_preop" value="now" />
              <button class="tick notext" type="button" onclick="submitPrepaForm(this.form);">{{tr}}Modify{{/tr}}</button>
            {{/if}}
          </form>
        {{else}}
          {{mb_value object=$_operation field="fin_prepa_preop"}}
        {{/if}}
      </td>
      <td>
        <button type="button" class="print notext"
          onclick="printDossier('{{$_operation->sejour_id}}', '{{$_operation->_id}}')"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="20" class="empty">{{tr}}COperation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
