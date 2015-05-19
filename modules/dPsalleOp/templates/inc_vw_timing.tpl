{{if @$modules.brancardage->_can->read && $selOp->_ref_brancardage->_id}}
<form name="changeItemBrancard" method="post" action="">
  <input type="hidden" name="brancardage_item_id" value="" />
  <input type="hidden" name="@class" value="CBrancardageItem" />
  <input type="hidden" name="brancardage_id" value="{{$selOp->_ref_brancardage->_id}}" />
  <input type="hidden" name="demande_brancard" value="now" />
</form>
{{/if}}

<form name="timing{{$selOp->operation_id}}" action="?" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
  <input type="hidden" name="del" value="0" />

  <table class="form" style="table-layout: fixed;">
    <tr>
      <th class="title" colspan="{{if $conf.dPsalleOp.COperation.use_remise_chir}}6{{else}}4{{/if}}">
        Horodatage
      </th>
    </tr>

    {{assign var=submit value=submitTiming}}
    {{assign var=opid value=$selOp->operation_id}}
    {{assign var=form value=timing$opid}}

    <tr>
      {{if @$modules.brancardage->_can->read}}
        {{assign var=bloc_brancard value=$selOp->_ref_salle->bloc_id}}
        {{mb_script module=brancardage script=creation_brancardage ajax=true}}
        <td id="demande_brancard-{{$selOp->sejour_id}}" rowspan="2">
          {{mb_include module=brancardage template=inc_exist_brancard _operation=$selOp colonne="demande_brancard"
            destination="CBlocOperatoire" destination_guid="CBlocOperatoire-$bloc_brancard" date_brancard=$selOp->_datetime|date_format:"%Y-%m-%d"}}
        </td>
      {{/if}}

      {{if $conf.dPsalleOp.COperation.use_entree_bloc}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=entree_bloc}}
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_entree_sortie_salle}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=entree_salle}}
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_remise_chir}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=remise_chir}}
      {{/if}}
      {{if "CAppUI::conf"|static_call:"dPsalleOp COperation use_tto":"CGroups-$g"}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=tto}}
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_garrot}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=pose_garrot}}
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_debut_fin_op}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=debut_op use_disabled=$selOp->entree_salle|default:'yes'}}
      {{/if}}
    </tr>
    <tr>
      {{if $conf.dPsalleOp.COperation.use_entree_bloc}}
        <td></td>
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_entree_sortie_salle}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=sortie_salle use_disabled=$selOp->fin_op|default:'yes'}}
      {{/if}}
      {{if "CAppUI::conf"|static_call:"dPsalleOp COperation use_tto":"CGroups-$g"}}
        <td></td>
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_suture}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=suture_fin}}
      {{else}}
        {{if $conf.dPsalleOp.COperation.use_remise_chir}}
          <td></td>
        {{/if}}
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_garrot}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=retrait_garrot}}
      {{/if}}
      {{if $conf.dPsalleOp.COperation.use_debut_fin_op}}
        {{mb_include module="salleOp" template="inc_field_timing" object=$selOp field=fin_op use_disabled=$selOp->debut_op|default:'yes'}}
      {{/if}}
    </tr>
  </table>
</form>