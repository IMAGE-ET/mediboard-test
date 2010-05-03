<h2>Etat de facturation et HPRIM XML</h2>

<script type="text/javascript">

Action = {
  trigger: function(sAction) {
    url = new Url("dPpmsi", sAction);
		url.requestUpdate(sAction);
	}
}

</script>

<table class="tbl">
  <tr>
    <th class="category">{{tr}}Action{{/tr}}</th>
    <th class="category">{{tr}}Result{{/tr}}</th>
  </tr>
	
  <tr>
    <td>
    	<button class="change" onclick="Action.trigger('apply_facture_hprim_intervention');">
    	  {{tr}}apply_facture_hprim_intervention{{/tr}}
			</button>
		</td>
    <td class="text" id="apply_facture_hprim_intervention">
    	
    </td>
  </tr>
	
</table>
