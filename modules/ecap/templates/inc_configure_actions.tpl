<h2>Mouvements</h2>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>

  <tr>
    <td>
			<script type="text/javascript">
			
			var Marks = {
			  makeInternal: function(sAction) {
			    var url = new Url("ecap", "ajax_make_internal");
			    url.requestUpdate("makeInternal");
			  }
			}
			
			</script>

      <button class="search" onclick="Marks.makeInternal()">
        {{tr}}Config-Ecap-MakeInternal{{/tr}}
      </button>
    </td>
    
    <td class="text" id="makeInternal" />
  </tr>

</table>
