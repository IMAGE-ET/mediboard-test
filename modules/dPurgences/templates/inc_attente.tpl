{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$sejour->_veille}}
  <span style="float: right;">
  {{assign var=rpu value=$sejour->_ref_rpu}}
    {{if $rpu->_presence < $dPconfig.dPurgences.attente_first_part}}
      <img src="images/icons/attente_first_part.png"
           title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})" />
    {{elseif $rpu->_presence >= $dPconfig.dPurgences.attente_first_part &&
             $rpu->_presence < $dPconfig.dPurgences.attente_second_part}}
      <img src="images/icons/attente_second_part.png"
           title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})"/>
    {{elseif $rpu->_presence >= $dPconfig.dPurgences.attente_second_part &&
             $rpu->_presence < $dPconfig.dPurgences.attente_third_part}}
      <img src="images/icons/attente_third_part.png"
           title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})" />
    {{else}}
      <img src="images/icons/attente_fourth_part.png"
           title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})"/>
    {{/if}}
  </span>
  {{if $sejour->sortie_reelle}}
    <br />(sortie à {{$sejour->sortie_reelle|date_format:$dPconfig.time}})
  {{/if}}
{{else}}
  Admis la veille
{{/if}}
