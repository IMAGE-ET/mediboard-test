{{*
 * $Id$
 *  
 * @category salleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
{{mb_default  var=to_remove         value=false}}

{{if $to_remove}}
  <script>
    removeLine('{{$op->_id}}');
  </script>
{{else}}
  {{assign      var=sejour            value=$op->_ref_sejour}}
  {{assign      var=consult_anesth    value=$op->_ref_consult_anesth}}
  {{assign      var=anesth            value=$op->_ref_anesth}}
  {{assign      var=patient           value=$sejour->_ref_patient}}
  {{assign      var=systeme_materiel  value=$conf.dPbloc.CPlageOp.systeme_materiel}}

  <td>
    <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}"
          onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
      {{$patient}}
    </span>
  </td>

  <td>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$op->_ref_chir}}
  </td>

  <td>
    <form name="editPlageFrm{{$op->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : refreshLine.curry('{{$op->_id}}')})">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_planning_aed" />
      <input type="hidden" name="operation_id" value="{{$op->_id}}" />
      <select name="anesth_id" style="width: 15em;" onchange="this.form.onsubmit()">
        <option value="">&mdash; Anesthésiste</option>
        {{foreach from=$anesths item=_anesth}}
          <option value="{{$_anesth->_id}}" {{if $_anesth->_id == $anesth->_id}}selected="selected"{{/if}}>{{$_anesth}}</option>
        {{/foreach}}
      </select>
    </form>
  </td>

  {{if $op->annulee}}
    <td colspan="3" class="cancelled">
      Annulée
    </td>
  {{else}}
    <td class="text">
      {{if !$op->annulee}}
        <form name="editTimeFrm{{$op->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : refreshLine.curry('{{$op->_id}}')})">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$op->_id}}" />
          {{assign var=op_id value=$op->_id}}
          {{mb_field object=$op field=time_operation form="editTimeFrm$op_id" register=true onchange="this.form.onsubmit()"}}
        </form>
      {{else}}
        {{mb_value object=$op field=time_operation}}
      {{/if}}
    </td>

    <td class="text">
      {{if !$op->annulee}}
        <form name="editSalleFrm{{$op->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : refreshLine.curry('{{$op->_id}}')})">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$op->_id}}" />
          <select  style="width: 15em;" name="salle_id" onchange="this.form.onsubmit()">
            <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
            {{foreach from=$listBlocs item=_bloc}}
              <optgroup label="{{$_bloc}}">
                {{foreach from=$_bloc->_ref_salles item=_salle}}
                  <option value="{{$_salle->_id}}" {{if $_salle->_id == $op->salle_id}}selected="selected"{{/if}}>
                    {{$_salle}}
                  </option>
                  {{foreachelse}}
                  <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                {{/foreach}}
              </optgroup>
            {{/foreach}}
          </select>
        </form>
        {{if $op->_alternate_plages|@count}}
          <form name="editPlageFrm{{$op->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : refreshLine.curry('{{$op->_id}}')})">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$op->_id}}" />
            <input type="hidden" name="date" value="" />
            <input type="hidden" name="time_op" value="" />
            <input type="hidden" name="salle_id" value="" />
            <input type="hidden" name="horaire_voulu" value="{{$op->time_operation}}" />
            <select name="plageop_id" style="width: 15em;" onchange="this.form.onsubmit()">
              <option value="">&mdash; {{tr}}COperation-action-to_plageop{{/tr}}</option>
              {{foreach from=$op->_alternate_plages item=_plage}}
                <option value="{{$_plage->_id}}">{{$_plage->_ref_salle}} - {{mb_value object=$_plage field=debut}} à {{mb_value object=$_plage field=fin}} - {{$_plage}}</option>
              {{/foreach}}
            </select>
          </form>
        {{/if}}
      {{else}}
        {{mb_value object=$op field=salle_id}}
      {{/if}}
      {{if $systeme_materiel == "expert"}}
        {{mb_include module=dPbloc template=inc_button_besoins_ressources type=operation_id usage=1 object_id=$op->_id}}
      {{/if}}
    </td>
    {{if "dPsalleOp hors_plage type_anesth"|conf:"CGroups-$g"}}
      <td>
        <form name="editTypeAnesthFrm{{$op->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : refreshLine.curry('{{$op->_id}}')})">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_operation_aed" />
          <input type="hidden" name="operation_id" value="{{$op->_id}}" />
          <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
          <input type="hidden" name="chir_id" value="{{$op->_ref_chir->_id}}" />
          <select name="type_anesth" style="width: 12em;" onchange="this.form.onsubmit()">
            <option value="">&mdash; Anesthésie</option>
            {{foreach from=$listAnesthType item=curr_anesth}}
              {{if $curr_anesth->actif || $op->type_anesth == $curr_anesth->type_anesth_id}}
                <option value="{{$curr_anesth->type_anesth_id}}" {{if $op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}}>
                  {{$curr_anesth->name}} {{if !$curr_anesth->actif && $op->type_anesth == $curr_anesth->type_anesth_id}}(Obsolète){{/if}}
                </option>
              {{/if}}
            {{/foreach}}
          </select>
        </form>
      </td>
    {{/if}}
    {{if "dPsalleOp hors_plage heure_entree_sejour"|conf:"CGroups-$g"}}
      <td>
        <form name="editEntreeFrm{{$op->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : refreshLine.curry('{{$op->_id}}')})">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_sejour_aed" />
          <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
          {{assign var=op_id value=$op->_id}}
          {{mb_field object=$sejour field=entree_reelle form="editEntreeFrm$op_id" register=true onchange="this.form.onsubmit()"}}
        </form>
      </td>
    {{/if}}
  {{/if}}

  <td class="text">
    <span style="float: right;">
      {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
      {{assign var=sejour_id value=$sejour->_id}}
      {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
      <span id="atcd_allergies">
            {{mb_include module=soins template=inc_antecedents_allergies patient_guid=$patient->_guid}}
      </span>
      {{if $op->_is_urgence}}
        <img src="images/icons/attente_fourth_part.png" title="Intervention en urgence" />
      {{/if}}
    </span>
    <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$op->_id}}">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$op->_guid}}');">
      {{if $op->libelle}}
        <em>[{{$op->libelle}}]</em><br />
      {{/if}}
        {{foreach from=$op->_ext_codes_ccam item=_code}}
          <strong>{{$_code->code}}</strong> : {{$_code->libelleLong}}<br />
        {{/foreach}}
      </span>
    </a>
  </td>
  <td>{{tr}}COperation.cote.{{$op->cote}}{{/tr}}</td>
  <td class="text">
    <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$op->_id}}">
      {{$op->rques|nl2br}}
    </a>
  </td>
{{/if}}