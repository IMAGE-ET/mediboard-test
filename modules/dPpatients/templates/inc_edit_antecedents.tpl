{{mb_script module=patients script=antecedent}}

<script type="text/javascript">
  afterAntecedent = function() {
    getForm("editAntFrm").up("div").up("div").down("button.change").click();
  }  
</script>

<form name="delAntecedent" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_antecedent_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="antecedent_id" value="" />
</form>

<form name="editAntFrm" action="?m=dPcabinet" method="post" onsubmit="onSubmitFormAjax(this, {onComplete: afterAntecedent})">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_antecedent_aed" />
  <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
  {{if $type}}
    <input type="hidden" name="type" value="{{$type}}" />
  {{/if}}
  
  <table class="form">
    <tr>
      <th class="title" colspan="4">
        Antécédents de type {{tr}}CAntecedent.type.{{$type}}{{/tr}}
      </th>
    </tr>
    <tr>
      {{if $app->user_prefs.showDatesAntecedents}}
        <th style="height: 1%">{{mb_label object=$antecedent field=date}}</th>
        <td>{{mb_field object=$antecedent field=date form=editAntFrm register=true}}</td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
      <td rowspan="{{$type|ternary:2:3}}" style="width: 60%">
        {{mb_field object=$antecedent field="rques" rows="4" form="editAntFrm"
          aidesaisie="filterWithDependFields: false, validateOnBlur: 0"}}
      </td>
      <td style="width: 40%; text-align: left; padding-left: 2em;" rowspan="{{$type|ternary:2:3}}">
        {{foreach from=$antecedents item=_antecedent}}
          <li {{if $_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
            {{if $_antecedent->_ref_first_log && $_antecedent->_ref_first_log->user_id == $app->user_id}}
              <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button"
                  onclick="var form = getForm('delAntecedent'); $V(form.antecedent_id, '{{$_antecedent->_id}}');
                    Antecedent.remove(form, afterAntecedent)">
                {{tr}}Delete{{/tr}}
              </button>
            {{/if}}   
            <strong>
              {{if !$type}} 
              {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
              {{/if}}
              {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
            </strong>
            {{if $_antecedent->date}}
              {{mb_value object=$_antecedent field=date}} : 
            {{/if}}
            {{$_antecedent->rques|nl2br}}
          </li>
        {{foreachelse}}
          <li class="empty">{{tr}}CAntecedent.unknown{{/tr}}</li>  
        {{/foreach}}
      </td>
    </tr>
    
    {{if !$type}}
      <tr>
        <th style="height: 100%">{{mb_label object=$antecedent field="type"}}</th>
        <td>{{mb_field object=$antecedent field="type" emptyLabel="None" alphabet="1" style="width: 9em;" onchange=""}}</td>
      </tr>
    {{/if}}
    <tr>
      <th>{{mb_label object=$antecedent field="appareil"}}</th>
      <td>{{mb_field object=$antecedent field="appareil" emptyLabel="None" alphabet="1" style="width: 9em;"}}</td>
    </tr>
        
    <tr>
      <td class="button" colspan="4">
        <button class="tick" type="button" onclick="this.form.onsubmit();">
          {{tr}}Add{{/tr}} l'antécédent
        </button>
      </td>
    </tr>
  </table>
</form>