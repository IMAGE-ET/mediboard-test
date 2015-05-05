<script>
  operationEditCallback = function() { window.url_edit_planning.refreshModal();};

  ObjectTooltip.modes.allergies = {  
    module: "patients",
    action: "ajax_vw_allergies",
    sClass: "tooltip"
  };
  
  Main.add(function(){
    var options = {
      exactMinutes: false, 
      minInterval : {{$conf.dPplanningOp.COperation.min_intervalle}},
      minHours    : {{$conf.dPplanningOp.COperation.duree_deb}},
      maxHours    : {{$conf.dPplanningOp.COperation.duree_fin}}
    };
    {{foreach from=$intervs item=_op}}
    oForm = getForm("edit-interv-{{$list_type}}-{{$_op->_id}}");
    Calendar.regField(oForm.temp_operation, null, options);
    Calendar.regField(oForm.duree_preop);
    if(oForm.pause) {
      Calendar.regField(oForm.pause);
    }
    {{/foreach}}
  });

</script>

<table class="tbl">
  
{{if $list_type == "left"}}
  <tr>
    <th class="title" colspan="4">
      <form name="editOrderVoulu" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_order_voulu_op" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="del" value="0" />
        <button type="button" class="tick oneclick" style="float: right;" onclick="submitOrder(this.form);">
          Utiliser l'ordre souhaité
        </button>
      </form>
      Patients à placer
    </th>
  </tr>
{{else}}
  <tr>
    <th class="title" colspan="4">Ordre des interventions</th>
  </tr>
{{/if}}

{{foreach from=$intervs item=_op}}
  {{assign var=sejour  value=$_op->_ref_sejour}}
  {{assign var=patient value=$sejour->_ref_patient}}
  {{assign var=consult_anesth value=$_op->_ref_consult_anesth}}
  <tr>
    <td class="text" style="vertical-align: top;">
      {{mb_include module=system template=inc_object_history object=$_op}}
      {{if $patient->_ref_dossier_medical->_id && $patient->_ref_dossier_medical->_count_allergies}}
        <img src="images/icons/warning.png" style="float: right" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" />
      {{/if}}
      <strong>
        {{if $_op->rank}}
          <div class="rank" style="float: left;{{if $_op->annulee}}background: #800; color: #fff;{{/if}}">{{$_op->rank}}</div>
          {{$_op->time_operation|date_format:$conf.time}}
        {{elseif $_op->rank_voulu}}
          <div class="rank desired" style="float: left;{{if $_op->annulee}}background: #800; color: #fff;{{/if}}">{{$_op->rank_voulu}}</div>
        {{else}}
          <div class="rank" style="float: left;{{if $_op->annulee}}background: #800; color: #fff;{{/if}}"></div>
        {{/if}}
        <a href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$patient->_id}}">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
            {{$patient}} ({{$patient->_age}})
          </span>
        </a>
      </strong>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
      <br />
      <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');" {{if !$sejour->entree_reelle}}style="color: red;"{{/if}}>
       Adm. le {{mb_value object=$sejour field=_entree}} ({{$sejour->type|truncate:1:""|capitalize}})
      </span>
      {{if $_op->horaire_voulu}}
        <br />
        Passage souhaité à {{$_op->horaire_voulu|date_format:$conf.time}}
      {{/if}}
      <div style="text-align: right;">
      <form name="edit-interv-{{$list_type}}-{{$_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
        {{mb_label object=$_op field="temp_operation"}}
        {{mb_field object=$_op field="temp_operation" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
        <br />
        {{mb_label object=$_op field="duree_preop"}}
        {{mb_field object=$_op field="duree_preop" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
        {{if $_op->rank}}
          <br />
          {{mb_label object=$_op field="pause"}}
          {{mb_field object=$_op field="pause" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
        {{elseif $listPlages|@count > 1}}
          <br />
          Changement de salle
          <select name="plageop_id" onchange="submitOrder(this.form);">
            {{foreach from=$listPlages item="_plage"}}
            <option value="{{$_plage->_id}}" {{if $plage->_id == $_plage->_id}} selected = "selected"{{/if}}>
            {{$_plage->_ref_salle->nom}} / {{$_plage->debut|date_format:$conf.time}} à {{$_plage->fin|date_format:$conf.time}}
            </option>
            {{/foreach}}
          </select>
        {{/if}}
      </form>
      </div>
    </td>
    
    <td class="text" style="vertical-align: top;">
      <a onclick="Operation.editModal({{$_op->_id}}, {{$_op->plageop_id}}, function() { window.url_edit_planning.refreshModal();} );" href="#">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}');">
          {{if $_op->libelle}}
            <strong>{{$_op->libelle}}</strong>
          {{else}}
          {{foreach from=$_op->_ext_codes_ccam item=curr_code}}
            <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong|truncate:60:"...":false}}<br />
          {{/foreach}}
          {{/if}}
        </span>
      </a>
      
      <em>{{mb_label object=$_op field=cote}}</em> :
      {{mb_value object=$_op field=cote}}
      {{if $_op->exam_extempo}}
        <br />
        <span class="texticon texticon-extempo" title="{{tr}}COperation-exam_extempo{{/tr}}">Ext</span>
      {{/if}}
      {{if $_op->materiel}}
        <br />
        <em>{{mb_label object=$_op field=materiel}}</em> :
        {{mb_value object=$_op field=materiel}}
      {{/if}}
      {{if $_op->exam_per_op}}
        <br /><em>{{mb_label object=$_op field=exam_per_op}}</em> :
        {{mb_value object=$_op field=exam_per_op}}
      {{/if}}
      {{mb_include module=bloc template=inc_rques_intub operation=$_op}}
    </td>
    <td class="narrow" style="vertical-align: top;">
      <form name="editFrmAnesth{{$_op->operation_id}}" action="?m={{$m}}" method="post" style="float: right;">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_op->operation_id}}" />
        <select name="type_anesth" onchange="submitOrder(this.form, '{{$list_type}}');" style="width: 11em; clear: both;">
          <option value="">&mdash; Anesthésie</option>
          {{foreach from=$anesth item=curr_anesth}}
            {{if $curr_anesth->actif || $_op->type_anesth == $curr_anesth->type_anesth_id}}
              <option value="{{$curr_anesth->type_anesth_id}}" {{if $_op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}}>
                {{$curr_anesth->name}}{{if !$curr_anesth->actif && $_op->type_anesth == $curr_anesth->type_anesth_id}}(Obsolète){{/if}}
              </option>
            {{/if}}
          {{/foreach}}
        </select>
        <br />
        <button type="button" style="clear: both; width:11em;"
                class="{{if $consult_anesth->_ref_consultation->_id}}print{{else}}warning{{/if}}"
                onclick="printFicheAnesth('{{$consult_anesth->_id}}', '{{$_op->_id}}');">
          Fiche d'anesthésie
        </button>
        <br />
        <button type="button" class="search" onclick="extraInterv('{{$_op->_id}}')"
          {{if ($_op->salle_id && $_op->salle_id != $_op->_ref_plageop->salle_id) || $_op->_count_affectations_personnel}}
          style="font-weight: bold"
          {{/if}}>Extra</button>
        {{if $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
          <br />
          {{mb_include module=bloc template=inc_button_besoins_ressources type=operation_id object_id=$_op->_id usage=1}}
        {{/if}}
      </form>
    </td>
    <td class="narrow" style="text-align: center;">
      <!-- Intervention à valider -->
      {{if $_op->annulee && !$conf.dPplanningOp.COperation.save_rank_annulee_validee}}
        <img src="images/icons/cross.png" />
      {{elseif $_op->rank == 0}}
        <form name="edit-insert-{{$_op->operation_id}}" action="?m={{$m}}" method="post" class="prepared">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="_move" value="last" /><!-- Insertion à la fin -->
          <input type="hidden" name="operation_id" value="{{$_op->operation_id}}" />
          <button type="button" class="tick notext oneclick" title="{{tr}}Add{{/tr}}" onclick="submitOrder(this.form);">
            {{tr}}Add{{/tr}}
          </button>
        </form>
      {{else}}
      
        <!-- Intervention validée -->
        {{if $_op->rank != 1}}
          <form name="edit-up-{{$_op->_id}}" action="?m={{$m}}" method="post" class="prepared">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
            <input type="hidden" name="_move" value="before" />
            <button type="button" class="up notext oneclick" title="{{tr}}Up{{/tr}}" onclick="submitOrder(this.form, '{{$list_type}}');">
              {{tr}}Up{{/tr}}
            </button>
          </form>
          <br />
        {{/if}}
        
        <form name="edit-del-{{$_op->_id}}" action="?m={{$m}}" method="post" class="prepared">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
          <input type="hidden" name="_move" value="out" />
          <button type="button" class="cancel notext oneclick" title="{{tr}}Delete{{/tr}}" onclick="submitOrder(this.form);">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        <br />
        
        {{if $_op->rank != $intervs|@count}}
        <form name="edit-down-{{$_op->_id}}" action="?m={{$m}}" method="post" class="prepared">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
          <input type="hidden" name="_move" value="after" />
          <button type="button" class="down notext oneclick" title="{{tr}}Down{{/tr}}" onclick="submitOrder(this.form, '{{$list_type}}');">
            {{tr}}Down{{/tr}}
          </button>
        </form>
        <br />
        {{/if}}
      {{/if}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td class="empty">{{tr}}COperation.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>