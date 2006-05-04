<!--  $Id$ -->
{literal}
<script type="text/javascript">

var classes = {
  {/literal}
  {foreach from=$classes key=key_class item=fields}
  "{$key_class}" : {ldelim}
    {foreach from=$fields item=field}
      "{$field|escape:javascript}" : null,
    {/foreach}
  {rdelim},
  {/foreach}
  {literal}
};

function loadItems(select, options, value) {
  // delete all former options
  while (select.length > 1) {
    select.options[1] = null;
  }

  // insert new ones
  for (var elm in options) {
	if (typeof(options[elm]) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(elm, elm, elm == value);
    }
  }
}

function loadClasses(value) {
  var form = document.editFrm;
  var select = form.elements['class'];

  loadItems(select, classes, value);
  loadFields();
}

function loadFields(value) {
  var form = document.editFrm;
  var select = form.elements['field'];
  var className  = form.elements['class' ].value;
  var fields = classes[className];
  loadItems(select, fields, value);
}

function pageMain() {
  {/literal}
  loadClasses('{$aide->class}');
  loadFields('{$aide->field}');
  {literal}
}

</script>
{/literal}

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{$m}" />
        
    <table class="form">
      <tr>
        <th class="category" colspan="10">Filtrer les aides</th>
      </tr>

      <tr>
        <th><label for="filter_user_id" title="Filtrer les aides pour cet utilisateur">Utilisateur :</label></th>
        <td>
          <select name="filter_user_id" onchange="this.form.submit()">
            <option value="0">&mdash; Tous les utilisateurs</option>
            {foreach from=$users item=curr_user}
            <option value="{$curr_user->user_id}" {if $curr_user->user_id == $filter_user_id} selected="selected" {/if}>
              {$curr_user->_view}
            </option>
            {/foreach}
          </select>
        </td>
        <th><label for="filter_class" title="Filtrer les aides pour ce type d'objet">Type d'objet :</label></th>
        <td>
          <select name="filter_class" onchange="this.form.submit()">
            <option value="0">&mdash; Tous les types d'objets</option>
            {foreach from=$classes key=class_name item=fields}
            <option {if $class_name == $filter_class} selected="selected" {/if}>
              {$class_name}
            </option>
            {/foreach}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="6"><strong>Liste des aides à la saisie</strong></th>
    </tr>
    
    <tr>
      <th>Utilisateur</th>
      <th>Type d'objet</th>
      <th>Champ de l'objet</th>
      <th>Nom de l'aide</th>
      <th>Texte de remplacement</th>
    </tr>

    {foreach from=$aides item=curr_aide}
    <tr>
      {eval var=$curr_aide->aide_id assign="aide_id"}
      {assign var="href" value="?m=$m&amp;tab=$tab&amp;aide_id=$aide_id"}
      <td><a href="{$href}">{$curr_aide->_ref_user->_view}</a></td>
      <td><a href="{$href}">{$curr_aide->class}</a></td>
      <td><a href="{$href}">{$curr_aide->field}</a></td>
      <td><a href="{$href}">{$curr_aide->name}</a></td>
      <td class="text">{$curr_aide->text|nl2br}</td>
    </tr>
    {/foreach}
      
    </table>

  </td>
  
  <td class="pane">

	<a href="index.php?m={$m}&amp;tab={$tab}&amp;aide_id=0"><strong>Créer une aide à la saisie</strong></a>

    <form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_aide_aed" />
    <input type="hidden" name="aide_id" value="{$aide->aide_id}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {if $aide->aide_id}
        Modification d'une aide
      {else}
        Création d'une aide
      {/if}
      </th>
    </tr>

    <tr>
      <th><label for="user_id" title="Utilisateur concerné, obligatoire.">Utilisateur :</label></th>
      <td>
        <select name="user_id" title="{$aide->_props.user_id}">
          <option value="0">&mdash; Choisir un utilisateur</option>
          {foreach from=$users item=curr_user}
          <option value="{$curr_user->user_id}" {if $curr_user->user_id == $aide->user_id} selected="selected" {/if}>
            {$curr_user->_view}
          </option>
          {/foreach}
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="class" title="Type d'objet concerné, obligatoire.">Objet:</label></th>
      <td>
        <select name="class" title="{$aide->_props.class}" onchange="loadFields()">
          <option value="">&mdash; Choisir un objet</option>
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="field" title="Champ de l'objet concerné, obligatoire.">Champ:</label></th>
      <td>
        <select name="field" title="{$aide->_props.field}">
          <option value="">&mdash; Choisir un champ</option>
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="name" title="intitulé de l'aide, obligatoire.">Intitulé:</label></th>
      <td><input type="text" name="name" title="{$aide->_props.name}" value="{$aide->name}" /></td>
    </tr>
    
    <tr>
      <th><label for="text" title="Texte de remplacement.">Texte:</label></th>
      <td>
        <textarea style="width: 200px" rows="4" name="text" title="{$aide->_props.text}">{$aide->text}</textarea>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {if $aide->aide_id}
        <input type="reset" value="Réinitialiser" />
        <input type="submit" value="Valider" />
        <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'l\'aide',objName:'{$aide->name|escape:javascript}'{rdelim})" />
        {else}
        <input type="submit" value="Créer" />
        {/if}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
</tr>

</table>
