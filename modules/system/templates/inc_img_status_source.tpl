{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{unique_id var=uid}}

{{main}}
  ExchangeSource.resfreshImageStatus($('{{$uid}}'));
{{/main}}
          
<img class="status" id="{{$uid}}" data-id="{{$exchange_source->_id}}" 
  data-guid="{{$exchange_source->_guid}}" src="images/icons/status_grey.png" 
  title="{{$exchange_source->name}}"/>