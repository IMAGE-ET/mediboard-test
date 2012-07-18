<form name="create-module-form" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="developpement" />
  <input type="hidden" name="dosql" value="do_create_module" />
  
  <table class="main form">
    <tr>
      <th colspan="2" class="title">
        Création d'un module
      </th>
    </tr>
    <tr>
      <th><label for="name_canonical">Nom canonique</label></th>
      <td><input type="text" name="name_canonical" class="str notNull pattern|[a-zA-Z0-9_]*"/></td>
    </tr>
    <tr>
      <th><label for="name_short">Nom court</label></th>
      <td><input type="text" name="name_short" class="str notNull" /></td>
    </tr>
    <tr>
      <th><label for="name_long">Nom long</label></th>
      <td><input type="text" name="name_long" class="str notNull"/></td>
    </tr>
    <tr>
      <th><label for="license">Licence</label></th>
      <td>
        <select name="license" class="notNull">
          {{foreach from=$licenses item=_license_value key=_license}}
            <option name="{{$_license}}">{{$_license}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th></th>
      <td>
        <button type="submit" class="submit">Créer le module</button>
      </td>
    </tr>
  </table>
</form> 