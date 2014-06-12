/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPsante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

Idex = {
  edit: function(object_guid, tag) {
    var parts = object_guid.split("-");

    new Url('sante400', 'ajax_edit_identifiant')
      .addParam("object_class", parts[0])
      .addParam("object_id"   , parts[1])
      .addParam('tag'         , tag)
      .addParam('load_unique' , 1)
      .addParam('dialog'      , 1)
      .requestModal(400);
  },

  edit_manually : function(sejour_guid, patient_guid, callback) {
    new Url("dPsante400", "ajax_edit_manually_ipp_nda")
      .addParam("sejour_guid" , sejour_guid)
      .addParam("patient_guid", patient_guid)
      .requestModal("40%", "40%")
      .modalObject.observe("afterClose", callback)
  }
};
