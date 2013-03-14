<script type="text/javascript">

function changePage(page){
  oForm = getForm("filter-activite");
  $V(oForm.current, page);
  oForm.submit();
  
}

</script>

<form action="?" name="filter-activite" method="get" >

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />
<input type="hidden" name="current" value="{{$current}}" />

<table class="form">
  <tr>
    <th>{{tr}}Keywords{{/tr}}</th>
    <td><input name="code" type="text" value="{{$activite->code}}" /></td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>

</form>

{{mb_include module=system template=inc_pagination change_page=changePage}}

<table class="tbl">
  <tr>
    <th>{{mb_title object=$activite field=hierarchie}}</th>
    <th>{{mb_title object=$activite field=code}}</th>
    <th>{{mb_title object=$activite field=libelle}}</th>
		<th class="narrow">Nb. éléments</th>
  </tr>
  {{foreach from=$listActivites item=_activite}}
  <tr>
    <td>{{$_activite->hierarchie|emphasize:$activite->code:"u"}}</td>
    <td>{{$_activite->code|emphasize:$activite->code:"u"}}</td>
    <td>{{$_activite->libelle|emphasize:$activite->code:"u"}}</td>
		<td style="text-align: center;">
			{{if $_activite->_ref_elements}}
			  <span onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-csarr-{{$_activite->code}}")'>{{$_activite->_ref_elements|@count}}</span>
				<table id="tooltip-content-csarr-{{$_activite->code}}" style="display: none;" class="tbl">
          <tr>
            <th class="title">Eléments de prescription</th>
					</tr>
					{{foreach from=$_activite->_ref_elements_by_cat item=_elements_by_cat}}
						{{foreach from=$_elements_by_cat item=_element name="foreach_elt"}}
						  {{assign var=elt_prescription value=$_element->_ref_element_prescription}}
              {{if $smarty.foreach.foreach_elt.first}}
							<tr>
								<th>{{$elt_prescription->_ref_category_prescription->_view}}</th>
						  </tr>
							{{/if}}
							<tr>
                <td>{{$elt_prescription->_view}}</td>
              </tr>
						{{/foreach}}
          {{/foreach}}
        </table>
			{{/if}}
			</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="4" class="empty">{{tr}}CActiviteCsARR.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
