{{* $Id: inc_attente.tpl 10548 2010-11-03 16:55:25Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 10548 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=presence value=$rpu->_presence|date_format:$conf.time}}
{{assign var=attente value=$rpu->_attente|date_format:$conf.time}}
{{assign var="img_title" value="(`$attente` / `$presence`)"}}

{{if !$rpu->radio_fin && $rpu->radio_debut}}
  {{assign var=radio_debut value=$rpu->radio_debut|date_format:$conf.time}}
  {{assign var="img_title" value="`$img_title` \nDépart radio : `$radio_debut`"}}
{{/if}}
{{if !$rpu->bio_retour && $rpu->bio_depart}}
  {{assign var=bio_depart value=$rpu->bio_depart|date_format:$conf.time}}
  {{assign var="img_title" value="`$img_title` \nDépart bio : `$bio_depart`"}}
{{/if}}
{{if !$rpu->specia_arr && $rpu->specia_att}}
  {{assign var=specia_att value=$rpu->specia_att|date_format:$conf.time}}
  {{assign var="img_title" value="`$img_title` \nAttente spécialiste : `$specia_att`"}}
{{/if}}
{{mb_default var=width value="24"}}

<span style="float: right;">
  {{if $rpu->_presence < $conf.dPurgences.attente_first_part}}
    <img src="images/icons/attente_first_part.png"
         title = "{{$img_title}}" width="{{$width}}"/>
  {{elseif $rpu->_presence >= $conf.dPurgences.attente_first_part &&
           $rpu->_presence < $conf.dPurgences.attente_second_part}}
    <img src="images/icons/attente_second_part.png"
         title = "{{$img_title}}" width="{{$width}}"/>
  {{elseif $rpu->_presence >= $conf.dPurgences.attente_second_part &&
           $rpu->_presence < $conf.dPurgences.attente_third_part}}
    <img src="images/icons/attente_third_part.png"
         title = "{{$img_title}}" width="{{$width}}"/>
  {{else}}
    <img src="images/icons/attente_fourth_part.png"
         title = "{{$img_title}}" width="{{$width}}"/>
  {{/if}}
</span>