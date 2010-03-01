<script type="text/javascript">

submitPersonnel = function(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() {
    reloadPersonnel(document.forms["affectationPers-aideop"].object_id.value);
  } });
}

</script>
       
<table class="form">
<tr>
  <th class="title" colspan="3">
    Personnel en salle
  </th>
</tr>
<tr>
  <th class="category" style="width: 50%;">Personnel prévu</th>
  <th class="category" style="width: 50%;">Personnel ajouté<br />
	  <form name="affectationPers-aideop" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPpersonnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
    
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
    
      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.iade{{/tr}}</option>
        {{foreach from=$listPersIADE item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <form name="affectationPers-aideop" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPpersonnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
    
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
    
      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.op{{/tr}}</option>
        {{foreach from=$listPersAideOp item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <form name="affectationPers-penseuse" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPpersonnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
    
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
    
      <select name="personnel_id" onchange="submitPersonnel(this.form)" style="width: 10em;">
        <option value="">&mdash; {{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</option>
        {{foreach from=$listPersPanseuse item="pers"}}
        <option value="{{$pers->_id}}" class="mediuser" style="border-color: #{{$pers->_ref_user->_ref_function->color}};">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
  </th>
</tr>

{{assign var=submit value=submitPersonnel}}
{{assign var=width value=30}} <!-- largeur en % pris par les td inc_field_timing -->

<tr>
  <!-- Personnel prévu dans la plage op -->
  <td>
    {{foreach from=$tabPersonnel.plage item=affectation}}
    <form name="affectationPersonnel-{{$affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="dPpersonnel" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="affect_id" value="{{$affectation->_id}}" />
    <input type="hidden" name="personnel_id" value="{{$affectation->_ref_personnel->_id}}" />
    <input type="hidden" name="object_class" value="COperation" />
    <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
    <input type="hidden" name="realise" value="0" />
    
    {{assign var="affectation_id" value=$affectation->_id}}
    {{assign var="timing" value=$timingAffect.$affectation_id}}
    {{assign var="form" value="affectationPersonnel-$affectation_id"}}
		<table class="form">
      <tr>
      	<td style="width: 40%;" class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$affectation->_ref_personnel->_ref_user}}
				<br />
				<span style="opacity: 0.6">{{tr}}CPersonnel.emplacement.{{$affectation->_ref_personnel->emplacement}}{{/tr}}</span>
				</td>
			  {{include file=inc_field_timing.tpl object=$affectation field=_debut}}
			  {{include file=inc_field_timing.tpl object=$affectation field=_fin}}
			</tr>
    </table>
   </form>
   <hr style="margin-top: 0px;"/>
   {{foreachelse}}
     <div class="small-info">Aucun personnel prévu</div>
   {{/foreach}}
  </td>
  
  <!-- Personnel ajouté pour l'intervention -->
  <td>
    {{foreach from=$tabPersonnel.operation item=affectation}}
    <form name="affectationPersonnel-{{$affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="dPpersonnel" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="affect_id" value="{{$affectation->_id}}" />
    <input type="hidden" name="personnel_id" value="{{$affectation->_ref_personnel->_id}}" />
    <input type="hidden" name="object_class" value="COperation" />
    <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
    <input type="hidden" name="realise" value="0" />
    
    {{assign var="affectation_id" value=$affectation->_id}}
    {{assign var="timing" value=$timingAffect.$affectation_id}}
    {{assign var="form" value="affectationPersonnel-$affectation_id"}}

    <table class="form">
      <tr>
      	<td style="width: 40%;" class="text">
					{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$affectation->_ref_personnel->_ref_user}}
					<br />
					<span style="opacity: 0.6">
            {{tr}}CPersonnel.emplacement.{{$affectation->_ref_personnel->emplacement}}{{/tr}}
          </span>
      	</td>
		    {{include file=inc_field_timing.tpl object=$affectation field=_debut}}
		    {{include file=inc_field_timing.tpl object=$affectation field=_fin}}
				<td>
					{{if $can->edit || $modif_operation}}
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
	</td>
</tr>
</table>