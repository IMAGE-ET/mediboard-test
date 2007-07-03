<strong>Addictions significatifs</strong>
<ul>
{{if $consult_anesth->_ref_addictions}}
  {{foreach from=$consult_anesth->_ref_types_addiction key=curr_type item=list_addiction}}
  {{if $list_addiction|@count}}
  <li>
    {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
    {{foreach from=$list_addiction item=curr_addiction}}
    <ul>
      <li>
        <form name="delAddictionFrm" action="?m=dPcabinet" method="post">

        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_addiction_aed" />
        {{mb_field object=$curr_addiction field="addiction_id" hidden=1 prop=""}}
        <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'cette addiction',ajax:1,target:'systemMsg'},{onComplete:reloadAntecedentsAnesth})">
        {{tr}}Delete{{/tr}}
        </button>
        <em>{{$curr_addiction->addiction}}</em>
       </form>
      </li>
    </ul>
    {{/foreach}}
  </li>
  {{/if}}
  {{/foreach}}
{{else}}
  <li><em>Pas d'addictions</em></li>
{{/if}}
</ul>