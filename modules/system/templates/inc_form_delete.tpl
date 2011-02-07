{{* $Id: CMbObject_view.tpl 7128 2009-10-26 17:25:30Z rhum1 $ *}}

{{*
  * @package Mediboard
  * @subpackage system
  * @version $Revision: 7128 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}
  
<form name="del{{$object->_guid}}" action="" method="post">
  {{mb_class object=$object}}
  {{mb_key object=$object}}
  <input type="hidden" name="del" value="1" />

  <button class="cancel notext" type="button" onclick="confirmDeletion(this.form, {
	    ajax:1, 
      typeName:&quot;{{tr}}{{$object->_class_name}}.one{{/tr}}&quot;,
      objName:&quot;{{$object->_view|smarty:nodefaults|JSAttribute}}&quot;
    })">
  </button>
</form>