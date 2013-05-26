{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $object->_class=="CProductStockGroup" && $field=="bargraph"}}
  {{include file="inc_bargraph.tpl" stock=$object}}
{{else}}
  {{mb_value object=$object field=$field}}
{{/if}}