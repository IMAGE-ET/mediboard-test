{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=level value="medium"}}
{{mb_default var=tag   value=""}}
{{mb_default var=callback value="Prototype.emptyFunction"}}
{{mb_default var=nb_alerts value=$object->_count_alerts_not_handled}}
{{assign var=object_guid value=$object->_guid}}
{{assign var=img_ampoule value="ampoule"}}

{{if $level == "high"}}
  {{assign var=img_ampoule value="ampoule_urgence"}}
{{/if}}

{{if $nb_alerts}}
  <span id="span-alerts-{{$level}}-{{$tag}}-{{$object->_guid}}">
    <img src="images/icons/{{$img_ampoule}}.png"
         onclick="Alert.showAlerts('{{$object_guid}}', '{{$tag}}', '{{$level}}', {{$callback}}, this);"/>
    {{mb_include module=system template=inc_vw_counter_tip count=$nb_alerts}}
  </span>

  <div id="tooltip-alerts-{{$level}}-{{$object_guid}}" style="display: none; height: 400px; width: 400px; overflow-x:auto;"></div>
{{/if}}