<script type="text/javascript">

signeActes = function(oForm, subject_id, praticien_id) {
	{{if $dialog}}
    return onSubmitFormAjax(oForm, {onComplete: function() { reloadAll(subject_id, praticien_id); } } );
  {{else}}
    oForm.submit();
  {{/if}}
}

{{if $dialog}}
  reloadAll = function(subject_id, praticien_id) {
    if (window.parent) {
      window.parent.ActesCCAM.refreshList(subject_id, praticien_id);
    }
    $("signature_actes_{{$object->_guid}}").up('div').up().select('.change')[0].click();
  }
{{/if}}
</script>

{{assign var=can_view_tarif value=true}}
{{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif}}
  {{if !$app->_ref_user->isPraticien() && !$app->_ref_user->isSecretaire()}}
    {{assign var=can_view_tarif value=false}}
  {{/if}}
{{/if}}

<table class="main" {{if $dialog}}id="signature_actes_{{$object->_guid}}"{{/if}}>
  {{if !$dialog}}
  <tr>
    <th>
      <script type="text/javascript">
        Main.add(function () {
          Calendar.regField(getForm("typeAffichage").date, null, {noView: true});
        });
      </script>
      <form name="typeAffichage" action="?" method="get">
        <input type="hidden" name="m" value="dPsalleOp" />
        <input type="hidden" name="tab" value="vw_signature_actes" />
        <div style="float: right">
          Filtres
    		  <select name="praticien_id" onchange="submit();">
      		  <option value="">&mdash; Tous les praticiens</option>
      		  {{foreach from=$listPraticien item=praticien}}
      		    <option value="{{$praticien->_id}}" {{if $praticien_id == $praticien->_id}}selected="selected"{{/if}}>{{$praticien->_view}}</option>
      		  {{/foreach}}
    		  </select>
        </div>
        
        {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
		  </form>
    </th>
  </tr>
  {{/if}}
  <tr>
    <td>
			<table class="tbl">
			
				  <!-- Parcours des operations -->
				  {{foreach from=$tabOperations item=operation key=operation_id}}
				    {{assign var=_operation value=$operations.$operation_id}}
				    <tr>
				      <th colspan="10">{{$_operation->_view}}</th>
				    </tr>
				    {{foreach from=$operation item=praticien key=praticien_id}}
				      {{assign var=_praticien value=$praticiens.$praticien_id}}
				      
				      {{foreach from=$praticien item=curr_acte name="acte"}}
				      
                {{assign var=can_view_dh value=true}}
                {{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif && $curr_acte->_id && ($curr_acte->_ref_executant->function_id != $app->_ref_user->function_id)}}
                  {{assign var=can_view_dh value=false}}
                {{/if}}
                
								{{if $smarty.foreach.acte.first}}
								<tr>
								  <th>{{mb_title object=$curr_acte field=code_acte}}</th>
								  <th>{{mb_title object=$curr_acte field=code_activite}}</th>
								  <th>{{mb_title object=$curr_acte field=code_phase}}</th>
								  <th>{{mb_title object=$curr_acte field=modificateurs}}</th>
								  <th>{{mb_title object=$curr_acte field=code_association}}</th>
								  <th>{{mb_title object=$curr_acte field=rembourse}}</th>
								  <th>{{mb_title object=$curr_acte field=montant_base}}</th>
								  <th>{{mb_title object=$curr_acte field=montant_depassement}}</th>
								  <th>{{mb_title object=$curr_acte field=_montant_facture}}</th>
								  <th>{{mb_title object=$curr_acte field=signe}}</th>
								</tr>
								{{/if}}
								<tr>
								  <td>{{mb_value object=$curr_acte field=code_acte}}</td>
								  <td>{{mb_value object=$curr_acte field=code_activite}}</td>
								  <td>{{mb_value object=$curr_acte field=code_phase}}</td>
								  <td>{{mb_value object=$curr_acte field=modificateurs}}</td>
								  <td>{{mb_value object=$curr_acte field=code_association}}</td>								  
								  <td>
								    {{if $conf.dPsalleOp.CActeCCAM.tarif}}
                      {{mb_value object=$curr_acte field=rembourse}}
                    {{/if}}
                  </td>
								  <td>
								    {{if $can_view_tarif && $conf.dPsalleOp.CActeCCAM.tarif}}
                      {{mb_value object=$curr_acte field=montant_base}}
                    {{/if}}  
                  </td>
								  <td>
								    {{if $can_view_dh && $conf.dPsalleOp.CActeCCAM.tarif}}
                      {{mb_value object=$curr_acte field=montant_depassement}}
                    {{/if}}
                  </td>
								  <td>
								    {{if $can_view_tarif && $can_view_dh && $conf.dPsalleOp.CActeCCAM.tarif}}
                      {{mb_value object=$curr_acte field=_montant_facture}}
                    {{/if}}
                  </td>
								  <td style="text-align: center">
			            {{if $curr_acte->signe}}
			              <img src="images/icons/tick.png" alt="" />
			            {{else}}
			              <img src="images/icons/cross.png" alt="" />
			            {{/if}}
			            </td>
							  </tr>  
				      {{/foreach}}
				      <!-- Formulaire de signature des actes ccam -->
				      {{if @$nonSigne.$operation_id.$praticien_id}}
				      <tr>
				        <td {{if $conf.dPsalleOp.CActeCCAM.tarif}}colspan="8"{{else}}colspan="4"{{/if}}>
				        </td>
				        <td colspan="2" style="text-align: right">
				        {{$_praticien->_view}}
				          <form name="signature_acte-{{$_praticien->_id}}-{{$_operation->_id}}" action="?" method="post" onsubmit="return signeActes(this, '{{$_operation->_id}}', '{{$_praticien->_id}}');">
							      <input type="hidden" name="dosql" value="do_signature_actes_aed" />
							      <input type="hidden" name="m" value="dPsalleOp" />
							      <input type="hidden" name="del" value="0" />
							      <input type="hidden" name="dialog" value="{{$dialog}}" />
							      <input type="hidden" name="date" value="{{$date}}" />
							      <input type="hidden" name="object_id" value="{{$_operation->_id}}" />
							      <input type="hidden" name="object_class" value="{{$_operation->_class}}" />
							      <input type="hidden" name="praticien_id" value="{{$_praticien->_id}}" />
							      <input type="password"  class="notNull str" size="10" maxlength="32" name="password" />
						        <button type="button" class="submit" onclick="this.form.onsubmit();">Signer</button>
							    </form>
				        </td>
				      </tr>
				    {{/if}}
				  {{/foreach}}
		    {{/foreach}}
		</table>
    </td>
  </tr>
</table>