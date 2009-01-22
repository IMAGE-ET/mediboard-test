<script type="text/javascript">
function addAntecedent(rques, type, element) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editAntFrm'];
    if (oForm) {
      oForm.rques.value = rques;
      oForm.type.value = type;
      window.opener.onSubmitAnt(oForm);
      $(element).setStyle({cursor: 'default', opacity: 0.3}).onclick = null;
    }
  }
}

function addTraitement(rques, type, element) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editTrmtFrm'];
    if (oForm) {
      oForm.traitement.value = rques;
      window.opener.onSubmitTraitement(oForm);
      $(element).setStyle({cursor: 'default', opacity: 0.3}).onclick = null;
    }
  }
}

Main.add(function () {
  var tabsAntecedents = Control.Tabs.create('tab-antecedents', false);
  var tabsAntecedentsTypes = Control.Tabs.create('tab-antecedents-types', false);
});
</script>

<ul id="tab-antecedents" class="control_tabs">
  <li><a href="#antecedents">Antécédents</a></li>
  <li><a href="#traitements">Traitements</a></li>
</ul>
<hr class="control_tabs" />

{{* Nombre de colonnes *}}
{{assign var=numCols value=4}}
{{math equation="100/$numCols" assign=width format="%.1f"}}

<!-- Antécédents -->
<table id="antecedents" class="main" style="display: none;">
  <tr>
    <td style="width: 0.1%; vertical-align: top;">
      <ul id="tab-antecedents-types" class="control_tabs_vertical">
      {{foreach from=$aides_antecedent item=curr_aides key=curr_type}}
        <li><a href="#antecedents_{{$curr_type}}" style="white-space: nowrap;">{{tr}}CAntecedent.type.{{if $curr_type}}{{$curr_type}}{{/if}}{{/tr}}</a></li>
      {{/foreach}}
      </ul>
    </td>
    <td>
      {{foreach from=$aides_antecedent item=curr_aides key=curr_type}}
      <table class="main tbl" id="antecedents_{{$curr_type}}" style="display: none;">
        {{foreach from=$curr_aides item=_curr_aides key=appareil}}
        <tr>
          <th colspan="1000">{{if $appareil}}{{$appareil}}{{else}}Non spécifié{{/if}}</th>
        </tr>
        <tr>
        {{foreach from=$_curr_aides item=curr_aide name=aides}}
          {{assign var=i value=$smarty.foreach.aides.index}}
          <td class="text" style="cursor: pointer; width: {{$width}}%;" 
              title="{{$curr_aide->text|smarty:nodefaults|JSAttribute}}" 
              onclick="addAntecedent('{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$curr_type}}', this)">
            <button class="tick notext">{{$curr_aide->name}}</button>
            {{$curr_aide->name}}
          </td>
          {{if ($i % $numCols) == ($numCols-1) && !$smarty.foreach.aides.last}}</tr><tr>{{/if}}
        {{foreachelse}}
          <td>{{tr}}CAideSaisie.none{{/tr}}</td>
        {{/foreach}}
        </tr>
        {{/foreach}}
      </table>
      {{foreachelse}}
        {{tr}}CAideSaisie.none{{/tr}}
      {{/foreach}}
    </td>
  </tr>
</table>




{{assign var=numCols value=4}}
<!-- Traitements -->
<table class="main tbl" id="traitements" style="display: none;">
{{foreach from=$traitement->_aides.traitement item=curr_type key=curr_key}}
  <tr>
  {{assign var=n value=0}}
  {{foreach from=$curr_type item=curr_helper_for key=curr_helper_for_key}}
    {{foreach from=$curr_helper_for item=curr_helper key=curr_helper_key name=helpers}}
    {{assign var=i value=$smarty.foreach.helpers.index}}
    {{assign var=n value=$n+1}}
    <td class="text" style="cursor: pointer;" 
        title="{{$curr_helper_key|smarty:nodefaults|JSAttribute}}"
        onclick="addTraitement('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}', this)">
      <button class="tick notext">{{$curr_helper}}</button>
      {{$curr_helper}}
    </td>
    {{if ($i % $numCols) == ($numCols-1) && !$smarty.foreach.helpers.last}}</tr><tr>{{/if}}
    {{/foreach}}
  {{/foreach}}
  {{if $n == 0}}<td>{{tr}}CAideSaisie.none{{/tr}}</td>{{/if}}
  </tr>
{{/foreach}}
</table>