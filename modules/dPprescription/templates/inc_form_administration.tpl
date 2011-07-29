{{assign var="qte_obligatoire_inscription" value=$conf.dPprescription.CPrescription.qte_obligatoire_inscription}}

<script type="text/javascript">
  submitAdministration = function() {
    var oForm = getForm('addAdministration');
    var quantite = $V(oForm.quantite);
    // On crée l'administration seulement si la quantité est remplie
    
    {{if $qte_obligatoire_inscription}}
      if (!quantite || quantite == 0) {
        alert("{{tr}}CPrescription.qte_obligatoire_inscription{{/tr}}");
        return;
      }
    {{/if}}
    if (quantite && quantite > 0) {
      return onSubmitFormAjax(oForm);
    }
    else {
      afterAdministration();
    }
  }
  afterAdministration = function(administration_id) {
    var oForm = getForm('editTrans');
    if (administration_id) {
      $V(oForm.object_id, administration_id);
      $V(oForm.object_class, "CAdministration");
    }
    // Si pas d'administration, la ou les transmissions sont associées à la ligne
    else {
      $V(oForm.object_id, '{{$line->_id}}');
      $V(oForm.object_class, '{{$line->_class}}');
    }
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
      $V(oForm.commentaire, $V(getForm("editCommentaire").commentaire));
      return onSubmitFormAjax(oForm, {onComplete: reloadAfter});
    }
    else {
      reloadAfter();
    }
  }
  Main.add(function() {
    setTimeout("getForm('addAdministration').quantite.focus()", 1);
    var oForm = getForm("editCommentaire");
    new AideSaisie.AutoComplete(oForm.commentaire, {
      objectClass: "{{$line->_class}}", 
      contextUserId: "{{$user_id}}",
      resetSearchField: false,
      validateOnBlur: false,
      strict: false,
      {{if $line instanceof CPrescriptionLineMedicament}}
        dependField1: oForm.code_ucd
      {{else}}
        dependField1: oForm.element_prescription_id
      {{/if}}
    });
  });
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
			  <input type="hidden" name="object_class" value="{{$line->_class}}" />
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
      <form name="editLine" action="?" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        {{if $line instanceof CPrescriptionLineMedicament}}
          <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
        {{else}}
          <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        {{/if}}
        {{mb_key object=$line}}
        <input type="hidden" name="commentaire" value="" />
        {{if $line instanceof CPrescriptionLineMedicament}}
          {{mb_label object=$line field=voie}} :
          <select name="voie">
            {{foreach from=$line->_ref_produit->voies item=libelle_voie}}
              <option value="{{$libelle_voie}}" {{if $line->voie == $libelle_voie}}checked="checked"{{/if}}>{{$libelle_voie}}</option>
            {{/foreach}}
            <option value="none">Voie non définie</option>
          </select>
        {{/if}}
      </form>
    </td>
	</tr>
  <tr>
    <td colspan="2">
      <form name="editCommentaire" method="post" action="?">
        <fieldset>
          <legend>
            {{mb_label object=$line field=commentaire}}
          </legend>
          {{mb_field object=$line field=commentaire}}
        </fieldset>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{assign var=hide_cible value=1}}
      {{assign var=hide_button_add value=1}}
      <fieldset>
        <legend>{{tr}}CTransmissionMedicale{{/tr}}</legend>
      {{mb_include module=dPhospi template=inc_transmission refreshTrans=0}}
      </fieldset>
      
      <button type="button" class="submit" 
        onclick="submitAdministration();">
        {{tr}}Save{{/tr}}
      </button>
      
      <form name="delInscription" method="post" action="?">
        <input type="hidden" name="m" value="dPprescription" />
        {{if $line instanceof CPrescriptionLineMedicament}}
          <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
        {{else}}
          <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        {{/if}}
        <input type="hidden" name="del" value="1" />
        {{mb_key object=$line}}
        <button type="button" class="trash"
          onclick="confirmDeletion(this.form, {
            typeName:'l\'inscription',
            objName:'{{$line->_view}}',
            ajax: 1},
            {onComplete: function(){ $('administration').update();}})">Supprimer l'inscription</button>
      </form>
    </td>
  </tr>
</table>