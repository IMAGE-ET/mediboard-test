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
  viewSegments : function (actor_guid, profil, message_class) {
    new Url("hl7", "ajax_hl7_transformation")
      .addParam("actor_guid"   , actor_guid)
      .addParam("profil"       , profil)
      .addParam("message_class", message_class)
      .requestModal("80%", "80%");

    return false;
  },

  viewFields : function (actor_guid, profil, segment_name, version, extension, message) {
    new Url("hl7", "ajax_hl7_transformation_fields")
      .addParam("actor_guid"  , actor_guid)
      .addParam("profil"      , profil)
      .addParam("segment_name", segment_name)
      .addParam("version"     , version)
      .addParam("extension"   , extension)
      .addParam("message"     , message)
      .requestUpdate("hl7-transformation")
  }
}