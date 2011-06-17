<div class="info">
  Nouvelle interface de recherche de codes CCAM par mots-clés. <br />
  Vous pouvez retrouver l'ancienne recherche dans les préférences utilisateur (volet CCAM).
</div>

<form name="filterCode" method="get" action="?" onsubmit="return onSubmitFormAjax(this, null, 'code_area');">
  <input type="hidden" name="m" value="dPccam" />
  <input type="hidden" name="a" value="code_selector_ccam" />
  <input type="hidden" name="only_list" value="1" />
  <input type="hidden" name="_all_codes" value="0" />
  <input type="hidden" name="chir" value="{{$chir}}" />
  <input type="hidden" name="anesth" value="{{$anesth}}" />
  <table class="tbl">
    <tr>
      <th>
        Filtre de recherche
      </th>
    </tr>
      <td>
        Mot-clé : <input type="text" name="_keywords_code"/> <button type="submit" class="search notext"></button>
        <br />
        <label>
          <input type="checkbox" id="_all_codes_view" onchange="$V(this.form._all_codes, this.checked ? 1 : 0)"/> Chercher dans toute la base CCAM
        </label>
      </td>
    </tr>
  </table>
</form>
<div id="code_area" style="height: 60%; text-align: left;">
  {{mb_include module=dPccam template=inc_code_selector_ccam}}
</div>