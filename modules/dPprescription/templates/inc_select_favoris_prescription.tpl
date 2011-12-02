{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add( function(){
  var oForm = getForm("addFavorisPresc");

  // Modification du praticien_id si celui-ci est spécifié
  if(window.document.forms.selPraticienLine){
    var oFormPraticien = window.document.forms.selPraticienLine;
		$V(oForm.praticien_id, $V(oFormPraticien.praticien_id));
  }
	
  var oFormDate = document.forms.selDateLine;
  if(oFormDate){
    if(oFormDate.debut && oFormDate.debut.value){
      $V(oForm.debut, oFormDate.debut.value);  
    }
    if(oFormDate.time_debut && oFormDate.time_debut.value){
      $V(oForm.time_debut, oFormDate.time_debut.value);
    }
    if(oFormDate.jour_decalage && oFormDate.jour_decalage.value){
      $V(oForm.jour_decalage, oFormDate.jour_decalage.value);
    }
    if(oFormDate.decalage_line && oFormDate.decalage_line.value){
      $V(oForm.decalage_line, oFormDate.decalage_line.value);
    }
    if(oFormDate.unite_decalage && oFormDate.unite_decalage.value){
      $V(oForm.unite_decalage, oFormDate.unite_decalage.value);
    }
    if(oFormDate.operation_id && oFormDate.operation_id.value){
      $V(oForm.operation_id, oFormDate.operation_id.value);
    }
  }
});

</script>

<form name="addFavorisPresc" method="post" action="?">
	<input type="hidden" name="m" value="dPprescription" />
	<input type="hidden" name="dosql" value="do_add_multiple_lines_aed" />
  <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	<input type="hidden" name="debut" value="" />
	<input type="hidden" name="time_debut" value="" />
  <input type="hidden" name="jour_decalage" value="" />
  <input type="hidden" name="decalage_line" value="" />
  <input type="hidden" name="unite_decalage" value="" />
	<input type="hidden" name="operation_id" value="" />
  <input type="hidden" name="mode_protocole" value="{{$mode_protocole}}" />
  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
  
	<table class="main">
		{{if $user->_id}}
			<tr>
				<th class="title" colspan="2">Favoris de {{$user->_view}}</th>
			</tr>
		{{/if}}
		<tr>
		{{foreach from=$favoris key=chap item=_favoris_by_cat}}
		  <td style="width: 50%;">
			  <table class="tbl">
					<tr>
				    <th colspan="3">{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}</th>
					</tr>	
				  {{foreach from=$_favoris_by_cat item=_favoris}}
						<tr>
							<td class="narrow">
								{{if $_favoris instanceof CBcbProduit}}
							    <input type="checkbox" name="codes_cip[{{$_favoris->code_cip}}]" value="{{$_favoris->code_cip}}" />
								{{else}}
								  <input type="checkbox" name="elements_id[{{$_favoris->element_prescription_id}}]" value="{{$_favoris->element_prescription_id}}" />
				        {{/if}}
							</td>	
					    <td>
					    	{{if $_favoris instanceof CBcbProduit}}
					        <strong>{{$_favoris->libelle_abrege}} {{$_favoris->dosage}}</strong>
									<br />
									<span class="opacity-60">{{$_favoris->forme}}</span>
								{{else}}
								  {{$_favoris->libelle}}
								{{/if}}
					    </td>
							<td class="narrow compact" style="text-align: right;">
								{{$_favoris->_count}}
							</td>
					  </tr>
					  {{foreachelse}}
					  <tr>
					    <td class="empty">
					      {{tr}}None{{/tr}}
					    </td>
					  </tr>
					{{/foreach}}
				</table>
			 </td>
		{{/foreach}}
		<tr>
			<td colspan="2" class="button">
				<button type="button" class="submit" onclick="return onSubmitFormAjax(this.form, { onComplete: Control.Modal.close } );">Prescrire les éléments sélectionnés</button>
	      <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
			</td>
		</tr>
	</table>
</form>