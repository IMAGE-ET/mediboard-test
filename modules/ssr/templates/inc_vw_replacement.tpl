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

<table class="tbl">
  <tr>
    <th class="title text" colspan="3">
    	Rééducateurs de la fonction 
			{{mb_include module=mediusers template=inc_vw_function function=$user->_ref_function}}
		</th>
  </tr> 
  <tr>
    <th>{{mb_title class=CEvenementSSR field=sejour_id    }}</th>
    <th>{{mb_title class=CEvenementSSR field=therapeute_id}}</th>
    <th>Evts SSR</th>
  </tr>
  
	{{foreach from=$evenements_counts key=sejour_id item=_counts_by_sejour}}
	<tbody class="hoverable">
		
  {{foreach from=$_counts_by_sejour key=therapeute_id item=_count name=therapeutes}}
  <tr>
  	{{if $smarty.foreach.therapeutes.first}} 
    {{assign var=_sejour value=$sejours.$sejour_id}}
    <td rowspan="{{$_counts_by_sejour|@count}}">
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
    		{{$_sejour}}
			</span>
		</td>
  	{{/if}}
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$users.$therapeute_id}}</td>
    <td style="text-align: center;">{{$_count}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3"><em>{{tr}}None{{/tr}}</em></td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  </tbody>
</table>

<form name="editReplacement" action="?" method="post" 
      onsubmit="return onSubmitFormAjax(this, { onComplete: 
			  function(){ 
				  refreshReplacement('{{$sejour->_id}}','{{$conge->_id}}','{{$type}}');
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

<table class="form">
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
			<th>{{mb_label object=$replacement field=replacer_id}}</th>
	    <td>
	    	{{if $replacement->_id}}
				  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$replacement->_ref_replacer}}
				{{else}}
		      <select name="replacer_id" onchange="refreshReplacerPlanning(this.value);">
		        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$users disabled=$conge->user_id}}
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
