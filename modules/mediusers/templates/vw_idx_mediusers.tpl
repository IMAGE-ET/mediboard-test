<script type="text/javascript">

popupImport = function () {
  var url = new Url("mediusers", "user_import_csv");
  url.popup(800, 600, "Import des utilisateurs");
  return false;
}

showMediuser = function(user_id, element) {
  if (element) {
    element.up('tr').addUniqueClassName('selected');
  }

  var url = new Url("mediusers", "ajax_edit_mediuser");
  url.addParam("user_id", user_id);
  url.requestUpdate("vw_mediuser");
}

createUserFromLDAP = function(){
  var url = new Url("admin", "ajax_choose_filter_ldap");
  url.requestModal(800, 350);
}

function changePage(page) {
	$V(getForm('listFilter').page,page);
}

Main.add(function() {
  showMediuser('{{$user_id}}');
});
</script>

{{assign var=configLDAP value=$conf.admin.LDAP.ldap_connection}}

<table class="main">
  {{if $can->edit}}
  <tr>
    <td style="width: 60%">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id=0" class="button new">
        {{tr}}CMediusers-title-create{{/tr}}
      </a>
    
      {{if $configLDAP}}
        <button class="new" onclick="createUserFromLDAP()">
          {{tr}}CMediusers_create-ldap{{/tr}}
        </button>
      {{/if}}
    </td>
  </tr>
  {{/if}}
  <tr>
  	<td>
  	  <button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button>
      <form name="listFilter" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        <table class="form">
          <tr>
            <th class="title" colspan="10">
              {{tr}}Filter{{/tr}}
            </th>
          </tr>
        	<tr>
        		<th>Champ de recherche</th>
        		<td>
              <input type="text" name="filter" value="{{$filter}}" onchange="$V(this.form.page, 0)" />
            </td>
          </tr>
          <tr>
            <th>Afficher seulement</th>
            <td>
              <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="pro_sante" {{if $pro_sante}}checked="checked"{{/if}} />
              <label for="pro_sante">Professionnel de santé</label>
              <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="inactif" {{if $inactif}}checked="checked"{{/if}} />
              <label for="inactif">Inactif</label>
              {{if $configLDAP}}
              <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="ldap_bound" {{if $ldap_bound}}checked="checked"{{/if}} />
              <label>Associé au LDAP</label>
              {{/if}}
            </td>
          </tr>
        	<tr>
            <td colspan="2" style="text-align: center">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>        
        {{if $total_mediuser != 0}}
          {{mb_include module=system template=inc_pagination total=$total_mediuser current=$page change_page='changePage'}}
	      {{/if}}
      </form>
      {{mb_include template=vw_list_mediusers}}
    </td>
    <td style="width: 40%" id="vw_mediuser">
    </td>
  </tr>
</table>