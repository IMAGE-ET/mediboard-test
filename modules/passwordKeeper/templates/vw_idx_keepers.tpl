{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

{{mb_script module="passwordKeeper" script="keeper"}}

<script type="text/javascript">
  Main.add(function(){
    Keeper.showListKeeper();
  })
</script>

<table class="main">
  <tr>
    <td>
      <button type="button" class="new" onclick="Keeper.showKeeper('0')">{{tr}}CPasswordKeeper-title-create{{/tr}}</button>
    </td>
  </tr>
  <tr>
    <td style="width: 30%" id="vw_list_keeper">
    </td>
    <td id="vw_edit_keeper">
    </td>
  </tr>
</table>