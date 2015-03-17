{{*
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
{{mb_script module=dPpmsi script=DiagPMSI ajax=true}}

<!--  Diagnostic Principal (OMS et à visée PMSI)-->
{{mb_include module=dPpmsi template=inc_diag_pmsi_dp}}
<!--  Diagnostic Relié (OMS et à visée PMSI)-->
{{mb_include module=dPpmsi template=inc_diag_pmsi_dr}}
<!--  Diagnostics Associés (OMS et à visée PMSI)-->
{{mb_include module=dPpmsi template=inc_diag_pmsi_das}}