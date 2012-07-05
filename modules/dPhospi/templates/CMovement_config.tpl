{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
removeMovementDuplicates = function(){
  var form = getForm("remove-movement-duplicates-form");
  var url = new Url;
  url.setModuleDosql("dPhospi", "do_remove_movement_duplicates");
  url.addFormData(form);
  url.requestUpdate("remove-movement-duplicates", {method: "post"}); 
}
</script>

<form name="editConfig-CMovement" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    {{mb_include module=system template=inc_config_str class=CMovement var=tag}}
    
    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<form name="remove-movement-duplicates-form" action="?" method="post" onsubmit="return false">
  <table class="main tbl" style="table-layout: fixed;">
    <tr>
      <th class="title" colspan="2">Actions</th>
    </tr>
    <tr>
      <td>
        <button class="change" onclick="removeMovementDuplicates()">Supprimer les mouvements en doublon</button>
        
        <table class="main form">
          <tr>
            <th><label for="original_trigger_code">Code</label></th>
            <td><input type="text" name="original_trigger_code" value="A01" /></td>
          </tr>
          <tr>
            <th><label for="count">Nombre</label></th>
            <td><input type="text" name="count" value="10" /></td>
          </tr>
          <tr>
            <th><label for="do_it">Traiter</label></th>
            <td><input type="checkbox" name="do_it" value="1" /></td>
          </tr>
          <tr>
            <th><label for="auto">Automatique</label></th>
            <td><input type="checkbox" name="auto" value="1" /></td>
          </tr>
        </table>
      </td>
      <td id="remove-movement-duplicates"></td>
    </tr>
  </table>
</form>