{{*
 * $Id$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  reloadIntervArea = function() {
    var url = new Url("planningOp", "ajax_reload_infos_interv");
    url.addParam("operation_id", '{{$operation->_id}}');
    url.requestUpdate("info_interv_area");
  }
</script>

<span id="info_interv_area">
  {{mb_include module=system template=inc_icon_alerts
    object=$operation
    callback="reloadIntervArea"}}
  {{if $operation->_canEdit}}
    {{mb_include module=planningOp template=inc_button_infos_interv operation_id=$operation->_id callback="reloadIntervArea"}}
  {{/if}}
  {{if $operation->libelle}}{{$operation->libelle}} &mdash;{{/if}}
  {{mb_label object=$operation field=cote}} :
  {{if !($conf.dPplanningOp.COperation.verif_cote && !$operation->cote_bloc) || ($operation->cote != "droit" && $operation->cote != "gauche")}}
    {{mb_value object=$operation field=cote}}
  {{else}}
    Non validé en salle
  {{/if}}
</span>