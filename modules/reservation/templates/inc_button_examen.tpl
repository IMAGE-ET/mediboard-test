{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_default var=form value=""}}

<script type="text/javascript">
  openExamens = function() {
    var form = getForm("{{$form}}");
    var url = new Url("reservation", "ajax_edit_examen");
    url.addParam("examen_operation_id", $V(form.examen_operation_id));
    url.requestModal(600, 400);
  }
  
  afterSaveExamen = function(examen_operation_id) {
    var form = getForm("{{$form}}");
    $V(form.examen_operation_id, examen_operation_id);
  }
</script>

<button type="button" class="new" onclick="openExamens()">Examens</button> 
