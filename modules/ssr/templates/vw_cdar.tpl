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
          <th>{{mb_label object=$activite field=code}}</th>
          <td>{{mb_field object=$activite field=code canNull=true  onchange="this.form.current.value = 0"}}</td>
          <th>{{mb_label object=$activite field=type}}</th>
          <td>
            <select name="type" onchange="this.form.current.value = 0">
              <option value="">&mdash choisir un type</option>
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
        </tr>
        {{foreach from=$listActivites item=_activite}}
        <tr>
          <td>{{$_activite->type}}</td>
          <td>{{$_activite->code}}</td>
          <td>{{$_activite->libelle}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="3">{{tr}}CActiviteCdARR.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>