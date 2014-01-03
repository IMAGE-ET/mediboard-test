<script>
  submitPersonnel = function(oForm){
    return onSubmitFormAjax(oForm, { onComplete : function() {
      reloadPersonnel(oForm.object_id.value);
    } });
}
</script>
       
<table class="form">
<tr>
  <th class="title" {{if $in_salle}}colspan="2"{{/if}}>
    Personnel en salle
  </th>
</tr>
<tr>
  {{if $in_salle}}
    <th class="category" style="width: 50%;">Personnel prévu</th>
  {{/if}}
  <th class="category" style="width: 50%;">Personnel ajouté<br />
    <form name="affectationPers-iade" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="personnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />

      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.iade{{/tr}}</option>
        {{foreach from=$listPers.iade item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <form name="affectationPers-aideop" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="personnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />

      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.op{{/tr}}</option>
        {{foreach from=$listPers.op item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <form name="affectationPers-panseuse" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="personnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</option>
        {{foreach from=$listPers.op_panseuse item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <form name="affectationPers-sagefemme" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="personnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.sagefemme{{/tr}}</option>
        {{foreach from=$listPers.sagefemme item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <form name="affectationPers-manipulateur" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="personnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.manipulateur{{/tr}}</option>
        {{foreach from=$listPers.manipulateur item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
  </th>
</tr>

{{assign var=submit value=submitPersonnel}}
{{assign var=width value=30}} <!-- largeur en % pris par les td inc_field_timing -->

<tr>
  {{if $in_salle}}
    <!-- Personnel prévu dans la plage op -->
    <td>
      {{foreach from=$tabPersonnel.plage item=affectation}}
        {{assign var="affectation_id" value=$affectation->_id}}
        {{assign var=personnel_id value=$affectation->_ref_personnel->_id}}
        {{assign var="form" value="affectationPersonnel-$personnel_id"}}

        <form name="{{$form}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="personnel" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$affectation}}
        {{mb_field object=$affectation field=personnel_id hidden=1}}
        {{mb_field object=$affectation field=object_class hidden=1}}
        {{mb_field object=$affectation field=object_id hidden=1}}
        {{mb_field object=$affectation field=parent_affectation_id hidden=1}}
        {{mb_field object=$affectation field=realise value="0" hidden=1}}

        <table class="form">
          <tr>
            <td {{if $in_salle}}style="width: 40%;"{{/if}}class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$affectation->_ref_personnel->_ref_user}}
            <br />
            <span class="opacity-60">{{tr}}CPersonnel.emplacement.{{$affectation->_ref_personnel->emplacement}}{{/tr}}</span>
            </td>
            {{mb_include module=dPsalleOp template=inc_field_timing object=$affectation field=_debut}}
            {{mb_include module=dPsalleOp template=inc_field_timing object=$affectation field=_fin disabled=$affectation->_debut|default:'yes'}}
          </tr>
        </table>
       </form>
       <hr style="margin-top: 0px;"/>
     {{foreachelse}}
       <div class="small-info">Aucun personnel prévu</div>
     {{/foreach}}
    </td>
  {{/if}}
  <!-- Personnel ajouté pour l'intervention -->
  <td>
    {{foreach from=$tabPersonnel.operation item=affectation}}
    <form name="affectationPersonnel-{{$affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="personnel" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$affectation}}
    <input type="hidden" name="personnel_id" value="{{$affectation->_ref_personnel->_id}}" />
    <input type="hidden" name="object_class" value="COperation" />
    <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
    <input type="hidden" name="realise" value="0" />

    {{assign var="affectation_id" value=$affectation->_id}}
    {{assign var="form" value="affectationPersonnel-$affectation_id"}}

    <table class="form">
      <tr>
        <td {{if $in_salle}}style="width: 40%;"{{/if}} class="text">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$affectation->_ref_personnel->_ref_user}}
          <br />
          <span class="opacity-60">
            {{tr}}CPersonnel.emplacement.{{$affectation->_ref_personnel->emplacement}}{{/tr}}
          </span>
        </td>
        {{if $in_salle}}
          {{mb_include module=dPsalleOp template=inc_field_timing object=$affectation field=_debut}}
          {{mb_include module=dPsalleOp template=inc_field_timing object=$affectation field=_fin disabled=$affectation->_debut|default:'yes'}}
        {{/if}}
        <td {{if !$in_salle}}class="narrow"{{/if}}>
          {{if $modif_operation}}
            <button type="button" class="cancel notext" onclick="$V(this.form.del, '1'); submitPersonnel(this.form)">{{tr}}Cancel{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
   </form>
   <hr style="margin-top: 0px;"/>
   {{foreachelse}}
     <div class="small-info">Aucun personnel ajouté</div>
   {{/foreach}}

    <form name="rques_personnel" action="?m={{$m}}" method="post" onchange="return onSubmitFormAjax(this);">
      {{mb_key   object=$selOp}}
      {{mb_class object=$selOp}}
      {{mb_label object=$selOp field=rques_personnel}}
      {{mb_field object=$selOp field=rques_personnel onchange="return onSubmitFormAjax(this.form);"}}
    </form>
  </td>
</tr>
</table>