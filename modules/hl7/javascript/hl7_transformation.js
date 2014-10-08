/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

HL7_Transformation = {
  viewSegments : function (actor_guid, message_class) {
    new Url("hl7", "ajax_hl7_transformation")
      .addParam("actor_guid"   , actor_guid)
      .addParam("message_class", message_class)
      .requestModal("80%", "60%");

    return false;
  },

  viewFields : function (actor_guid, segment_name) {
    new Url("hl7", "ajax_hl7_transformation_fields")
      .addParam("actor_guid"  , actor_guid)
      .addParam("segment_name", segment_name)
      .requestUpdate("hl7-transformation")
  }
}