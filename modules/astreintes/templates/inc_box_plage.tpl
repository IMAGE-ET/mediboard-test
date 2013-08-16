{{*
  * plage box content
  *  
  * @category Astreintes
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{if !$_astreinte->libelle}}{{$_astreinte->type}}{{else}}{{$_astreinte->libelle}}{{/if}}<br/>
{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_astreinte->_ref_user}}<br/>
<span><img src="style/mediboard/images/buttons/phone.png" alt=""/>{{mb_value object=$_astreinte field=phone_astreinte}}</span><br/>
<a href="#astreinte_{{$_astreinte->_id}}" onclick="PlageAstreinte.modal('{{$_astreinte->_id}}')">{{tr}}Edit{{/tr}}</a>
{{if ($mode == "day") || ($mode=="week")}}
{{if $_astreinte->start|date_format:"%H:%M" != "00:00"}}<span class="startTime incline">{{$_astreinte->start|date_format:"%H:%M"}}</span>{{else}}<span class="startTime"><</span>{{/if}}
{{if $_astreinte->end|date_format:"%H:%M" != "23:59"}}<span class="endTime incline">{{$_astreinte->end|date_format:"%H:%M"}}</span>{{else}}<span class="endTime">></span>{{/if}}
{{/if}}