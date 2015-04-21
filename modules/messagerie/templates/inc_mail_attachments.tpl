{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<ul style="display: inline-block; padding: 0px;">
  {{foreach from=$attachments item=_attachment name=attachments}}
    <li style="list-style-type: none; margin-top: 5px; display: inline-block;{{if !$smarty.foreach.attachments.first}} margin-left: 10px;{{/if}}" class="attachment">
      <span style="cursor: pointer;" onclick="popFile('{{$_attachment->_class}}', '{{$_attachment->_id}}', 'CFile', '{{$_attachment->_file->_id}}', '0');">
        <span style="margin-right: 5px;">
          <i class="msgicon fa fa-2x fa-file"></i>
        </span>

        <span style="height: 25px;">
          <span style="vertical-align: 25%;">
            <a href="#">{{$_attachment->name}} ({{$_attachment->_file->_file_size}})</a>
          </span>
        </span>
      </span>
    </li>
  {{/foreach}}
</ul>