<!-- $Id$ -->

{literal}
<script type="text/javascript">

function checkItemForm() {
  var form = document.frmItemPerms;
  var field = null;
  
  if (field = form.permission_item)
    if (field.value == -1) {
      alert("{/literal}{tr}Please choose an item.{/tr}{literal}");
      popPermItem();
      return false;
    }

  return true;
}

function delModulePerm(id) {
	if (confirm( "{/literal}{tr}Are you sure you want to delete this module permission?{/tr}{literal}")) {
		var f = document.frmItemPerms;
		f.del.value = 1;
		f.permission_id.value = id;
		f.submit();
	}
}

function delItemPerm(id) {
	if (confirm( "{/literal}{tr}Are you sure you want to delete this item permission?{/tr}{literal}")) {
		var f = document.frmModulePerms;
		f.del.value = 1;
		f.permission_id.value = id;
		f.submit();
	}
}

var tables = new Array;
{/literal}
{foreach from=$pgos item=pgo key=module}
	tables['{$module}'] = '{$pgo.table}';
{/foreach}
{literal}

function popPermItem() {
	var f = document.frmItemPerms;
	var pgo = f.permission_grant_on.options[f.permission_grant_on.selectedIndex].value;
	if (!(pgo in tables)) {
		alert( 'No list associated with the module ' + pgo + '.' );
		return;
	}
  
  var url = new Url();
  url.setModuleAction("admin", "selector");
  url.addParam("callback", "setPermItem");
  url.addParam("table", tables[pgo]);
  url.popup(400, 250, "Selector");
}

// Callback function for the generic selector
function setPermItem( key, val ) {
	var f = document.frmItemPerms;
	if (val != '') {
		f.permission_item.value = key;
		f.permission_item_name.value = val;
	}
}

</script>
{/literal}

<table class="main">
  <tr>
    <td class="halfPane">
    
    <table class="tbl">
      <tr>
      	<th />
      	<th>{tr}Module{/tr}</th>
      	<th>{tr}Item{/tr}</th>
      	<th>{tr}Type{/tr}</th>
      	<th />
      </tr>

      {foreach from=$userPerms item=userPerm} 
      <tr>
		{if $userPerm.perm_module == "all" && $userPerm.perm_item == -1}
			{assign var="bg_color" value="ffc235"}
		{elseif $userPerm.perm_item == -1 }
			{assign var="bg_color" value="ffff99"}
		{else}
			{assign var="bg_color" value="transparent"}
		{/if}

        <td style="background: #{$bg_color};">
          {if $canEdit}
          <a href="index.php?m={$m}&amp;a={$a}&amp;user_id={$user_id}&amp;perm_id={$userPerm.perm_id}">
          	{html_image file="./images/icons/stock_edit-16.png"}
          </a>
          {/if}
        </td>

		<td style="background: #{$bg_color};">{tr}{$userPerm.perm_module_name}{/tr}</td>
	
		<td style="background: #{$bg_color}; width: 100%;">{$userPerm.perm_item_name}</td>
	
		<td style="background: #{$bg_color}; width: 100%;">{tr}{$userPerm.perm_value_name}{/tr}</td>
	
		<td style="background: #{$bg_color};">
		{if $canEdit}
		  {if $userPerm.perm_item == -1}
		  <a href="javascript:delModulePerm({$userPerm.perm_id});" title="{tr}delete{/tr}">
          {else}
		  <a href="javascript:delItemPerm({$userPerm.perm_id});" title="{tr}delete{/tr}">
          {/if}
        	{html_image file="./images/icons/stock_delete-16.png"}
		  </a>
		{/if}
		</td>
	  </tr>	
	  {/foreach}
	</table>
	
    <table>
      <tr>
		<td style="width: 20px; background: #ffc235;"></td>
		<td> = {tr}Global permission{/tr}</td>
		<td style="width: 20px; background: #ffff99;"></td>
		<td> = {tr}Module permission{/tr}</td>
      </tr>
    </table>

  	</td>
  	<td class="halfPane">
	
	{if $canEdit}
	
	{if !$permSel->permission_id || $permSel->permission_item == -1}
	<!-- AddEdit Permission on modules -->
	<form name="frmModulePerms" method="post" action="?m={$m}">

	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="dosql" value="do_perms_aed" />
	<input type="hidden" name="user_id" value="{$user_id}" />
	<input type="hidden" name="permission_user" value="{$user_id}" />
	<input type="hidden" name="permission_id" value="{$permSel->permission_id}" />
	<input type="hidden" name="permission_item" value="-1" />

	<table class="form">
	  <tr>
        {if $permSel->permission_id}
		<th class="category" colspan="4">{tr}Edit permission on module{/tr}</th>
        {else}
		<th class="category" colspan="4">{tr}Add permission on module{/tr}</th>
        {/if}
	  </tr>
	  
	  <tr>
	    <th>{tr}Module{/tr}:</th>
	    <td colspan="3">
	      <select name="permission_grant_on">
	        <option value="all">{tr}All Modules{/tr}</option>
	      	{html_options options=$modules selected=$permSel->permission_grant_on}
	      </select>
		</td>
	  </tr>

      <tr>
	    <th>{tr}Level{/tr}:</th>
	    <td>
          <input type="checkbox" name="_module_visible" {if $permSel->_module_visible}checked="checked"{/if} />
          <label for="_module_visible">{tr}Visible{/tr}</label>
        </td>
        <td>
          <input type="checkbox" name="_module_readall" {if $permSel->_module_readall}checked="checked"{/if} />
          <label for="_module_readall">{tr}Read All{/tr}</label>
        </td>
        <td>
          <input type="checkbox" name="_module_editall" {if $permSel->_module_editall}checked="checked"{/if} />
          <label for="_module_editall">{tr}Edit All{/tr}</label>
        </td>
      </tr>

      <tr>
        <td class="button" colspan="4">
          <input type="reset" value="{tr}Reset{/tr}" />
          {if $permSel->permission_id}
		  <input type="submit" value="{tr}Edit{/tr}" />
		  <input type="submit" value="{tr}Remove{/tr}" onclick="if (confirm('{tr}Please confirm removal{/tr}')) {ldelim}this.form.del.value = 1; this.form.submit();{rdelim}" />
		  {else}
          <input type="submit" value="{tr}Add{/tr}" />
		  {/if}
	    </td>
	  </tr>
    </table>
    
    </form>
	{/if}
	
	{if !$permSel->permission_id || $permSel->permission_item != -1}
	<!-- AddEdit Permission on items -->
	<form name="frmItemPerms" method="post" action="?m={$m}" onsubmit="return checkItemForm();">

	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="dosql" value="do_perms_aed" />
	<input type="hidden" name="user_id" value="{$user_id}" />
	<input type="hidden" name="permission_user" value="{$user_id}" />
	<input type="hidden" name="permission_id" value="{$permSel->permission_id}" />
	<input type="hidden" name="permission_item" value="{$permSel->permission_item}" />

	<table class="form">
	  <tr>
        {if $permSel->permission_id}
		<th class="category" colspan="3">{tr}Edit permission on item{/tr}</th>
        {else}
		<th class="category" colspan="3">{tr}Add permission on item{/tr}</th>
        {/if}
	  </tr>
	  
	  <tr>
	    <th>{tr}Module{/tr}:</th>
	    <td colspan="2">
	      <select name="permission_grant_on">
	      	{html_options options=$modules selected=$permSel->permission_grant_on}
	      </select>
		</td>
	  </tr>

	  <tr>
		<th>{tr}Item{/tr}:</th>
		<td class="readonly">
		  <input type="text" name="permission_item_name" class="text" size="60" value="" readonly="readonly" />
		</td>
	    <td class="button">	
		  <input type="button" name="choose_item" class="text" value="..." onclick="popPermItem();" />
		</td>
	  </tr>

      <tr>
	    <th>{tr}Level{/tr}:</th>
	    <td colspan="2">
	      <select name="permission_value">
	        {html_options options=$permItemValues selected=$permSel->permission_value}
	      </select>
        </td>
      </tr>

      <tr>
        <td class="button" colspan="3">
          <input type="reset" value="{tr}Reset{/tr}" />
          {if $permSel->permission_id}
		  <input type="submit" value="{tr}Edit{/tr}" />
		  <input type="submit" value="{tr}Remove{/tr}"  onclick="{literal}if (confirm('Please confirm removal')) {this.form.del.value = 1; this.form.submit();}{/literal}" />
		  {else}
          <input type="submit" value="{tr}Add{/tr}" />
		  {/if}
	    </td>
	  </tr>
    </table>
    
    </form>
    {/if}
    
	{if !$permSel->permission_id}

	<form name="cpPerms" method="post" action="?m={$m}">

	<input type="hidden" name="dosql" value="do_perms_cp" />
	<input type="hidden" name="user_id" value="{$user_id}" />
	<input type="hidden" name="permission_user" value="{$user_id}" />

	<table class="form">
      <tr>
	    <th class="category" colspan="2">{tr}Copy Permissions from Template{/tr}</th>
	  </tr>
	  
	  <tr>
	    <th><label for="temp_user_name">{tr}Copy Permissions from User{/tr}</label></th>
	    <td>
	      <select name="temp_user_name">
			{html_options options=$otherUsers}
		  </select>
		</td>
	
	  </tr>

	  <tr>
        <td colspan="2">
          <input type="checkbox" name="delPerms" class="text" value="true" checked="checked" />
	      <label for="delPerms">{tr}adminDeleteTemplate{/tr}</label>
	    </td>
      </tr>

      <tr>
	    <td class="button" colspan="2">
	      <input type="submit" value="{tr}Copy from Template{/tr}" class="button" name="cptempperms" />
	    </td>
      </tr>
  
	</table>

	</form>
	{/if}

	{/if}
	
	</td>
  </tr>
</table>
