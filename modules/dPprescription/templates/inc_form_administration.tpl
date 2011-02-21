<table class="form">
	<tr>
    <th class="category">
      Administration - {{$line->_view}}
    </th>
  </tr>
	<tr>
		<td>
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
				
				{{mb_field object=$administration field="quantite" min=0 increment=1 form=addAdministration}}
        {{if $line instanceof CPrescriptionLineMedicament}}
          {{$line->_ref_produit->libelle_unite_presentation}}
        {{else}}
          {{$line->_unite_prise}}
        {{/if}} 
        <button type="button" class="submit" 
				        onclick="return onSubmitFormAjax(this.form, { onComplete: function(){ window.opener.refreshDossierSoin(null, 'inscription', true); window.close(); }  } );">
          {{tr}}Save{{/tr}}
        </button>
			</form>
		</td>
	</tr>
</table>