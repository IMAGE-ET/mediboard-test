{{mb_script module=patients script=antecedent}}

<script type="text/javascript">
  editAntecedent = function(antecedent_id) {
    var url = new Url("dPpatients", "ajax_edit_antecedents");
    url.addParam("dialog", 1);
    url.addParam("patient_id", "{{$patient->_id}}");
    url.addParam("type", "{{$type}}");
    if (antecedent_id) {
      url.addParam("antecedent_id", antecedent_id);
    }
    url.redirect();
  }
</script>

<form name="delAntecedent" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_antecedent_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="antecedent_id" value="" />
  <input type="hidden" name="callback" value="editAntecedent" />
</form>

<form name="editAntFrm" action="?m=dPcabinet" method="post" onsubmit="onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_antecedent_aed" />
  <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
  <input type="hidden" name="callback" value="editAntecedent" />
  {{mb_key object=$antecedent}}
  {{if $type}}
    <input type="hidden" name="type" value="{{$type}}" />
  {{/if}}
  
  <table class="form">
    <tr>
      <th class="title {{if $antecedent->_id}}modify{{/if}}" colspan="4">
        {{if $antecedent->_id}}
          <button type="button" class="new" onclick="editAntecedent()" style="float: left;">
            Nouvel antécédent
          </button>
        {{/if}}
        {{if $antecedent->_id}}
          {{tr}}CAntecedent-title-modify{{/tr}}
        {{else}}
          {{tr}}CAntecedent-title-create{{/tr}}
        {{/if}}
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
      <td style="width: 40%; text-align: left; padding-left: 2em;" rowspan="{{$type|ternary:2:3}}" class="text">
        {{foreach from=$antecedents item=_antecedent}}
          {{assign var=first_log value=$_antecedent->_ref_first_log}}
          <li {{if $_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
            <div  {{if $antecedent->_id == $_antecedent->_id}}class="selected"{{/if}}>
              {{if $first_log && $first_log->user_id == $app->user_id}}
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
              {{if $first_log && $first_log->user_id == $app->user_id}}
                <a href="#1" onclick="editAntecedent('{{$_antecedent->_id}}')">
              {{/if}}
                {{$_antecedent->rques|nl2br}}
              {{if $first_log && $first_log->user_id == $app->user_id}}
                </a>
              {{/if}}
            </div>
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
        {{if $antecedent->_id}}
          <button class="save" type="button" onclick="this.form.onsubmit();">
            {{tr}}Save{{/tr}}
          </button>
        {{else}}
          <button class="tick" type="button" onclick="this.form.onsubmit();">
            {{tr}}Add{{/tr}} l'antécédent
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>