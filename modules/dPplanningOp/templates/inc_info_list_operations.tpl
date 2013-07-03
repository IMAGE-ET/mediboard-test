{{mb_default var=offline value=0}}

{{foreach from=$sejour->_ref_operations item=_operation name=operation}}
<tr>
  <td>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
  </td>
  <td>{{$_operation->_datetime|date_format:$conf.date}} 
  {{if $_operation->annulee}}
  <th class="category cancelled">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}');">{{tr}}COperation-annulee{{/tr}}</strong>
  </th>
  {{else}}
  <td class="text">
    {{mb_include module=planningOp template=inc_vw_operation}}
  </td>
  {{/if}}
  
  {{if @$modules.brancardage->_can->read}}
    <td>
      {{mb_script module=brancardage script=creation_brancardage ajax=true}}
      <input id="modif" type="hidden" name="modif"/>
      <div id="patientpret-{{$_operation->sejour_id}}">
        {{mb_include module=brancardage template=inc_exist_brancard brancardage=$_operation->_ref_brancardage id="patientpret"
        sejour_id=$_operation->sejour_id salle_id=$_operation->salle_id operation_id=$_operation->_id reveil=false }}
      </div>
    </td>  
  {{/if}}
  
  <td class="narrow button">
    <button class="{{if $_operation->_ref_consult_anesth->_id}}print{{else}}warning{{/if}}" type="button"
    onclick="
    {{if $offline}}
      var fiche = $('fiche_anesth_{{$_operation->_id}}');
      if (fiche) {
    Modal.open(fiche);
      }
    {{else}}
      printFicheAnesth('{{$_operation->_ref_consult_anesth->_id}}', '{{$_operation->_id}}');
    {{/if}}">
      Fiche d'anesthésie
    </button>
    <br />
    <button class="print" style="width: 100%; min-width: 10em;" type="button" onclick="printFicheBloc('{{$_operation->_id}}');">
      Feuille de bloc
    </button>
    {{mb_include module=forms template=inc_widget_ex_class_register object=$_operation event_name=liaison}}
  </td>
</tr>

{{if $_operation->_back && array_key_exists("check_lists", $_operation->_back)}}
<tr>
  <td colspan="10">
    {{mb_include module=salleOp template=inc_vw_check_lists object=$_operation}}
  </td>
</tr>
{{/if}}
{{foreachelse}}
<tr>
  <td colspan="4" class="empty">{{tr}}COperation.none{{/tr}}</td>
</tr>
{{/foreach}}