<script type="text/javascript">

submitPrepaForm = function(oFormPrepa) {
  submitFormAjax(oFormPrepa,'systemMsg', {onComplete: function(){ refreshTabsReveil() }});
}

</script>

<table class="tbl">
  <tr>
    <th>Heure</th>
    <th>Salle</th>
    <th>Praticien</th>
    <th>Patient</th>
    <th>Interv</th>
    <th>Coté</th>
    <th>debut prépa.</th>
    <th>fin prépa.</th>
  </tr>
{{foreach from=$listOperations item=_operation}}
  <tr>
    <td class="text">
      {{if $_operation->rank}}
        {{$_operation->_datetime|date_format:$dPconfig.time}}
      {{else}}
        NP
      {{/if}}
    </td>
    <td>{{$_operation->_ref_salle->_shortview}}</td>
    <td class="text">
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
    </td>
    <td class="text">
      <div style="float: right; display: inline">
        <a href="#" onclick="codageCCAM('{{$_operation->_id}}');">
        <img src="images/icons/anesth.png" alt="Anesth" />
        </a>
      </div>
      <a href="?m={{$m}}&amp;tab=vw_soins_reveil&amp;operation_id={{$_operation->_id}}">
      <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
        {{$_operation->_ref_patient->_view}}
      </span>
      </a>
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
    <td class="button">
      {{if $can->edit || $modif_operation}}
        <form name="editDebutPreopFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="del" value="0" />
          {{if $_operation->debut_prepa_preop}}
            {{assign var=_operation_id value=$_operation->_id}}
            {{mb_field object=$_operation field=debut_prepa_preop form="editDebutPreopFrm$_operation_id"}}
          {{else}}
            <input type="hidden" name="debut_prepa_preop" value="now" />
          {{/if}}
          <button class="tick notext" type="button" onclick="submitPrepaForm(this.form);">{{tr}}Modify{{/tr}}</button>
        </form>
      {{else}}
        {{mb_value object=$_operation field="debut_prepa_preop"}}
      {{/if}}
    </td>
    <td class="button">
      {{if $can->edit || $modif_operation}}
        <form name="editFinPreopFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="del" value="0" />
          {{if $_operation->fin_prepa_preop}}
            {{assign var=_operation_id value=$_operation->_id}}
            {{mb_field object=$_operation field=fin_prepa_preop form="editFinPreopFrm$_operation_id"}}
          {{else}}
            <input type="hidden" name="fin_prepa_preop" value="now" />
          {{/if}}
          <button class="tick notext" type="button" onclick="submitPrepaForm(this.form);">{{tr}}Modify{{/tr}}</button>
        </form>
      {{else}}
        {{mb_value object=$_operation field="fin_prepa_preop"}}
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $('lipreop').innerHTML = {{$listOperations|@count}};
</script>