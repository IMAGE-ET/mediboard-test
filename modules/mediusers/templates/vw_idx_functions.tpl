<script type="text/javascript">

function popColor() {
  var url = new Url;
  url.setModuleAction("mediusers", "color_selector");
  url.addParam("callback", "setColor");
  url.popup(320, 250, "color");
}

function setColor(color) {
  var f = document.editFrm;
  if (color) {
    f.color.value = color;
  }
  document.getElementById('test').style.background = '#' + f.color.value;
  f.color.onchange();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;function_id=0" class="buttonnew">
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
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;function_id={{$curr_function->function_id}}">
              {{$curr_function->text}}
            </a>
          </td>
          <td>
            {{$curr_function->type}}
          </td>
          <td style="background: #{{$curr_function->color}}">
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;function_id={{$curr_function->function_id}}">
              {{$curr_function->_ref_users|@count}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
    <form name="editFrm" action="./index.php?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_functions_aed" />
      <input type="hidden" name="function_id" value="{{$userfunction->function_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {{if $userfunction->function_id}}
            <a style="float:right;" href="javascript:view_log('CFunctions',{{$userfunction->function_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification de la fonction &lsquo;{{$userfunction->text}}&rsquo;
          {{else}}
            {{tr}}Création d'une fonction{{/tr}}
          {{/if}}
          </th>
        </tr>
        
        <tr>
          <th>
            <label for="text" title="Intitulé de la fonction. Obligatoire">Intitulé</label>
          </th>
          <td>
            <input type="text" name="text" title="{{$userfunction->_props.text}}" size="30" value="{{$userfunction->text}}" />
          </td>
        </tr>
        
        <tr>
          <th>
            <label for="group_id" title="Etablissement auquel se rattache la fonction">Etablissement</label>
          </th>
          <td>
            <select name="group_id" title="{{$userfunction->_props.group_id}}">
              <option value="">&mdash; choisir un établissement</option>
              {{foreach from=$listGroups item=curr_group}}
              <option value="{{$curr_group->group_id}}" {{if $curr_group->group_id == $userfunction->group_id}} selected="selected" {{/if}}>
                {{$curr_group->text}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="type" title="Type de fonction. Obligatoire">Type</label></th>
          <td>
            <select name="type" title="{{$userfunction->_props.type}}">
              <option value="">&mdash; Choisir un type</option>
              {{html_options options=$userfunction->_enumsTrans.type selected=$userfunction->type}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="color" title="Couleur de visualisation des fonctions dans les plannings">Couleur</label></th>
          <td>
            <span id="test" title="test" style="background: #{{$userfunction->color}};">
            <a href="javascript:popColor()">cliquez ici</a>
            </span>
            <input type="hidden" name="color" title="{{$userfunction->_props.color}}" value="{{$userfunction->color}}" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $userfunction->function_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fonction',objName:'{{$userfunction->text|escape:"javascript"}}'})">
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