<script type="text/javascript">
	changeCdarr = function(checked, code){
	  var oFormEditCdarr = getForm("editCdarrs");
		$V(oFormEditCdarr.checked, checked);
		$V(oFormEditCdarr.code, code);
    onSubmitFormAjax(oFormEditCdarr, { onComplete: updateModalCdarr } );
	}	
</script>

<form name="editCdarrs" action="?" method="post">
	<input type="hidden" name="m" value="ssr" />
	<input type="hidden" name="dosql" value="do_cdarrs_multi_aed" />
  <input type="hidden" name="checked" value="" />
  <input type="hidden" name="code" value="" />
  <input type="hidden" name="token_evts" value="{{$token_evts}}" />
</form>

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
	<tr>
		<td colspan="2" class="text">
			{{* 
			  strong: les actes presents sur tous les evenements (checked)
        opacity: les actes presents sur certains evenements
       *}}
			{{foreach from=$actes key=_code item=_acte}}
			   <span style="whitespace: nowrap; display: inline-block;">
			   	{{if array_key_exists($_code, $count_actes)}}
					  {{if $count_actes.$_code == $count_events}}
              <input type="checkbox" checked="checked" onchange="changeCdarr(false, '{{$_code}}');" /> 
					    <strong onmouseover="ObjectTooltip.createEx(this, '')">
								{{$_code}}
							</strong> 
	      	  {{else}}
              <input type="checkbox" checked="checked" onchange="changeCdarr(true, '{{$_code}}');" />
						  <span onmouseover="ObjectTooltip.createEx(this, '')" style="opacity: 0.7">
						  	 {{$_code}}
							</span> 
	          {{/if}}
					{{else}}
            <span onmouseover="ObjectTooltip.createEx(this, 'CActiviteCdARR-{{$_code}}')">
	            <input type="checkbox" onchange="changeCdarr(true, '{{$_code}}');" /> {{$_code}} 
            </span> 
					{{/if}}
				 </span>
			{{foreachelse}}
			  <em>Aucun acte CdARR de disponible</em>
			{{/foreach}}
		</td>
	</tr>	
  <tr>
    <td colspan="3" class="button">
      <button type="button" class="cancel" onclick="modalWindow.close(); refreshPlanningsSSR();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
</table>