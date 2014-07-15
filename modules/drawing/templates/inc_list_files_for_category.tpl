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

<!-- use the "selectThisElement" function -->

<ul>
  {{foreach from=$category->_ref_files item=_file}}
    <li>
      <a href="#"
         onclick="selectThisElement(this);"
         data-file_id="{{$_file->_id}}"
         data-file_path="{{$_file->_file_path}}"
         data-file_type="{{$_file->file_type}}"
         onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}');">
        <img src="?m=files&amp;a=fileviewer&amp;file_id={{$_file->_id}}&amp;phpThumb=1&amp;w=200" alt="" />
      </a>
    </li>
  {{/foreach}}
</ul>