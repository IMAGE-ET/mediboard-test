<script type="text/javascript">
Main.add(function(){
  var url = new Url("system", "ajax_object_tag_tree");
	url.addParam("object_class", "{{$object_class}}");
  url.addParam("col[]", {{$columns|@json}});
	url.requestUpdate("tag-tree");
	
	MbObject.edit("{{$object_guid}}");
});
</script>

<table class="main layout">
	<col style="width: 30%; max-width: 60%;" />
	
	<tr>
    <td id="tag-tree"> </td>
    <td id="object-editor">&nbsp;</td>
	</tr>
</table>
