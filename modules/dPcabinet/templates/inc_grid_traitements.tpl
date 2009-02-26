<script type="text/javascript">

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

</script>

<!-- Traitements -->
{{assign var=numCols value=4}}
{{math equation="100/$numCols" assign=width format="%.1f"}}

<table class="main tbl" id="traitements" style="display: none;">
{{foreach from=$traitement->_aides.traitement item=type key=curr_key}}
  <tr>
  {{assign var=n value=0}}
  {{foreach from=$type item=curr_helper_for key=curr_helper_for_key}}
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