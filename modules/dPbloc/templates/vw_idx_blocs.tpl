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

{{assign var=use_poste value=$conf.dPplanningOp.COperation.use_poste}}

<ul id="tabs-bloc" class="control_tabs">
  <li><a href="#blocs">{{tr}}CBlocOperatoire{{/tr}}</a></li>
  <li><a href="#salles">{{tr}}CSalle{{/tr}}</a></li>
  {{if $use_poste}}
    <li><a href="#postes">{{tr}}CPosteSSPI{{/tr}}</a></li>
  {{/if}}
  <li><button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button></li></li>
</ul>
<hr class="control_tabs" />

<div id="blocs" style="display: none;">
  <table class="main">
    <tr>
      <td class="halfPane">
        <a class="button new" href="?m={{$m}}&tab={{$tab}}&bloc_id=0">{{tr}}CBlocOperatoire-title-create{{/tr}}</a>
        <table class="tbl">
          <tr>
            <th>{{tr}}CBlocOperatoire-nom{{/tr}}</th>
            {{if $use_poste}}
              <th>{{tr}}CBlocOperatoire-poste_sspi_id{{/tr}}</th>
            {{/if}}
            <th>{{tr}}CBlocOperatoire-back-salles{{/tr}}</th>
          </tr>
          {{foreach from=$blocs_list item=_bloc}}
          <tr {{if $_bloc->_id == $bloc->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&tab={{$tab}}&bloc_id={{$_bloc->_id}}">{{$_bloc}}</a></td>
            {{if $use_poste}}
              <td>{{$_bloc->_ref_poste}}</td>
            {{/if}}
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
              {{mb_include module=system template=inc_form_table_header object=$bloc}}
            </tr>
            <tr>
              <th>{{mb_label object=$bloc field="nom"}}</th>
              <td>{{mb_field object=$bloc field="nom"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$bloc field="days_locked"}}</th>
              <td>{{mb_field object=$bloc field="days_locked"}}</td>
            </tr>
            {{if $use_poste}}
              <tr>
                <th>{{mb_label object=$bloc field="poste_sspi_id"}}</th>
                <td>
                  <input type="hidden" name="poste_sspi_id" value="{{$bloc->poste_sspi_id}}"/>
                  <input type="text" name="_poste_sspi_id_autocomplete" value="{{$bloc->_ref_poste}}"/>
                  <script type="text/javascript">
                    Main.add(function() {
                      var form=getForm("bloc-edit");
                      var url = new Url("system", "ajax_seek_autocomplete");
                      url.addParam("object_class", "CPosteSSPI");
                      url.addParam('show_view', true);
                      url.addParam("input_field", "_poste_sspi_id_autocomplete");
                      url.autoComplete(form.elements._poste_sspi_id_autocomplete, null, {
                        minChars: 2,
                        method: "get",
                        select: "view",
                        dropdown: true,
                        afterUpdateElement: function(field,selected) {
                          var guid = selected.getAttribute('id');
                          if (guid) {
                            $V(field.form['poste_sspi_id'], guid.split('-')[2]);
                          }
                        },
                        callback:  function(input, queryString) {
                          queryString += "&ljoin[bloc_operatoire]=bloc_operatoire.poste_sspi_id  = poste_sspi.poste_sspi_id";
                          queryString += "&whereComplex[]=(bloc_operatoire.bloc_operatoire_id IS NULL OR bloc_operatoire.bloc_operatoire_id = "+{{$bloc->_id}}+")";
                          return queryString;
                        }
                      });
                    });
                  </script>
                  <button type="button" class="cancel notext"
                    onclick="$V(this.form.poste_sspi_id, ''), $V(this.form._poste_sspi_id_autocomplete, '')"></button>
                </td>
              </tr>
            {{/if}}
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
        <a class="button new" href="?m={{$m}}&tab={{$tab}}&salle_id=0">{{tr}}CSalle-title-create{{/tr}}</a>
        <table class="tbl">
          {{foreach from=$blocs_list item=_bloc}}
            <tr>
              <th class="">{{$_bloc->nom}}</th>
            </tr>
            {{foreach from=$_bloc->_ref_salles item=_salle}}
              <tr {{if $_salle->_id == $salle->_id}}class="selected"{{/if}}>
                <td><a href="?m={{$m}}&tab={{$tab}}&salle_id={{$_salle->_id}}">{{$_salle}}</a></td>
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
              {{mb_include module=system template=inc_form_table_header object=$salle}}
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

{{if $use_poste}}
  <div id="postes" style="display: none;">
     <table class="main">
      <tr>
        <td class="halfPane">
          <a class="button new" href="?m={{$m}}&tab={{$tab}}&poste_sspi_id=0">{{tr}}CPosteSSPI-title-create{{/tr}}</a>
          <table class="tbl">
            <tr>
              <th>{{tr}}CPosteSSPI-nom{{/tr}}</th>
              <th>{{tr}}CPosteSSPI-back-bloc{{/tr}}</th>
            </tr>
            {{foreach from=$postes_list item=_poste}}
            <tr {{if $_poste->_id == $poste->_id}}class="selected"{{/if}}>
              <td><a href="?m={{$m}}&tab={{$tab}}&poste_sspi_id={{$_poste->_id}}">{{$_poste}}</a></td>
              <td>
                {{if $_poste->_ref_bloc->_id}}
                  <div>{{$_poste->_ref_bloc}}</div>
                {{else}}
                  <div class="empty">{{tr}}CBlocOperatoire.none{{/tr}}</div>
                {{/if}}
             </td>
            </tr>
            {{foreachelse}}
              <tr>
                <td colspan="2" class="empty">
                  {{tr}}CPosteSSPI.none{{/tr}}
                </td>
              </tr>
            {{/foreach}}
          </table>
        </td>
        <td class="halfPane">
          <form name="poste-edit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_poste_sspi_aed" />
            <input type="hidden" name="del" value="0" />
            {{mb_key object=$poste}}
            <input type="hidden" name="group_id" value="{{$g}}" />
            <table class="form">
              <tr>
                {{mb_include module=system template=inc_form_table_header object=$poste}}
              </tr>
              <tr>
                <th>{{mb_label object=$poste field="nom"}}</th>
                <td>{{mb_field object=$poste field="nom"}}</td>
              </tr>
              <tr>
                <td class="button" colspan="2">
                  {{if $poste->_id}}
                    <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
                    <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$poste->nom|smarty:nodefaults|JSAttribute}}'})">
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
{{/if}}