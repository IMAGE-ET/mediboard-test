<script type="text/javascript">
Main.add(function(){
  MbObject.list("{{$object_class}}", {{$columns|@json}});
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
