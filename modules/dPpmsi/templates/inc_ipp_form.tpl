{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  prepareForm(document.forms.editIPP);
});
</script>

<form name="editIPP" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitIPPForm()">
  <input type="hidden" name="dosql" value="do_idsante400_aed" />
  <input type="hidden" name="m" value="dPsante400" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ajax" value="1" />
  <input type="hidden" name="id_sante400_id" value="{{$patient->_ref_IPP->_id}}" />
  
  <table class="form" style="table-layout:fixed">
    <tr>
      <th class="category" colspan="4">
        Identifiant externe Patient 
        <script type="text/javascript">
          PatHprimSelector.init = function(){
            this.sForm      = "editIPP";
            this.sId        = "id400";
            this.sPatNom    = "{{$patient->nom}}";
            this.sPatPrenom = "{{$patient->prenom}}";
            this.pop();
          };
          PatHprimSelector.doSet = function(){
            var oForm = document[PatHprimSelector.sForm];
            $V(oForm[PatHprimSelector.sId], PatHprimSelector.prepared.id);
            ExtRefManager.submitIPPForm();
          }
        </script>
      </th>
    </tr>
  
    <tr>
      <th>
        <label for="id400" title="Saisir l'identifiant du patient">IPP</label>
      </th>
      <td>
        <input type="text" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" size="8" />
        <input type="hidden" class="notNull" name="tag" value="{{$patient->_ref_IPP->tag}}" />
        <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
        <input type="hidden" class="notNull" name="object_class" value="CPatient" />
        <input type="hidden" name="last_update" value="{{$patient->_ref_IPP->last_update}}" />
      </td>
      <td class="button">
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
      </td>
      <td class="button">
        {{if $hprim21installed}}
        <button class="search" type="button" onclick="PatHprimSelector.init()">{{tr}}Search{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>