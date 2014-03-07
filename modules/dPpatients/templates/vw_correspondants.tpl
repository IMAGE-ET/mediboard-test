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
    refreshPageMedecin();

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
  };

  refreshPageCorrespondant = function(page) {
    var oform = getForm('find_correspondant');
    if (oform) {
      $V(oform.start_corres, page);
      oform.onsubmit();
    }
  };
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

<hr class="control_tabs" />

<div id="medicaux">
  <form name="find_medecin" action="?" method="get" onsubmit="return onSubmitFormAjax(this, null, 'medicaux_result')">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="a" value="vw_medecins" />
    <input type="hidden" name="dialog" value="{{$dialog}}" />
    <input type="hidden" name="start_med" value="{{$start_med}}"/>
    <input type="hidden" name="step_med" value="{{$step_med}}"/>

    <table class="form">
      <tr>
        <th class="title" colspan="6">Recherche d'un correspondant médical</th>
      </tr>

      <tr>
        <th>{{mb_label object=$medecin field=nom}}</th>
        <td>{{mb_field object=$medecin field=nom prop=str onchange="\$V(this.form.start, 0)"}}</td>
        <th>{{mb_label object=$medecin field=cp}}</th>
        <td>{{mb_field object=$medecin field=cp prop=str placeholder="code postal" onchange="\$V(this.form.start, 0)"}}</td>
        <th>{{mb_label object=$medecin field=type}}</th>
        <td>{{mb_field object=$medecin field=type emptyLabel="All" onchange="\$V(this.form.start, 0)"}}</td>
      </tr>

      <tr>
        <th>{{mb_label object=$medecin field=prenom}}</th>
        <td>{{mb_field object=$medecin field=prenom prop=str onchange="\$V(this.form.start, 0)"}}</td>
        <th>{{mb_label object=$medecin field=ville}}</th>
        <td>{{mb_field object=$medecin field=ville prop=str placeholder=ville onchange="\$V(this.form.start, 0)"}}</td>
        <th>{{mb_label object=$medecin field=disciplines}}</th>
        <td>{{mb_field object=$medecin field=disciplines prop=str placeholder=discipline onchange="\$V(this.form.start, 0)"}}</td>
      </tr>

      <tr>
        <td class="button" colspan="6">
          {{if !$dialog}}
            <button class="search" type="submit">{{tr}}Search{{/tr}}</button>
          {{else}}
              <button id="vw_medecins_button_dialog_search" class="search" type="submit" onclick="formVisible=false;">{{tr}}Search{{/tr}}</button>
          {{/if}}
          <button class="new" type="button" onclick="Medecin.editMedecin('0', refreshPageMedecin);">{{tr}}Create{{/tr}}</button>
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
          <th class="title" colspan="6">{{tr}}CCorrespondantPatient.search{{/tr}}</th>
        </tr>

        <tr>
          <th>{{mb_label object=$correspondant field=nom}}</th>
          <td>{{mb_field object=$correspondant field=nom prop=str onchange="\$V(this.form.start, 0)"}}</td>
          <th>{{mb_label object=$correspondant field=cp}}</th>
          <td>{{mb_field object=$correspondant field=cp prop=str placeholder="code postal" onchange="\$V(this.form.start, 0)"}}</td>
          <th>{{mb_label object=$correspondant field=relation}}</th>
          <td>{{mb_field object=$correspondant field=relation emptyLabel="All" onchange="\$V(this.form.start, 0)"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$correspondant field=surnom}}</th>
          <td>{{mb_field object=$correspondant field=surnom prop=str onchange="\$V(this.form.start, 0)"}}</td>
          <th>{{mb_label object=$correspondant field=ville}}</th>
          <td>{{mb_field object=$correspondant field=ville prop=str placeholder=ville onchange="\$V(this.form.start, 0)"}}</td>
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
