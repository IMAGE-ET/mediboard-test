<script type="text/javascript">
function addAddiction(rques, type) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editAddictFrm'];
    if (oForm) {
      oForm.addiction.value = rques;
      oForm.type.value = type;
      window.opener.onSubmitAddiction(oForm);
      $(type+'-'+rques+'-button').setOpacity(0.3);
      $(type+'-'+rques+'-label').setOpacity(0.3);
      $(type+'-'+rques+'-button').onclick = null;
      $(type+'-'+rques+'-label').onclick = null;
    }
  }
}

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
</script>

{{* Nombre de colonnes *}}
{{assign var=numCols value=4}}

<table class="main tbl">

  <!-- Addictions -->
  <tr>
    <th colspan="{{$numCols*2}}" class="title">Addictions</th>
  </tr>
  {{foreach from=$addiction->_aides.addiction item=curr_type key=curr_key}}
    {{if $curr_key != "no_enum"}}
    {{if $curr_type && $curr_key}}
    <tr>
      <th colspan="{{$numCols*2}}">{{$addiction->_enumsTrans.type.$curr_key}}</th>
    </tr>
    {{/if}}
    <tr>
    {{foreach from=$curr_type item=curr_helper_for key=curr_helper_for_key}}
      {{foreach from=$curr_helper_for item=curr_helper key=curr_helper_key name=helpers}}
      {{assign var=i value=$smarty.foreach.helpers.iteration}}
      <td><button id="{{$curr_key}}-{{$curr_helper_key}}-button" class="tick notext" onclick="addAddiction('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')"></button></td>
      <td {{if $i==$curr_helper_for|@count}}colspan="{{$numCols*2}}"{{/if}} class="text">
        <label id="{{$curr_key}}-{{$curr_helper_key}}-label" onclick="addAddiction('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')">{{$curr_helper}}</label>
      </td>
      {{if $i % $numCols == 0}}</tr><tr>{{/if}}
      {{/foreach}}
    {{/foreach}}
    </tr>
    {{/if}}
  {{/foreach}}
  
  
  <!-- Antécédents -->
  <tr>
    <th colspan="{{$numCols*2}}" class="title">Antécédents</th>
  </tr>
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
      <td><button id="{{$curr_key}}-{{$curr_helper_key}}-button" class="tick notext" onclick="addAntecedent('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')"></button></td>
      <td {{if $i==$curr_helper_for|@count}}colspan="{{$numCols*2}}"{{/if}} class="text">
        <label id="{{$curr_key}}-{{$curr_helper_key}}-label" onclick="addAntecedent('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')">{{$curr_helper}}</label>
      </td>
      {{if $i % $numCols == 0}}</tr><tr>{{/if}}
      {{/foreach}}
    {{/foreach}}
    </tr>
    {{/if}}
  {{/foreach}}
  
  
  <!-- Traitements -->
  <tr>
    <th colspan="{{$numCols*2}}" class="title">Traitements</th>
  </tr>
  {{foreach from=$traitement->_aides.traitement item=curr_type key=curr_key}}
    <tr>
    {{foreach from=$curr_type item=curr_helper_for key=curr_helper_for_key}}
      {{foreach from=$curr_helper_for item=curr_helper key=curr_helper_key name=helpers}}
      {{assign var=i value=$smarty.foreach.helpers.iteration}}
      <td><button id="{{$curr_key}}-{{$curr_helper_key}}-button" class="tick notext" onclick="addTraitement('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')"></button></td>
      <td {{if $i==$curr_helper_for|@count}}colspan="{{$numCols*2}}"{{/if}} class="text">
        <label id="{{$curr_key}}-{{$curr_helper_key}}-label" onclick="addTraitement('{{$curr_helper_key|smarty:nodefaults|JSAttribute}}', '{{$curr_key|smarty:nodefaults|JSAttribute}}')">{{$curr_helper}}</label>
      </td>
      {{if $i % $numCols == 0}}</tr><tr>{{/if}}
      {{/foreach}}
    {{/foreach}}
    </tr>
  {{/foreach}}
</table>