<script type="text/javascript">
  afterAdministration = function(administration_id) {
    var oForm = getForm('editTrans');
    $V(oForm.object_id, administration_id);
    $V(oForm.object_class, "CAdministration");
    return onSubmitFormAjax(oForm, {onComplete: function() {
     afterEditLine();
    } });
  }
  afterEditLine = function() {
    var reloadAfter = function() {
      window.opener.refreshDossierSoin(null, 'inscription', true);
      if (window.opener.updateNbTrans) {
        window.opener.updateNbTrans($V(getForm('editTrans').sejour_id));
      }
      window.close();
    }
    var oForm = getForm("editLine");
    if (oForm) {
      return onSubmitFormAjax(oForm, {onComplete: reloadAfter});
    }
    else {
      reloadAfter();
    }
  }
</script>
<table class="form">
	<tr>
    <th class="category" colspan="2">
      Administration - {{$line->_view}}
    </th>
  </tr>
	<tr>
		<td style="width: 50%">
			<form name="addAdministration" action="?" method="post">
				<input type="hidden" name="dosql" value="do_administration_aed" />
			  <input type="hidden" name="m" value="dPprescription" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="administration_id" value="" />
			  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
			  <input type="hidden" name="object_id" value="{{$line->_id}}" />
			  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
				<input type="hidden" name="dateTime" value="{{$datetime}}" />
	      <input type="hidden" name="unite_prise" value="aucune_prise" />
				<input type="hidden" name="callback" value="afterAdministration" />
				{{mb_field object=$administration field="quantite" min=0 increment=1 form=addAdministration}}
        {{if $line instanceof CPrescriptionLineMedicament}}
          {{$line->_ref_produit->libelle_unite_presentation}}
        {{else}}
          {{$line->_unite_prise}}
        {{/if}} 
			</form>
		</td>
    <td style="width: 50%">
      {{if $line instanceof CPrescriptionLineMedicament ||
           $line instanceof CPrescriptionLineMix}}
        <form name="editLine" action="?" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          {{if $line instanceof CPrescriptionLineMedicament}}
            <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
          {{else}}
            <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
          {{/if}}
          {{mb_key object=$line}}
          {{mb_label object=$line field=voie}} :
          <select name="voie">
            {{foreach from=$line->_ref_produit->voies item=libelle_voie}}
              <option value="{{$libelle_voie}}" {{if $line->voie == $libelle_voie}}checked="checked"{{/if}}>{{$libelle_voie}}</option>
            {{/foreach}}
            <option value="none">Voie non définie</option>
          </select>
        </form>
      {{/if}}
    </td>
	</tr>
  <tr>
    <td colspan="2">
      {{assign var=hide_cible value=1}}
      {{assign var=hide_button_add value=1}}
      {{mb_include module=dPhospi template=inc_transmission refreshTrans=0}}
      <button type="button" class="submit" 
        onclick="return onSubmitFormAjax(getForm('addAdministration'));">
        {{tr}}Save{{/tr}}
      </button>
    </td>
  </tr>
</table>