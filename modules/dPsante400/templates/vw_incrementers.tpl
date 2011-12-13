<script type="text/javascript">

showIncrementer = function(incrementer_id, element){
	if (element) {
		element.up('tr').addUniqueClassName('selected');
	}
  
  var url = new Url("dPsante400", "ajax_edit_incrementer");
  url.addParam("incrementer_id", incrementer_id);
  url.requestUpdate("vw_incrementer");
}
 
</script>

<table class="main">
  <tr>
    <td style="width: 60%">
      <a href="#" onclick="showIncrementer(0)" class="button new">
        {{tr}}CIncrementer-title-create{{/tr}}
      </a>
    </td>
  </tr>
  <tr>
    <td>      
      {{mb_include template=inc_list_incrementers}}
    </td>
    <td style="width: 40%" id="vw_incrementer">
      {{mb_include template=inc_edit_incrementer}}
    </td>
  </tr>
</table>