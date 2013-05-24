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
    <td id="action-insee"></td>
  </tr>
</table>

{{assign var=class value=INSEE}}
<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />
<table class="form"> 
  {{mb_include module=system template=inc_config_bool var=france}}
  {{mb_include module=system template=inc_config_bool var=suisse}}
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>
