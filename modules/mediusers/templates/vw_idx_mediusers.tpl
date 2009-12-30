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

showMediuser = function(user_id){
  var url = new Url;
  url.setModuleAction("mediusers", "ajax_show_mediuser");
  url.addParam("user_id", user_id);
  url.requestUpdate("vw_mediuser");
}
 
</script>

<table class="main">
  <tr>
    <td style="width: 60%">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id=0" class="button new">
        {{tr}}CMediusers-title-create{{/tr}}
      </a>
      <form name="listFilter" action="?m={{$m}}" method="get" style="float: right;">
        <input type="hidden" name="m"      value="{{$m}}" />
        <input type="hidden" name="tab"    value="{{$tab}}" />
        <input type="text"   name="filter" value="{{$filter}}" />
      </form>
      {{include file="vw_list_mediusers.tpl"}}
    </td>
    
    <td style="width: 40%" id="vw_mediuser">
      {{include file="inc_edit_mediuser.tpl"}}
    </td>
  </tr>
</table>