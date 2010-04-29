{{assign var=evenement_ssr value=$object}}
{{assign var=evenement_ssr_id value=$evenement_ssr->_id}}
{{assign var=unique_id value=""|uniqid}}

<script type="text/javascript">
	
submitFormEditSSR = function(oForm){
  return onSubmitFormAjax(oForm, { onComplete: window.refreshPlanningsSSR || Prototype.emptyFunction} );
}

Main.add(function(){
  Calendar.regField(getForm("editEvenementSSR-{{$evenement_ssr_id}}-{{$unique_id}}").debut, null, { noView: true, inline: true, container: null });
});
        
</script>

{{include file=CMbObject_view.tpl}}

<form name="editEvenementSSR-{{$evenement_ssr_id}}-{{$unique_id}}" method="post" action="?" onsubmit="return submitFormEditSSR(this);">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_evenement_ssr_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="evenement_ssr_id" value="{{$evenement_ssr->_id}}" />
	<table class="tooltip">
		<tr>
			<th>
				{{mb_label object=$evenement_ssr field="debut"}}
      </th>
			<td>
			  {{mb_field object=$evenement_ssr field="debut"}}
			</td>
		</tr>
		<tr>
			<th>
				{{mb_label object=$evenement_ssr field="duree"}}
			</th>
			<td>
        {{mb_field object=$evenement_ssr field="duree" form="editEvenementSSR-$evenement_ssr_id-$unique_id" increment=1 size=2 step=10}}
      </td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">  
				<button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
			  <button type="submit" class="trash" onclick="$V(this.form.del, '1');">{{tr}}Delete{{/tr}}</button>
			</td>
		</tr>
	</table>
</form>