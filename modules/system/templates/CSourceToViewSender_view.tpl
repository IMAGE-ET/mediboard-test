{{* $Id: CMbObject_view.tpl 7973 2010-02-02 11:11:23Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7973 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=CMbObject_view}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="@class" value="{{$object->_class_name}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$object}}
  
  <table class="tbl tooltip">
    <tr>
      <td class="button">
        {{if $can->edit}}
        <button class="trash" type="button" onclick="SourceToViewSender.confirmDeletion(this.form)">
          {{tr}}Delete{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>