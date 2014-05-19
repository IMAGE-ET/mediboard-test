{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_include module=soins template=inc_vw_antecedents}}

{{if $dossier_medical->_id && $dossier_medical->_count_allergies}}
  <script type="text/javascript">
    ObjectTooltip.modes.allergies = {
      module: "patients",
      action: "ajax_vw_allergies",
      sClass: "tooltip"
    };
  </script>
  <img src="images/icons/warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$patient_guid}}', 'allergies');" />
{{/if}}