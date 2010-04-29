<script type="text/javascript">
	
submitFormEditSSR = function(oForm){
  $V(oForm.del, '1');
	 return onSubmitFormAjax(oForm, { onComplete: window.refreshPlanningsSSR || Prototype.emptyFunction} );
}

</script>

{{include file=CMbObject_view.tpl}}

{{assign var=evenement_ssr value=$object}}

<table class="tbl tooltip">
	<tr>
		<td class="button">
			<form name="editEvenementSSR-{{$evenement_ssr->_id}}" method="post" action="?" onsubmit="return submitFormEditSSR(this);">
			  <input type="hidden" name="m" value="ssr" />
				<input type="hidden" name="dosql" value="do_evenement_ssr_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="evenement_ssr_id" value="{{$evenement_ssr->_id}}" />
        
			  <button type="submit" class="trash">{{tr}}Delete{{/tr}}</button>
    	</form>
		</td>
	</tr>
</table>
