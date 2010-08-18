<script type="text/javascript">

	// Parcours de toutes les checkbox, 3 cas possibles:
	// - checkbox disabled: on ne fait rien
	// - checkbox decoché: on supprime le code cdarr
	// - checkbox coché: on rajoute le code cdarr 
	submitCdarrs = function(){
	  var oForm = getForm("editCdarrs");
		$V(oForm.add_cdarrs, '');
		$V(oForm.remove_cdarrs, '');
    
		// Parcours des checkbox
    $('list-cdarrs').select('input[type="checkbox"]:not(.disabled)').each(function(checkbox){
		  var add_cdarrs = new TokenField(oForm.add_cdarrs); 
			var remove_cdarrs = new TokenField(oForm.remove_cdarrs); 
			checkbox.checked ? add_cdarrs.add(checkbox.value): remove_cdarrs.add(checkbox.value);
    });	
		return onSubmitFormAjax(oForm, { onComplete: function(){ 
		  refreshPlanningsSSR(); 
			modalWindow.close();
		}});
	}
	
	updateFieldCodeModal = function(selected, input) {
    var code_selected = selected.childElements()[0];
    $('other_cdarr_modal').insert({bottom: 
      DOM.span({}, 
        DOM.input({
          type: 'hidden', 
          id: 'editCdarrs__cdarrs['+code_selected.innerHTML+']', 
          name:'_cdarrs['+code_selected.innerHTML+']',
          value: code_selected.innerHTML
        }),
        DOM.button({
          className: "cancel notext", 
          type: "button",
          onclick: "deleteCode(this)"
        }),
        DOM.label({}, code_selected.innerHTML)
      )
   });
	}
			
	Main.add(function(){
    var url = new Url("ssr", "httpreq_do_activite_autocomplete");
    url.autoComplete("editCdarrs_code", "other_code_auto_complete", {
      dropdown: true,
      minChars: 2,
      select: "value",
      updateElement: updateFieldCodeModal
    } );
  });
	         
</script>

<form name="editCdarrs" action="?" method="post">
	<input type="hidden" name="m" value="ssr" />
	<input type="hidden" name="dosql" value="do_cdarrs_multi_aed" />
  <input type="hidden" name="token_evts" value="{{$token_evts}}" />
  <input type="hidden" name="add_cdarrs" />
	<input type="hidden" name="remove_cdarrs" />

{{assign var="count_events" value=$evenements|@count}}
<table class="tbl" style="width: 200px;">
  <tr>
    <th colspan="3" class="title">Evenements sélectionnés</th>
  </tr>
	<tr>
		<th>{{mb_label class="CEvenementSSR" field="debut"}}</th>
    <th>{{mb_label class="CEvenementSSR" field="duree"}}</th>
  </tr>	
	{{foreach from=$evenements item=_evenement}}
	<tr>
		<td>{{mb_value object=$_evenement field="debut"}}</td>
    <td>{{mb_value object=$_evenement field="duree"}} min</td>
	</tr>
	{{foreachelse}}
	<tr>
		<td colspan="2"><em>Aucun événement sélectionné</em></td>
	</tr>	
	{{/foreach}}
	<tr>
	  <th colspan="2">Actes CdARR</th>
	</tr>	
	<tr id="list-cdarrs">
		<td colspan="2" class="text">
			{{* 
			  strong: les actes presents sur tous les evenements (checked)
        opacity: les actes presents sur certains evenements
       *}}
			{{foreach from=$actes key=_code item=_acte}}
			   <span style="whitespace: nowrap; display: inline-block;">
			   	{{if array_key_exists($_code, $count_actes)}}
					  {{if $count_actes.$_code == $count_events}}
              <input name="cdarrs[{{$_code}}]" type="checkbox" checked="checked" value="{{$_code}}" /> 
					    <strong onmouseover="ObjectTooltip.createEx(this, '')">
								{{$_code}}
							</strong> 
	      	  {{else}}
						  <!-- Activation de la checkbox -->
						  <input name="cdarrs[{{$_code}}]" type="checkbox" checked="checked" class="disabled" value="{{$_code}}" onclick="this.removeClassName('disabled');"/>
							<span onmouseover="ObjectTooltip.createEx(this, '')">
							  {{$_code}}
							</span> 
						{{/if}}
					{{else}}
					  
            <span onmouseover="ObjectTooltip.createEx(this, 'CActiviteCdARR-{{$_code}}')">
	            <input name="cdarrs[{{$_code}}]" type="checkbox" value="{{$_code}}" /> {{$_code}} 
            </span> 
					{{/if}}
				 </span>
			{{foreachelse}}
			  <em>Aucun acte CdARR de disponible</em>
			{{/foreach}}
		</td>
	</tr>		
	<tr>
	  <td colspan="3" class="text">
      <input type="text" name="code" class="autocomplete" canNull=true size="2" />
      <div style="display:none;" class="autocomplete" id="other_code_auto_complete"></div>
		  <span id="other_cdarr_modal"></span>
	  </td>
	</tr>	
  <tr>
    <td colspan="3" class="button">
      <button type="button" class="cancel" onclick="modalWindow.close();">{{tr}}Close{{/tr}}</button>
			 <button type="button" class="submit" onclick="submitCdarrs();">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>