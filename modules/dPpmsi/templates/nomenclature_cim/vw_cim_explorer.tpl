{{*
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module=pmsi script=DiagPMSI ajax=true}}
<script>
  changePage = function (page) {
    var oForm = getForm("filter-cim");
    $V(oForm.current, page);
    oForm.onsubmit();
  };

  Main.add(function () {
    var oForm = getForm("filter-cim");
    oForm.onsubmit();
  });
</script>

<form action="?" name="filter-cim" method="get" onsubmit=" return onSubmitFormAjax(this, null, 'filter_results');">
  <input type="hidden" name="m" value="dPpmsi" />
  <input type="hidden" name="current" value="0" />
  <input type="hidden" name="modal" value="{{$modal}}" />
  <input type="hidden" name="a" value="ajax_search_nomenclature_cim10" />

  <table class="form">
    <tr>
      <th>{{tr}}Keywords{{/tr}}</th>
      <td>
        <input name="words" type="text" value="{{$words}}" onchange="$V(this.form.elements.current, 0)"/>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button class="search" type="submit"> Afficher</button>
      </td>
    </tr>
  </table>
</form>
<div id="filter_results"></div>
