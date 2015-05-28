{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=patients script=correspondant}}
{{mb_script module=patients script=medecin}}

<script>
  Main.add(function() {
    Control.Tabs.create("tabs_correspondants", true);

    {{if $medecin_id}}
      Medecin.editMedecin('{{$medecin_id}}', refreshPageMedecin.curry('0'));
    {{else}}
      refreshPageMedecin();
    {{/if}}

    {{if $correspondant_id}}
      Correspondant.edit('{{$correspondant_id}}', null, refreshPageCorrespondant);
    {{else}}
      refreshPageCorrespondant();
    {{/if}}
  });

  refreshPageMedecin = function(page) {
    var oform = getForm('find_medecin');
    if (oform) {
      $V(oform.start_med, page);
      oform.onsubmit();
    }
  }

  refreshPageCorrespondant = function(page) {
    var oform = getForm('find_correspondant');
    if (oform) {
      $V(oform.start_corres, page);
      oform.onsubmit();
    }
  }

  onMergeComplete = function() {
    getForm("find_medecin").onsubmit();
  }
</script>


<ul id="tabs_correspondants" class="control_tabs">
  <li>
    <a href="#medicaux">{{tr}}CCorrespondant-tab-medecin{{/tr}}</a>
  </li>
  {{if !$dialog}}
    <li>
      <a href="#autres">{{tr}}CCorrespondant-tab-others{{/tr}}</a>
    </li>
  {{/if}}
</ul>

<div id="medicaux">
  <form name="find_medecin" action="?" method="get" onsubmit="return onSubmitFormAjax(this, null, 'medicaux_result')">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="a" value="vw_medecins" />
    <input type="hidden" name="dialog" value="{{$dialog}}" />
    <input type="hidden" name="annuaire" value="0" />
    <input type="hidden" name="start_med" value="{{$start_med}}" />
    <input type="hidden" name="step_med" value="{{$step_med}}" />

    <table class="main form">
      <tr>
        <th class="title" colspan="6">{{tr}}CMedecin.search{{/tr}}</th>
      </tr>

      <tr>
        <th>{{mb_label object=$medecin field=nom}}</th>
        <td>
          {{mb_field object=$medecin field=nom prop=str tabindex=1
          onchange="\$V(this.form.start_med, 0)" style="width: 13em;"}}
        </td>
        <th>{{mb_label object=$medecin field=cp}}</th>
        <td>
          {{mb_field object=$medecin field=cp prop=str tabindex=3
          onchange="\$V(this.form.start_med, 0)" style="width: 13em;"}}
        </td>
        <th>{{mb_label object=$medecin field=type}}</th>
        <td>
          {{mb_field object=$medecin field=type emptyLabel="All" tabindex=5
          onchange="\$V(this.form.start_med, 0)" style="width: 13em;"}}
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$medecin field=prenom}}</th>
        <td>
          {{mb_field object=$medecin field=prenom prop=str tabindex=2
          onchange="\$V(this.form.start_med, 0)" style="width: 13em;"}}
        </td>
        <th>{{mb_label object=$medecin field=ville}}</th>
        <td>
          {{mb_field object=$medecin field=ville prop=str tabindex=4
          onchange="\$V(this.form.start_med, 0)" style="width: 13em;"}}
        </td>
        <th>{{mb_label object=$medecin field=disciplines}}</th>
        <td>
          {{mb_field object=$medecin field=disciplines prop=str tabindex=6
          onchange="\$V(this.form.start_med, 0)" style="width: 13em;"}}
        </td>
      </tr>

      {{if $is_admin}}
      <tr>
        <th></th>
        <td></td>
        <th></th>
        <td></td>
        <th>{{mb_label object=$medecin field=function_id}}</th>
        <td>
          <select name="function_id" style="width: 13em;" onchange="\$V(this.form.start_med, 0)">
            <option value="">&mdash; Toutes</option>
            {{foreach from=$listFunctions item=_function}}
              <option value="{{$_function->_id}}" {{if $_function->_id == $medecin->function_id}}selected="selected"{{/if}}>
                {{$_function}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      {{/if}}

      <tr>
        <td class="button" colspan="6">
          {{if !$dialog}}
            <button class="search" type="submit" onclick="$V(this.form.annuaire, 0);">{{tr}}Search{{/tr}}</button>
          {{else}}
              <button id="vw_medecins_button_dialog_search" class="search" type="submit" onclick="formVisible=false;">{{tr}}Search{{/tr}}</button>
          {{/if}}
          {{if $conf.dPpatients.CPatient.function_distinct}}
            <button class="search" type="button" onclick="$V(this.form.annuaire, 1); this.form.onsubmit()">{{tr}}Search{{/tr}} dans l'annuaire</button>
          {{/if}}
          <button class="new" type="button" onclick="Medecin.editMedecin('0', refreshPageMedecin.curry('0'));">{{tr}}Create{{/tr}}</button>

          <a class="button download" href="?m=patients&amp;raw=export_medecins_csv" target="_blank">{{tr}}CMedecin-action-Export with e-mail address{{/tr}}</a>
        </td>
      </tr>
    </table>
  </form>
  <hr/>
  <div id="medicaux_result"></div>
</div>

{{if !$dialog}}
  <div id="autres">
    <form name="find_correspondant" action="?" method="get" onsubmit="return onSubmitFormAjax(this, null, 'correspondants_result')">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="a" value="ajax_list_correspondants_modele" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
      <input type="hidden" name="start_corres" value="{{$start_corres}}"/>
      <input type="hidden" name="step_corres" value="{{$step_corres}}"/>

      <table class="form">
        <tr>
          <th class="title" colspan="6">{{tr}}CCorrespondantPatient.search{{/tr}} (Modèles)</th>
        </tr>

        <tr>
          <th>{{mb_label object=$correspondant field=nom}}</th>
          <td>
            {{mb_field object=$correspondant field=nom prop=str tabindex=1
            onchange="\$V(this.form.start_corres, 0)" style="width: 13em;"}}
          </td>
          <th>{{mb_label object=$correspondant field=cp}}</th>
          <td>
            {{mb_field object=$correspondant field=cp prop=str tabindex=4
            onchange="\$V(this.form.start_corres, 0)" style="width: 13em;"}}
          </td>
          <th>{{mb_label object=$correspondant field=relation}}</th>
          <td>
            {{mb_field object=$correspondant field=relation emptyLabel="All" tabindex=6
            onchange="\$V(this.form.start_corres, 0)" style="width: 13em;"}}
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$correspondant field=prenom}}</th>
          <td>
            {{mb_field object=$correspondant field=prenom prop=str tabindex=2
            onchange="\$V(this.form.start_corres, 0)" style="width: 13em;"}}
          </td>
          <th>{{mb_label object=$correspondant field=ville}}</th>
          <td>
            {{mb_field object=$correspondant field=ville prop=str tabindex=5
            onchange="\$V(this.form.start_corres, 0)" style="width: 13em;"}}
          </td>
          {{if $is_admin}}
            <th>{{mb_label object=$correspondant field=function_id}}</th>
            <td colspan="5">
              <select name="function_id" style="width: 13em;" onchange="\$V(this.form.start_corres, 0)" tabindex="7">
                <option value="">&mdash; Toutes</option>
                {{foreach from=$listFunctions item=_function}}
                  <option value="{{$_function->_id}}" {{if $_function->_id == $correspondant->function_id}}selected="selected"{{/if}}>
                    {{$_function}}
                  </option>
                {{/foreach}}
              </select>
            </td>
          {{else}}
            <th></th>
            <td></td>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$correspondant field=surnom}}</th>
          <td>
            {{mb_field object=$correspondant field=surnom prop=str tabindex=3
            onchange="\$V(this.form.start_corres, 0)" style="width: 13em;"}}
          </td>
          <th></th>
          <td></td>
          <th></th>
          <td></td>
        </tr>
        <tr>
          <td class="button" colspan="6">
            <button class="search" type="submit">{{tr}}Search{{/tr}}</button>
            <button class="new" type="button" onclick="Correspondant.edit('0', null, refreshPageCorrespondant)">{{tr}}Create{{/tr}}</button>
          </td>
        </tr>
      </table>
    </form>
    <hr/>
    <div id="correspondants_result"></div>
  </div>
{{/if}}
