{{* $Id: inc_attente.tpl 10548 2010-11-03 16:55:25Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 10548 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<span style="float: right;">
  {{if $rpu->_presence < $conf.dPurgences.attente_first_part}}
    <img src="images/icons/attente_first_part.png"
         title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})" />
  {{elseif $rpu->_presence >= $conf.dPurgences.attente_first_part &&
           $rpu->_presence < $conf.dPurgences.attente_second_part}}
    <img src="images/icons/attente_second_part.png"
         title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})"/>
  {{elseif $rpu->_presence >= $conf.dPurgences.attente_second_part &&
           $rpu->_presence < $conf.dPurgences.attente_third_part}}
    <img src="images/icons/attente_third_part.png"
         title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})" />
  {{else}}
    <img src="images/icons/attente_fourth_part.png"
         title = "({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})"/>
  {{/if}}
</span>