/**
 * $Id$
 *
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCirconstance = {
  edit : function(id) {
    new Url("dPurgences", "ajax_edit_circonstance")
      .addParam("id", id)
      .requestModal();
  },

  searchMotifSFMU : function() {
    new Url("dPurgences", "ajax_search_motif_sfmu")
      .requestModal(600, 500);
  },

  displayMotifFromCategorie : function(value) {
    new Url("dPurgences", "ajax_display_motif_sfmu_category")
      .addParam("categorie", value)
      .requestUpdate("motif_sfmu_by_category");
  },

  selectMotifSFMU : function(libelle, id) {
    var form = getForm("editRPU");
    $V(form["motif_sfmu"], id);
    $V(form["motif_sfmu_autocomplete_view"], libelle);
    Control.Modal.close();
  }
};