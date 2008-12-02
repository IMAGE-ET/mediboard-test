{{mb_include_script module="dPpatients" script="autocomplete"}}
{{mb_include_script module="mediusers" script="color_selector"}}

<script type="text/javascript">
Main.add(function () {
  initInseeFields("editFrm", "cp", "ville");
});

ColorSelector.init = function(){
  this.sForm  = "editFrm";
  this.sColor = "color";
  this.pop();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id=0" class="buttonnew">
        Créer une fonction
      </a>
      <table class="tbl">
      {{foreach from=$listGroups item=curr_group}}
        <tr>
          <th>Etablissement {{$curr_group->text}} &mdash; {{$curr_group->_ref_functions|@count}} fonction(s)</th>
          <th>Type</th>
          <th>Utilisateurs</th>
        </tr>
        {{foreach from=$curr_group->_ref_functions item=curr_function}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id={{$curr_function->_id}}">
              {{$curr_function->text}}
            </a>
          </td>
          <td>
            {{tr}}CFunctions.type.{{$curr_function->type}}{{/tr}}
          </td>
          <td style="background: #{{$curr_function->color}}">
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id={{$curr_function->_id}}">
              {{$curr_function->_ref_users|@count}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
    <form name="editFrm" action="?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_functions_aed" />
      <input type="hidden" name="function_id" value="{{$userfunction->function_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {{if $userfunction->function_id}}
            <a style="float:right;" href="#" onclick="view_log('CFunctions',{{$userfunction->function_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification de la fonction &lsquo;{{$userfunction->text}}&rsquo;
          {{else}}
            {{tr}}Création d'une fonction{{/tr}}
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="text"}}</th>
          <td>{{mb_field object=$userfunction field="text"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="soustitre"}}</th>
          <td>{{mb_field object=$userfunction field="soustitre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="compta_partagee"}}</th>
          <td>{{mb_field object=$userfunction field="compta_partagee"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="group_id"}}</th>
          <td>
            <select name="group_id" class="{{$userfunction->_props.group_id}}">
              <option value="">&mdash; {{tr}}CGroups.select{{/tr}}</option>
              {{foreach from=$listGroups item=curr_group}}
              <option value="{{$curr_group->group_id}}" {{if $curr_group->group_id == $userfunction->group_id}} selected="selected" {{/if}}>
                {{$curr_group->text}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="type"}}</th>
          <td>{{mb_field object=$userfunction field="type"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="color"}}</th>
          <td>
            <a href="#1" id="select_color" style="background: #{{$userfunction->color}};" onclick="ColorSelector.init()">cliquez ici</a>
            {{mb_field object=$userfunction field="color" hidden=1}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="adresse"}}</th>
          <td>{{mb_field object=$userfunction field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="cp"}}</th>
          <td>
            {{mb_field object=$userfunction field="cp"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="ville"}}</th>
          <td>
            {{mb_field object=$userfunction field="ville"}}
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="tel"}}</th>
          <td>{{mb_field object=$userfunction field="tel"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="fax"}}</th>
          <td>{{mb_field object=$userfunction field="fax"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
          {{if $userfunction->function_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fonction',objName:'{{$userfunction->text|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          {{else}}
            <button class="submit" name="btnFuseAction" type="submit">Créer</button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>