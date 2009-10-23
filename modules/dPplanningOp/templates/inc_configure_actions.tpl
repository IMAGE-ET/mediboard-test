<script type="text/javascript">
viewNoPratSejour = function() {
  var url = new Url("dPplanningOp", "vw_resp_no_prat"); 
  url.popup(700, 500, "printFiche");
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
      <button class="change" onclick="viewNoPratSejour()">
        Corriger les praticiens des séjours
      </button>
    </td>
    <td>
    </td>
  </tr>

</table>