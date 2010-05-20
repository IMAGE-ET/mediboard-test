{{mb_include_script module="mediusers" script="color_selector"}}
{{mb_include_script module="system" script="object_selector"}}

<script type="text/javascript">

ColorSelector.init = function(){
  this.sForm  = "editFrm";
  this.sColor = "color";
	this.sColorView = "select_color";
  this.pop();
}

showFunction = function(function_id, element){
  element.up('tr').addUniqueClassName('selected');
  var url = new Url("mediusers", "ajax_edit_function");
  url.addParam("function_id", function_id);
  url.requestUpdate("vw_function");
}

function changePage(page) {
  $V(getForm('listFilter').page,page);
}

</script>

<table class="main">
  <tr>
    <td style="width: 60%">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id=0" class="button new">
       {{tr}}CFunctions-title-create{{/tr}}
      </a>
    </td>
  </tr>
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
          {{mb_include module=system template=inc_pagination total=$total_functions current=$page change_page='changePage' step=35}}
        {{/if}}
      </form>
      {{include file="vw_list_functions.tpl"}}
    </td>
    
    
    <td style="width: 40%" id="vw_function">
      {{include file="inc_edit_function.tpl"}}
    </td>
  </tr>
</table>