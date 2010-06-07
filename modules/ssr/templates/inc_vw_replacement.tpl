{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function(){
  var oForm = getForm("editReplacement");
	if($V(oForm.replacer_id)){
    refreshReplacerPlanning($V(oForm.replacer_id));
	}
});

	
</script>
<form name="editReplacement" action="?" method="post" 
      onsubmit="return onSubmitFormAjax(this, { onComplete: 
			  function(){ 
				  refreshReplacement('{{$sejour->_id}}','{{$conge_id}}','{{$type}}');
					refreshlistSejour('{{$sejour->_id}}','{{$type}}');
					{{if $type == "reeducateur"}}
					$('replacement-reeducateur').update(''); 
					{{/if}}
				} });">
      	
	<input type="hidden" name="m" value="ssr" />
	{{if $type == "kine"}}
	  <input type="hidden" name="dosql" value="do_replacement_aed" />
    {{mb_key object=$replacement}}
	{{else}}
	  <input type="hidden" name="dosql" value="do_transfert_ssr_multi_aed" />
  {{/if}}
	<input type="hidden" name="del" value="0" />
	
    {{mb_field object=$replacement field=sejour_id hidden=1}}
    {{mb_field object=$replacement field=conge_id hidden=1}}

	<table class="tbl">
		<tr>
			<th class="title" colspan="2">Anciens remplacants pour {{$sejour->_ref_patient->_view}}</th>
		</tr>	
		<tr>
	    <th>
	      {{mb_title class=CReplacement field=replacer_id}}
	    </th>
	    <th>
	      {{mb_title class=CReplacement field=sejour_id}}
	   </th>
	  </tr>
		{{foreach from=$replacements item=_replacement}}
		<tr>
		  <td>
			  {{$_replacement->_ref_replacer->_view}}
		  </td>
			<td>
				{{$_replacement->_ref_sejour->_view}}
			</td>
		</tr>
		{{foreachelse}}
		<tr>
		  <td colspan="2"><em>{{tr}}None{{/tr}}</em></td>
		</tr>
		{{/foreach}}
		<tr>
			{{if $type == "kine"}}
				{{if $replacement->_id}}
				 <th class="title modify" colspan="2">Modification du remplacement du séjour<br /> {{$sejour->_view}}</th>
	      {{else}}
				 <th class="title" colspan="2">Création d'un remplacement</th>
	    	{{/if}}
			{{else}}
			  <th class="title" colspan="2">
			    Transfert des évenements SSR
				</th>
			{{/if}}
		</tr>
		
		{{if $type == "kine"}}
		<tr>
	    <td colspan="2" class="button">
	    	{{if $replacement->_id}}
				  {{mb_value object=$replacement field=replacer_id}}
				{{else}}
		      <select name="replacer_id" onchange="refreshReplacerPlanning(this.value);">
		        <option value="">&mdash; Utilisateur</option>
		        {{foreach from=$users item=_user}}
		          <option value="{{$_user->_id}}" class="mediuser" style="border-color: #{{$_user->_ref_function->color}};" 
							{{if $replacement->replacer_id == $_user->_id}}selected="selected"{{/if}}>{{$_user->_view}}</option>
		        {{/foreach}}
		      </select>
				{{/if}}
	    </td>			
		</tr>
    <tr>
      <td colspan="2" class="button">
      	{{if $replacement->_id}}
					<button class="trash" type="button" onclick="confirmDeletion(this.form, {
	          typeName:'le remplacement ',
	          objName:'{{$replacement->_view|smarty:nodefaults|JSAttribute}}',
	          ajax: 1})">
	          {{tr}}Delete{{/tr}}
	        </button>
				{{else}}
				  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        {{/if}}
      </td>	
    </tr>
		{{/if}}
		
		{{if $type == "reeducateur"}}
		<tr>
      <td colspan="2" class="button">
        <select name="replacer_id" onchange="refreshReplacerPlanning(this.value);">
          <option value="">&mdash; Utilisateur</option>
          {{foreach from=$users item=_user}}
            <option value="{{$_user->_id}}" class="mediuser" style="border-color: #{{$_user->_ref_function->color}};">{{$_user->_view}}</option>
          {{/foreach}}
        </select>
      </td>     
    </tr>
		<tr>
			<td colspan="2" class="button">
				 <button type="submit" class="submit">Transférer les évenements</button>
			</td>
		</tr>
		{{/if}}
		
		
	</table>
</form>
<div id="replacer-planning">
	
</div>
