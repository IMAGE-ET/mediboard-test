{{* $Id: inc_consultations.tpl$  *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPcabinet" script="icone_selector" ajax=true}}
{{mb_script module="dPpatients" script="patient" ajax=true}}


<table class="tbl">
  {{mb_include module=cabinet template=inc_consultations_lines chirSel=$plageSel->chir_id}}
</table>