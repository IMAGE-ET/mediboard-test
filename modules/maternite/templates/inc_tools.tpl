{{*
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  function mapGroupId(form) {
    var url = new Url("maternite", "ajax_map_group_id");
    url.addParam("limit", $V(form.limit));
    url.requestUpdate("result_area", function() {
      if (form.auto.checked) {
        mapGroupId(form);
      }
    });
  }

  Main.add(function() {
    getForm("mapGroup").limit.addSpinner({min: 0, step: 50});
  });
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  <tr>
    <td style="vertical-align: top;">
      <form name="mapGroup" method="get">
        Limite : <input type="text" name="limit" value="100" size="4" />
        <label>
          <input type="checkbox" name="auto"/> Auto
        </label>
        <button type="button" class="tick" onclick="mapGroupId(this.form)">Associer le bon établissement</button>
      </form>
    </td>
    <td id="result_area"></td>
  </tr>
</table>