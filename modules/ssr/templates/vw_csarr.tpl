<script type="text/javascript">

function changePage(page){
  oForm = getForm("filter-activite");
  $V(oForm.current, page);
  oForm.submit();
  
}

</script>

{{mb_script module=ssr script=csarr}}

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
    <th class="narrow">{{mb_title object=$activite field=hierarchie}}</th>
    <th class="narrow">{{mb_title object=$activite field=code}}</th>
    <th>{{mb_title object=$activite field=libelle}}</th>
    <th colspan="3" class="narrow">
      <label title="Eléments de prescription et actes réalisés">Usage</label>
    </th>
  </tr>
  {{foreach from=$listActivites item=_activite}}
  <tr>
    <td>
      <button class="compact search"  onclick="CsARR.viewHierarchie('{{$_activite->hierarchie}}')">
        {{$_activite->hierarchie|emphasize:$activite->code:"u"}}
      </button></td>
    <td>
      <button class="compact search" onclick="CsARR.viewActivite('{{$_activite->code}}')">
        {{$_activite->code|emphasize:$activite->code:"u"}}
      </button>
    </td>
    <td>{{$_activite->libelle|emphasize:$activite->code:"u"}}</td>
    <td class="narrow" style="text-align: center;">
      {{if $_activite->_count_elements}}
        {{$_activite->_count_elements}}
      {{/if}}
    </td>
    <td class="narrow" style="text-align: center;">
      {{if $_activite->_count_actes}}
        {{$_activite->_count_actes}}
      {{/if}}
    </td>
    <td class="narrow">
      <button class="compact search notext" onclick="CsARR.viewActiviteStats('{{$_activite->code}}')">
        {{tr}}Stats{{/tr}}
      </button>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="4" class="empty">{{tr}}CActiviteCsARR.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
