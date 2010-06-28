<h2>Import de la base de données des codes INSEE / ISO</h2>

{{mb_include module=system template=configure_dsn dsn=INSEE}}

<script type="text/javascript">

function startINSEE() {
  var url = new Url("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("action-insee");
}

</script>

<table class="tbl">

<tr>
  <th>{{tr}}Action{{/tr}}</th>
  <th>{{tr}}Status{{/tr}}</th>
</tr>
  
<tr>
  <td>
    <button class="tick" onclick="startINSEE()">
      Importer les codes INSEE / ISO
    </button>
  </td>
  <td id="action-insee" />
</tr>

</table>
