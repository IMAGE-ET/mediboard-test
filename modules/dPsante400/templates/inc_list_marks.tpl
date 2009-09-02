<script type="text/javascript">
function retry(sClass, iRec) {
  var url = new Url("{{$m}}", "synchro_sante400");
  url.addParam("class", sClass);
  url.addParam("rec", iRec);
  url.addParam("verbose", 1);
  url.popup(900, 700, "Explaination Import Sante400");
}
  
</script>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CTriggerMark field=trigger_number}}</th>
    <th>{{mb_title class=CTriggerMark field=trigger_class}}</th>
    <th>{{mb_title class=CTriggerMark field=mark}}</th>  
    <th colspan="2" style="width: 1%">{{mb_title class=CTriggerMark field=done}}</th>  
  </tr>
  
  {{assign var=href value="?m=dPsante400&$actionType=$action&dialog=$dialog"}}
  
  {{foreach from=$marks item=_mark}}
  <tr {{if $_mark->_id == $mark->_id}}class="selected"{{/if}}>
    <td>
      <a href="{{$href}}&amp;mark_id={{$_mark->_id}}">
      	{{mb_value object=$_mark field=trigger_number}}
      </a>
    <td>{{tr}}{{mb_value object=$_mark field=trigger_class}}{{/tr}}</td>
    </td>
    <td>{{mb_value object=$_mark field=mark}}</td>
    <td>{{mb_value object=$_mark field=done}}</td>
	  <td class="button">
	    <button class="search" onclick="retry('{{$_mark->trigger_class}}', '{{$_mark->trigger_number}}');">
	      {{tr}}Retry{{/tr}}
	    </button>
	  </td>
  </tr>
  {{/foreach}}
</table>
