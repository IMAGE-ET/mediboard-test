<script type="text/javascript">

reloadPrescriptionAnesth = function(prescription_id){
  reloadPrescription(prescription_id);
  reloadAnesth('{{$selOp->_id}}');
}


</script>
<table class="form">
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>Infos intervention</legend>
        <form name="anesthInterv" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
          <input type="hidden" name="del" value="0" />
          {{mb_label object=$selOp field=anesth_id}}
          {{if $modif_operation}}
          <select name="anesth_id" onchange="submitAnesth(this.form);">
            <option value="">&mdash; Anesthésiste</option>
            {{foreach from=$listAnesths item=curr_anesth}}
            <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
              {{$curr_anesth->_view}}
            </option>
            {{/foreach}}
          </select>
          {{elseif $selOp->_ref_anesth->user_id}}
            {{assign var="keyChir" value=$selOp->_ref_anesth->user_id}}
            {{assign var="typeChir" value=$listAnesths.$keyChir}}
            {{$typeChir->_view}}
          {{else}}
            -
          {{/if}}
        </form>
        {{if $selOp->_ref_consult_anesth->_id}}
        <br />
        <form name="editInfosASAFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
          {{mb_key object=$selOp->_ref_consult_anesth}}
          {{mb_label object=$selOp->_ref_consult_anesth field="ASA" style="padding-left: 4em;"}}
          {{mb_field object=$selOp->_ref_consult_anesth field="ASA" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
          <br />
          {{mb_label object=$selOp->_ref_consult_anesth field="position" style="padding-left: 2.5em;"}}
          {{mb_field object=$selOp->_ref_consult_anesth field="position" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
        </form>
        {{/if}}
      </fieldset>
    </td>
    <td class="halfPane">
      <fieldset>
        <legend>Infos induction</legend>
        <form name="anesthTiming" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="layout">
          <tr>
            <td colspan="2">
              {{if $modif_operation}}
              <select name="type_anesth" onchange="submitAnesth(this.form);">
                <option value="">&mdash; Type d'anesthésie</option>
                {{foreach from=$listAnesthType item=curr_anesth}}
                <option value="{{$curr_anesth->type_anesth_id}}" {{if $selOp->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
                  {{$curr_anesth->name}}
                </option>
               {{/foreach}}
              </select>
              {{elseif $selOp->type_anesth}}
                {{assign var="keyAnesth" value=$selOp->type_anesth}}
                {{assign var="typeAnesth" value=$listAnesthType.$keyAnesth}}
                {{$typeAnesth->name}}
              {{else}}
                -
              {{/if}}
            </td>
          </tr>
          <tr>
            {{include file=inc_field_timing.tpl object=$selOp form="anesthTiming" field=induction_debut submit=submitAnesth}}
            {{include file=inc_field_timing.tpl object=$selOp form="anesthTiming" field=induction_fin submit=submitAnesth}}
          </tr>
        </table>
        </form>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <fieldset>
        <legend>Horodatage intervention</legend>
          <table class="main layout">
            <tr>
              <th style="font-weight: bold">{{mb_label object=$selOp field=entree_salle}}</th>
              <td>{{mb_value object=$selOp field=entree_salle}}</td>
              <th style="font-weight: bold">{{mb_label object=$selOp field=pose_garrot}}</th>
              <td>{{mb_value object=$selOp field=pose_garrot}}</td>
              <th style="font-weight: bold">{{mb_label object=$selOp field=debut_op}}</th>
              <td>{{mb_value object=$selOp field=debut_op}}</td>
            </tr>
            <tr>
              <th style="font-weight: bold">{{mb_label object=$selOp field=sortie_salle}}</th>
              <td>{{mb_value object=$selOp field=sortie_salle}} </td>
              <th style="font-weight: bold">{{mb_label object=$selOp field=retrait_garrot}}</th>
              <td>{{mb_value object=$selOp field=retrait_garrot}}</td>
              <th style="font-weight: bold">{{mb_label object=$selOp field=fin_op}}</th>
              <td>{{mb_value object=$selOp field=fin_op}}</td>
            </tr>
        </table>
      </fieldset>
    </td>
  </tr>
</table>