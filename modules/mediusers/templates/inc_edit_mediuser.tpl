{{mb_script module=hospi script=affectation_uf ajax=1}}

<script type="text/javascript">
  searchUserLDAP = function(object_id) {
    var url = new Url("admin", "ajax_search_user_ldap");
    url.addParam("object_id", object_id);
    url.requestModal(800, 350);
    url.modalObject.observe("afterClose", function() { location.reload(); } );
    window.ldapurl = url;
  };

  showPratInfo = function(type) {
    var ps_types = {{$ps_types|@json}};
    Control.Tabs.getTabAnchor('infos_praticien').setClassName('wrong', !ps_types.include(type));
  };

  loadProfil = function(type){
    var tabProfil = {{$tabProfil|@json}};

    // Liste des profils dispo pour le type selectionné
    var listProfil = tabProfil[type] || [];

    $A(document.mediuser._profile_id).each( function(input) {
      input.disabled = !listProfil.include(input.value) && input.value;
      input.selected = input.selected && !input.disabled;
    });
  };

  changeRemote = function(input) {
    var oPassword = $(input.form._user_password);

    // can the user connect remotely ?
    var canRemote = $V(input)==0;

    // we change the form element's spec
    oPassword.className = canRemote?
      '{{$object->_props._user_password_strong}}':
      '{{$object->_props._user_password_weak}}';

    {{if !$object->user_id}}oPassword.addClassName('notNull');{{/if}}

    // Admin user can force the remote field toggle
    {{if !$is_admin}}
      // Force to re-enter password
      if (canRemote) {
        oPassword.addClassName('notNull');
      }
    {{/if}}

    // we check the field
    checkFormElement(oPassword);
  };

  unlinkOrUpdateUserLDAP = function(user_id, action) {
    var url = new Url("admin", "ajax_unlink_update_user_ldap");
    url.addParam("user_id", user_id);
    url.addParam("action", action);
    url.requestUpdate(SystemMessage.id, {onComplete: function() {showMediuser(user_id);}});
  };

  Main.add(function () {
    {{if $object->_id}}
      showPratInfo("{{$object->_user_type}}");
      loadProfil("{{$object->_user_type}}");
    {{/if}}
  });
</script>

{{assign var=configLDAP value=$conf.admin.LDAP.ldap_connection}}
{{if $configLDAP && $object->_ref_user && $object->_ref_user->_ldap_linked}}
  {{assign var=readOnlyLDAP value=true}}
{{else}}
  {{assign var=readOnlyLDAP value=null}}
{{/if}}

{{if $object->_id}}
  <a class="button search" style="" href="?m=admin&amp;tab=view_edit_users&amp;user_username={{$object->_user_username}}&amp;user_id={{$object->_id}}">
    {{tr}}CMediusers_administer{{/tr}}
  </a>

  {{if $configLDAP}}
    <button class="search" {{if $readOnlyLDAP}}disabled="disabled"{{/if}} onclick="searchUserLDAP('{{$object->_id}}')">
      {{tr}}CMediusers_search-ldap{{/tr}}
    </button>
    {{if $object->_ref_user && $object->_ref_user->_ldap_linked}}
      <button class="cancel" type="button" onclick="unlinkOrUpdateUserLDAP({{$object->_id}}, 'unlink')">{{tr}}Unlink{{/tr}} du LDAP</button>
      <button class="search" type="button" onclick="unlinkOrUpdateUserLDAP({{$object->_id}}, 'update')">{{tr}}Update{{/tr}} à partir du LDAP</button>
    {{/if}}
  {{/if}}
{{/if}}

{{if $readOnlyLDAP}}
<div class="small-warning">
  {{tr}}CUser_associate-ldap{{/tr}}
</div>
{{/if}}

{{if $is_robot}}
  <div class="small-info">
    {{tr}}CUser_user-robot{{/tr}}{{if $tag}} : <strong>{{$tag}}</strong>{{/if}}.
  </div>
{{/if}}

<div class="small-info">
  {{$pwd_info}}
</div>

<form name="mediuser" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : Control.Modal.close});">
  {{if !$can->edit}}
    <input name="_locked" value="1" hidden="hidden" />
  {{/if}}
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_mediusers_aed" />
  <input type="hidden" name="user_id" value="{{$object->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_user_id" value="{{$object->_user_id}}" />

  <table class="form">
    <tr>
      {{if $object->_id}}
      <th class="title modify text" colspan="2">
        {{mb_include module=system template=inc_object_idsante400 object=$object}}
        {{mb_include module=system template=inc_object_history    object=$object}}
        {{mb_include module=system template=inc_object_uf         object=$object}}
        {{mb_include module=system template=inc_object_idex       object=$object tag=$tag_mediuser}}

        {{tr}}CMediusers-title-modify{{/tr}} '{{$object->_user_username}}'
      </th>
      {{else}}
      <th class="title" colspan="2">
        {{tr}}CMediusers-title-create{{/tr}}
      </th>
      {{/if}}
    </tr>
  </table>

  <script>
    Main.add(Control.Tabs.create.curry('tabs-form', true));
  </script>

  <ul id="tabs-form" class="control_tabs">
    <li><a href="#identification">Identification</a></li>
    <li><a href="#infos_praticien">Professionnel de santé</a></li>
    <li><a href="#iconographie">Iconographie</a></li>
    {{if $conf.ref_pays == 2}}
      <li><a href="#facturation">Facturation</a></li>
      <li><a href="#sources">Archivage</a></li>
    {{/if}}
  </ul>

  <hr class="control_tabs" />

  <table id="identification" class="form">
    {{mb_include template=inc_identification}}
  </table>

  <table id="infos_praticien" class="form" style="display: none;">
    {{mb_include template=inc_infos_praticien name_form="mediuser"}}
  </table>

  <table id="iconographie" class="form" style="display: none;">
    {{mb_include template=inc_iconographie}}
  </table>

  {{if $conf.ref_pays == 2}}
    <table id="facturation" class="form" style="display: none;">
      {{mb_include template=inc_facturation name_form="mediuser"}}
    </table>
    <div id="sources" class="form" style="display: none;">
    </div>
  {{/if}}

  <table class="form">
    <tr>
      <td class="button" colspan="2">
        {{if $object->user_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur',objName:'{{$object->_user_username|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>