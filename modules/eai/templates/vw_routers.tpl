{{*
 * View route EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=route ajax=1}}

<script>
  Main.add(
    function () {
      Route.refreshList();
    }
  )
</script>

<button type="button" class="new" onclick="Route.edit()">
  {{tr}}New-female{{/tr}} {{tr}}CEAIRoute{{/tr}}
</button>
<div id="list_route"></div>