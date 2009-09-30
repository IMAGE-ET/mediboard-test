<script type="text/javascript">

var Actions = {
  civilite: function(mode) {
	  if (mode == "repair") {
		  if (!confirm("Etes-vous sur de vouloir réparer les civilités ?")) {
			  return;
			}
		}
	  var url = new Url("dPpatients", "ajax_civilite");
		url.addParam("mode", mode);
		url.requestUpdate("ajax_civilite");
	}
}
</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
	<tr>
	  <th style="width: 50%">Action</th>
	  <th style="width: 50%">Status</th>
	</tr>
	  
	<tr>
	  <td>
			<button class="search" onclick="Actions.civilite('check')">
			  Vérifier les civilités
			</button>
			<br />
      <button class="change" onclick="Actions.civilite('repair')">
        Corriger les civilités
      </button>
	  </td>
	  <td id="ajax_civilite">
	  </td>
	</tr>

</table>