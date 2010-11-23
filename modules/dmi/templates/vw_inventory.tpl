{{main}}
  getForm("inventory-search-form").barcode.select();
{{/main}}

<form name="inventory-search-form" method="get" action="?" onsubmit="this.barcode.select(); return Url.update(this, 'search-dmi-result')">
  <input type="hidden" name="m" value="dmi" />
  <input type="hidden" name="a" value="httpreq_search_dmi_by_barcode" />
  
  <table class="main tbl" style="table-layout: fixed;">
    <tr>
      <td style="text-align: right;">
        <input type="text" name="barcode" class="barcode" style="font-size: 1.2em;" size="40" value="{{$barcode}}" />
      </td>
      <td>
        <button type="submit" class="search notext"></button>
      </td>
    </tr>
  </table>

</form>

<div id="search-dmi-result"></div>