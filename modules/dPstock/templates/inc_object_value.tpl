{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

{{if $object->_class_name=="CProductStockGroup" && $field=="bargraph"}}
  {{include file="inc_bargraph.tpl" stock=$object}}
{{else}}
  {{mb_value object=$object field=$field}}
{{/if}}