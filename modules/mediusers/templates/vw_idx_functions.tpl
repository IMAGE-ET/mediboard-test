{{mb_script module="mediusers" script="color_selector"}}
{{mb_script module="system" script="object_selector"}}
{{mb_script module="patients" script="autocomplete"}}

<script type="text/javascript">

showFunction = function(function_id, element) {
  if (element && !element._class) {
    element.up('tr').addUniqueClassName('selected');
  }
  var url = new Url("mediusers", "ajax_edit_function");
  url.addParam("function_id", function_id);
  url.requestUpdate("vw_function");
}

function changePage(page) {
  $V(getForm('listFilter').page,page);
}

Main.add(showFunction.curry('{{$function_id}}'));
</script>

<table class="main">
  {{if $can->edit}}
  <tr>
    <td style="width: 60%">
      <a href="#" class="button new" onclick="showFunction(0);">
       {{tr}}CFunctions-title-create{{/tr}}
      </a>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td>
      <form name="listFilter" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        <table class="form">
          <tr>
            <th class="title" colspan="10"> {{tr}}Filter{{/tr}} </th>
          </tr>
          <tr> 
            <th>Type</th> 
            <td>
              <select name="type" onchange="$V(this.form.page, 0)"> 
                <option value="" {{if !$type}}selected="selected"{{/if}}>Tous</option> 
                <option value="administratif" {{if $type == "administratif"}}selected="selected"{{/if}}>Administratif</option> 
                <option value="cabinet" {{if $type == "cabinet"}}selected="selected"{{/if}}>Cabinet</option> 
              </select>
            </td> 
          </tr> 
          <tr>
            <th></th>
            <td>
              <input onclick="$V(this.form.page, 0)" type="checkbox" name="inactif" {{if $inactif}}checked="checked"{{/if}} /> Inactif
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: center">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>  
        {{if $total_functions != 0}}
          {{mb_include module=system template=inc_pagination total=$total_functions current=$page change_page='changePage' step=25}}
        {{/if}}
      </form>
      {{mb_include template=vw_list_functions}}
    </td>
    <td style="width: 40%" id="vw_function">
    </td>
  </tr>
</table>