<script type="text/javascript">

function changePage(page){
  oForm = getForm("filter-activite");
  $V(oForm.current, page);
  oForm.submit();
  
}

</script>

<table class="main">
  <tr>
    <td>
      <form action="?" name="filter-activite" method="get" >

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
      <input type="hidden" name="current" value="{{$current}}" />
      <table class="form">
        <tr>
          <th>{{tr}}Keywords{{/tr}}</th>
          <td><input name="code" type="text" value="{{$activite->code}}" /></td>
          <th>{{mb_label object=$activite field=type}}</th>
          <td>
            <select name="type">
              <option value="">&mdash; Choisir un type</option>
              {{foreach from=$listTypes item=_type}}
              <option value="{{$_type->code}}" {{if $_type->code == $activite->type}}selected="selected"{{/if}}>
                {{$_type->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <button class="search" type="submit">Afficher</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td>
      {{mb_include module=system template=inc_pagination change_page=changePage}}
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>{{mb_title object=$activite field=type}}</th>
          <th>{{mb_title object=$activite field=code}}</th>
          <th>{{mb_title object=$activite field=libelle}}</th>
					<th style="width: 1%">Nb. éléments</th>
        </tr>
        {{foreach from=$listActivites item=_activite}}
        <tr>
          <td>{{$_activite->type|emphasize:$activite->code:"u"}}</td>
          <td>{{$_activite->code|emphasize:$activite->code:"u"}}</td>
          <td>{{$_activite->libelle|emphasize:$activite->code:"u"}}</td>
					<td style="text-align: center;">
						{{if $_activite->_ref_elements}}
						  <span onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-cdarr-{{$_activite->code}}")'>{{$_activite->_ref_elements|@count}}</span>
							<table id="tooltip-content-cdarr-{{$_activite->code}}" style="display: none;" class="tbl">
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
          <td colspan="4">{{tr}}CActiviteCdARR.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>