{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  Main.add(function() {
    Grossesse.refreshList('{{$parturiente_id}}', '{{$object_guid}}');
  });
</script>

<button class="new" onclick="Grossesse.editGrossesse(0, '{{$parturiente_id}}')" style="float: left;">{{tr}}CGrossesse-title-create{{/tr}}</button>

<table class="main layout">
  <tr>
    <td style="width: 40%">
      <div id="list_grossesses"></div>
      <div style="text-align: right;">
        <button type="button" class="tick" onclick="Grossesse.bindGrossesse(); Control.Modal.close();">Sélectionner</button>
        <button type="button" class="cancel" onclick="Grossesse.emptyGrossesses(); Control.Modal.close();">Vider</button>
      </div>
    </td>
    <td id="edit_grossesse"></td>
  </tr>
</table>
