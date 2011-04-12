<script type="text/javascript">

function loadProfil(type){
  var tabProfil = {{$tabProfil|@json}};

  // Liste des profils dispo pour le type selectionné
  var listProfil = tabProfil[type] || [];
  
  $A(document.mediuser._profile_id).each( function(input) {
    input.disabled = !listProfil.include(input.value) && input.value;
    input.selected = input.selected && !input.disabled;
  });  
}

function showPratInfo(type) {
	// Type de la classe admin 
	$('show_prat_info').setVisible(type == 3 || type == 4 || type == 13);
}

function changeRemote(o) {
  var oPassword = $(o.form._user_password);
  
  // can the user connect remotely ?
  var canRemote = $V(o)==0;
  
  // we change the form element's spec 
  oPassword.className = canRemote?
  '{{$object->_props._user_password_strong}}':
  '{{$object->_props._user_password_weak}}';
  
  {{if !$object->user_id}}oPassword.addClassName('notNull');{{/if}}
  
  // we check the field
  checkFormElement(oPassword);
}

showMediuser = function(user_id, element){
  element.up('tr').addUniqueClassName('selected');
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
              <label>
                <input onchange="$V(this.form.page, 0)" type="checkbox" name="pro_sante" {{if $pro_sante}}checked="checked"{{/if}} /> Professionnel de santé
              </label>
              <label>
                <input onchange="$V(this.form.page, 0)" type="checkbox" name="inactif" {{if $inactif}}checked="checked"{{/if}} /> Inactif
              </label>
              {{if $configLDAP}}
              <label>
                <input onchange="$V(this.form.page, 0)" type="checkbox" name="ldap_bound" {{if $ldap_bound}}checked="checked"{{/if}} /> Associé au LDAP
              </label>
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
      {{mb_include template=inc_edit_mediuser}}
    </td>
  </tr>
</table>