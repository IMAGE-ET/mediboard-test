{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=codable_id value=$_codable->_id}}
{{assign var=codable_class value=$_codable->_class_name}}
{{if @$entCCAM.$codable_class.$codable_id}}
<div class="error">{{$entCCAM.$codable_class.$codable_id}}</div>
{{else}}
<div class="message">Entête CCAM correctement exporté</div>
{{/if}}
