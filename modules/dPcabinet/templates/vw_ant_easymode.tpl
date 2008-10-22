<script type="text/javascript">
function addAntecedent(rques, type) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editAntFrm'];
    if (oForm) {
      oForm.rques.value = rques;
      oForm.type.value = type;
      window.opener.onSubmitAnt(oForm);
      $(type+'-'+rques+'-button').setOpacity(0.3);
      $(type+'-'+rques+'-label').setOpacity(0.3);
      $(type+'-'+rques+'-button').onclick = null;
      $(type+'-'+rques+'-label').onclick = null;
    }
  }
}

function addTraitement(rques, type) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editTrmtFrm'];
    if (oForm) {
      oForm.traitement.value = rques;
      window.opener.onSubmitTraitement(oForm);
      $(type+'-'+rques+'-button').setOpacity(0.3);
      $(type+'-'+rques+'-label').setOpacity(0.3);
      $(type+'-'+rques+'-button').onclick = null;
      $(type+'-'+rques+'-label').onclick = null;
    }
  }
}

Main.add(function () {
  var tabsAntecedents = Control.Tabs.create('tab-antecedents', false);
});
</script>

{{* Nombre de colonnes *}}
{{assign var=numCols value=4}}

<ul id="tab-antecedents" class="control_tabs">
  <li><a href="#antecedents">Antécédents</a></li>
  <li><a href="#traitements">Traitements</a></li>
</ul>
<hr class="control_tabs" />

<table class="main tbl">
  <!-- Antécédents -->
  <tbody id="antecedents" style="display: none;">
  {{foreach from=$antecedent->_aides.rques item=curr_type key=curr_key}}
    {{if $curr_key != "no_enum"}}
    {{if $curr_type && $curr_key}}
    <tr>
      <th colspan="{{$numCols*2}}">{{$antecedent->_enumsTrans.type.$curr_key}}</th>
    </tr>
    {{/if}}
    <tr>
    {{foreach from=$curr_type item=curr_helper_for key=curr_helper_for_key}}
      {{foreach from=$curr_helper_for item=curr_helper key=curr_helper_key name=helpers}}
      {{assign var=i value=$smarty.foreach.helpers.iteration}}
      <td style="width: 1%;"><button id="{{$curr_key}}-{{$curr_helper_key}}-button" class="tick notext" onclick="addAntecedent('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')"></button></td>
      <td class="text" {{if $i==$curr_helper_for|@count}}colspan="{{math equation="2 * (c - (i % c)) + 1" c=$numCols i=$i}}"{{/if}}>
        <label title="{{$curr_helper_key}}" id="{{$curr_key}}-{{$curr_helper_key}}-label" onclick="addAntecedent('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')">
          {{$curr_helper}}
        </label>
      </td>
      {{if ((($i % $numCols) == 0) && $i != 1) || $i == count($curr_helper_for)}}</tr><tr>{{/if}}
      {{/foreach}}
    {{/foreach}}
    </tr>
    {{/if}}
  {{/foreach}}
  </tbody>
  
  
  <!-- Traitements -->
  <tbody id="traitements" style="display: none;">
  {{foreach from=$traitement->_aides.traitement item=curr_type key=curr_key}}
    <tr>
    {{foreach from=$curr_type item=curr_helper_for key=curr_helper_for_key}}
      {{foreach from=$curr_helper_for item=curr_helper key=curr_helper_key name=helpers}}
      {{assign var=i value=$smarty.foreach.helpers.iteration}}
      <td style="width: 1%;"><button id="{{$curr_key}}-{{$curr_helper_key}}-button" class="tick notext" onclick="addTraitement('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')"></button></td>
      <td class="text" {{if $i==$curr_helper_for|@count}}colspan="{{math equation="2 * (c - (i % c)) + 1" c=$numCols i=$i}}"{{/if}}>
        <label title="{{$curr_helper_key}}" id="{{$curr_key}}-{{$curr_helper_key}}-label" onclick="addTraitement('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')">
          {{$curr_helper}}
        </label>
      </td>
      {{if ((($i % $numCols) == 0) && $i != 1)}}</tr><tr>{{/if}}
      {{/foreach}}
    {{/foreach}}
    </tr>
  {{/foreach}}
  </tbody>
</table>