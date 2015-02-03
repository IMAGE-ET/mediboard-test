{{*
 * View PIX profile
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function () {
    new Url("sip", "patient_identity_consumer")
      .requestUpdate("search_pix");
  });
</script>

<div id="search_pix"></div>