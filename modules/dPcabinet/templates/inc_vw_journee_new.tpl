{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    var height_planning = '{{$height_planning_journee}}';
    ViewPort.SetAvlHeight("planningInterventions", 1);
    $('planningWeek').setStyle({height : "2000px"});
  });
</script>

<div id="planningInterventions">
  {{mb_include module=system template=calendars/vw_week}}
</div>