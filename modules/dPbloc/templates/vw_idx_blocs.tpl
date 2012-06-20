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

Main.add(function () {
  Control.Tabs.create('tabs-bloc', true);
});

</script>

<ul id="tabs-bloc" class="control_tabs">
  <li><a href="#blocs">{{tr}}CBlocOperatoire{{/tr}}</a></li>
  <li><a href="#salles">{{tr}}CSalle{{/tr}}</a></li>
  <li><button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button></li></li>
</ul>
<hr class="control_tabs" />

<div id="blocs" style="display: none;">
  <table class="main">
    <tr>
      <td class="halfPane">
        <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;bloc_id=0">{{tr}}CBlocOperatoire-title-create{{/tr}}</a>
        <table class="tbl">
          <tr>
            <th>{{tr}}CBlocOperatoire-nom{{/tr}}</th>
            <th>{{tr}}Salles{{/tr}}</th>
          </tr>
          {{foreach from=$blocs_list item=_bloc}}
          <tr {{if $_bloc->_id == $bloc->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;bloc_id={{$_bloc->_id}}">{{$_bloc}}</a></td>
            <td>
            	{{foreach from=$_bloc->_ref_salles item=_salle}}
            	   <div>{{$_salle}}</div>
            	{{foreachelse}}
  						<div class="empty">{{tr}}CSalle.none{{/tr}}</div>
            	{{/foreach}}
   				</td>
          </tr>
          {{/foreach}}
        </table>
      </td>
      <td class="halfPane">
        <form name="bloc-edit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_bloc_operatoire_aed" />
          <input type="hidden" name="del" value="0" />
          {{mb_key object=$bloc}}
          <input type="hidden" name="group_id" value="{{$g}}" />
          <table class="form">
            <tr>
              <th class="title {{if $bloc->_id}}modify{{/if}}" colspan="2">
              {{if $bloc->_id}}
                {{assign var=object value=$bloc}}
  				      {{mb_include module=system template=inc_object_idsante400}}
  				      {{mb_include module=system template=inc_object_history}}
  				      {{mb_include module=system template=inc_object_notes}}
                {{tr}}CBlocOperatoire-title-modify{{/tr}} "{{$bloc}}"
              {{else}}
                {{tr}}CBlocOperatoire-title-create{{/tr}}
              {{/if}}
              </th>
            </tr>
            <tr>
              <th>{{mb_label object=$bloc field="nom"}}</th>
              <td>{{mb_field object=$bloc field="nom"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$bloc field="days_locked"}}</th>
              <td>{{mb_field object=$bloc field="days_locked"}}</td>
            </tr>
            <tr>
              <td class="button" colspan="2">
                {{if $bloc->_id}}
                <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$bloc->nom|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
                {{else}}
                <button type="submit" class="new">{{tr}}Create{{/tr}}</button>
                {{/if}}
              </td>
            </tr>
          </table>
        </form>
      </td>
    </tr>
  </table>
</div>

<div id="salles" style="display: none;">
  <table class="main">
    <tr>
      <td class="halfPane">
        <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;salle_id=0">{{tr}}CSalle-title-create{{/tr}}</a>
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
</div>
