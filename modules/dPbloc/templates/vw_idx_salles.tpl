{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function popupImport() {
  var url = new Url("dPbloc", "salles_import_csv");
  url.popup(800, 600, "Import des Salles");
  return false;
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;salle_id=0">{{tr}}CSalle-title-create{{/tr}}</a>
      <button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button></li>
      <table class="tbl">
        {{foreach from=$blocs_list item=_bloc}}
          <tr>
            <th class="">{{$_bloc->nom}}</th>
          </tr>
          {{foreach from=$_bloc->_ref_salles item=_salle}}
            <tr {{if $_salle->_id == $salle->_id}}class="selected"{{/if}}>
              <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;salle_id={{$_salle->_id}}">{{$_salle}}</a></td>
            </tr>
          {{foreachelse}}
            <tr><td class="empty">{{tr}}CSalle.none{{/tr}}</td></tr>
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
    
    <td class="halfPane">
      <form name="salle" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_salle_aed" />
        <input type="hidden" name="salle_id" value="{{$salle->_id}}" />
        <input type="hidden" name="del" value="0" />
    
        <table class="form">
    
        <tr>
          <th class="title {{if $salle->_id}} modify {{/if}}" colspan="2">
          {{if $salle->_id}}
			      {{assign var=object value=$salle}}
			      {{mb_include module=system template=inc_object_idsante400}}
			      {{mb_include module=system template=inc_object_history}}
			      {{mb_include module=system template=inc_object_notes}}
            {{tr}}CSalle-title-modify{{/tr}} '{{$salle}}'
          {{else}}
            {{tr}}CSalle-title-create{{/tr}}
          {{/if}}
          </th>
        </tr>
    
        <tr>
          <th>{{mb_label object=$salle field="bloc_id"}}</th>
          <td>{{mb_field object=$salle field="bloc_id" options=$blocs_list}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$salle field="nom"}}</th>
          <td>{{mb_field object=$salle field="nom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$salle field="stats"}}</th>
          <td>{{mb_field object=$salle field="stats"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$salle field="dh"}}</th>
          <td>{{mb_field object=$salle field="dh"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if $salle->salle_id}}
            <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'la salle',objName: $V(this.form.nom)})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button type="submit" class="new">
              {{tr}}Create{{/tr}}
            </button>
            {{/if}}
          </td>
        </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
