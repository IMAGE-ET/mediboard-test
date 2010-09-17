<script type="text/javascript">
function editEtiq(id) {
	var url = new Url("dPhospi", "ajax_edit_modele_etiquette");
	url.addParam("modele_etiquette_id", id);
	url.requestUpdate("edit_etiq");
}

function refreshList(filter_class) {
	var url = new Url("dPhospi", "ajax_list_modele_etiquette");
  if (filter_class != '')
	  url.addParam("filter_class", filter_class);
	url.requestUpdate("list_etiq");
}
</script>

{{main}}
  editEtiq('{{$modele_etiquette_id}}');
  refreshList('{{$filter_class}}');
{{/main}}

<table class="main">
  <tr>
    <td id="list_etiq" style="width: 45%;">
    
    </td>
    <!-- Création / Modification de l'étiquette -->
	  <td id="edit_etiq">
	  </td>
  </tr>
</table>