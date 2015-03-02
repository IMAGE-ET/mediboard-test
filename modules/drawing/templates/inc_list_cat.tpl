{{*
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2><button class="edit notext" onclick="DrawingCategory.editModal('{{$object->_id}}', null, null, refreshList)"></button> {{$object}} <button class="upload notext" onclick="File.upload('{{$object->_class}}', '{{$object->_id}}')"></button></h2>
{{foreach from=$object->_ref_files item=_file}}
  <img src="?m=files&amp;a=fileviewer&amp;file_id={{$_file->_id}}&amp;phpThumb=1&amp;suppressHeaders=1&amp;w=150&amp;h=150" style="max-width:150px; max-height: 150px;" alt="{{$_file}}" />
{{/foreach}}