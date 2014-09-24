{{*
 * $Id$
 *  
 * @category Reservation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  edit_sejour = function() {
    var url = new Url("dPplanningOp", "vw_edit_sejour");
    url.requestUpdate("sejour_dom_target");
  };

  Main.add(function() {
    edit_sejour();
  });

</script>


<div id="sejour_dom_target">

</div>