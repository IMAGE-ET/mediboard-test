{{*
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=context_guid value=""}}

<script>
  addDocument = function(context_guid) {
    var url = new Url("patients", "ajax_add_doc");
    var form = getForm("filterDisplay");
    if (form) {
      url.addFormData(form);
    }
    url.addParam("patient_id"  , '{{$patient_id}}');
    url.addParam("context_guid", context_guid);
    url.requestModal("70%", "70%");
  };
</script>

<button type="button" class="add" onclick="addDocument('{{$context_guid}}');">Ajouter un document</button>