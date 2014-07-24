{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=system script=exchange_source ajax=true}}

{{mb_default var=actor_actif        value=""}}
{{mb_default var=actor_parent_class value=""}}
{{unique_id var=uid}}

{{main}}
  ExchangeSource.resfreshImageStatus($('{{$uid}}'), '{{$actor_actif}}', '{{$actor_parent_class}}');
{{/main}}
          
<img class="status" id="{{$uid}}" data-id="{{$exchange_source->_id}}" 
  data-guid="{{$exchange_source->_guid}}" src="images/icons/status_grey.png" 
  title="{{$exchange_source->name}}"/>