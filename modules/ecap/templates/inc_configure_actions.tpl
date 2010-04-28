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

      <button class="change" onclick="Marks.makeInternal()">
        {{tr}}config-ecap-MakeInternal{{/tr}}
      </button>
    </td>
    
    <td class="text" id="makeInternal"></td>
  </tr>

</table>
