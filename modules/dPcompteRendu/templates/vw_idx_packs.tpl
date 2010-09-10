<!--  $Id$ -->

<script type="text/javascript">

Main.add(function () {
	reloadList('{{$pack_id}}', '{{$user_id}}', '{{$filter_class}}');
	updateAddEditPack.defer('{{$pack_id}}', '{{$filter_class}}');
});

function updateAddEditPack(pack_id, filter_class) {
	var url = new Url("dPcompteRendu", "ajax_add_edit_pack");
	url.addParam("pack_id", pack_id);
	url.addParam("filter_class", filter_class);
  url.requestUpdate("add_edit_pack");
  if (pack_id != '') {
	  var lists = new Array("owner-user", "owner-func", "owner-etab");
	  lists.each(function(elem) {
	    if ($(elem) != null) {
			  firstchild = $(elem).firstDescendant();
			  firstchild.className = '';
			  var siblings = firstchild.siblings();
			  siblings.each(function(item) {
			    item.className = '';
			  });
      }
	  });
	  if ($("p"+pack_id) != null) {
		  var pack = $("p"+pack_id);
		  pack.className = "selected";
	  }
	} 
}

function reloadList(pack_id, filter_user_id, filter_class) {
	var url = new Url("dPcompteRendu", "ajax_list_pack");
	url.addParam("pack_id", pack_id);
	url.addParam("filter_user_id", filter_user_id);
  if (filter_class != undefined) {
	  url.addParam("filter_class", filter_class);
  }
	url.requestUpdate("list-pack");
}
</script>

<table class="main">

<tr>
  <td class="greedyPane">
    <div id="list-pack">
    </div>
  </td>
  
  <td>
   <div id="add_edit_pack">
    
   </div>
  </td>
</tr>
</table>
