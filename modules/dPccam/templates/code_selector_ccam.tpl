<script type="text/javascript">
  searchCodes = function() {
    var url = new Url("dPccam", "code_selector_ccam");
    url.addParam("chir", "{{$chir}}");
    url.addParam("anesth", "{{$anesth}}");
    url.addParam("_keywords_code", $("_keywords_code").value);
    url.addParam("object_class", "{{$object_class}}");
    url.addParam("_all_codes", $("_all_codes").checked ? 1 : 0);
    url.addParam("only_list", 1);
    url.requestUpdate("code_area");
  }
</script>

<div class="info">
  Nouvelle interface de recherche de codes CCAM par mots-clés. <br />
  Vous pouvez retrouver l'ancienne recherche dans les préférences utilisateur (volet CCAM).
</div>

<table class="tbl">
  <tr>
    <th>
      Filtre de recherche
    </th>
  </tr>
    <td>
      Mot-clé : <input type="text" id="_keywords_code"/> <button onclick="searchCodes()" class="search notext"></button>
      <br />
      <label>
        <input type="checkbox" id="_all_codes" /> Chercher dans toute la base CCAM
      </label>
    </td>
  </tr>
</table>

<div id="code_area" style="height: 70%; text-align: left;">
  {{mb_include module=dPccam template=inc_code_selector_ccam}}
</div>