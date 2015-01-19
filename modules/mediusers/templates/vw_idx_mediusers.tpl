{{assign var=configLDAP value=$conf.admin.LDAP.ldap_connection}}

<script>
  popupImport = function () {
    var url = new Url("mediusers", "user_import_csv");
    url.popup(800, 600, "Import des utilisateurs");
    return false;
  };

  editMediuser = function(user_id, element) {
    if (element) {
      element.up('tr').addUniqueClassName('selected');
    }

    var url = new Url("mediusers", "ajax_edit_mediuser");
    url.addParam("user_id", user_id);
    url.addParam("no_association", "{{$no_association}}");
    url.addParam("ldap_user_actif", "{{$ldap_user_actif}}");
    url.addParam("ldap_user_deb_activite", "{{$ldap_user_deb_activite}}");
    url.addParam("ldap_user_fin_activite", "{{$ldap_user_fin_activite}}");
    url.requestModal(800, 700);
    url.modalObject.observe("afterClose", function() {
      getForm('listFilter').onsubmit();
    });
  };

  createUserFromLDAP = function(){
    var url = new Url("admin", "ajax_choose_filter_ldap");
    url.requestModal(800, 350);
  };

  changePage = function(page) {
    $V(getForm('listFilter').page, page);
  };

  changeFilter = function(order, way) {
    var form = getForm('listFilter');
    $V(form.order_col, order);
    $V(form.order_way, way);
    form.onsubmit();
  };

  Main.add(function() {
    {{if $user_id}}
      editMediuser('{{$user_id}}');
    {{/if}}
    getForm('listFilter').onsubmit();
  });
</script>

{{if $can->edit}}
  <a href="#" onclick="editMediuser(0)" class="button new">
    {{tr}}CMediusers-title-create{{/tr}}
  </a>
  {{if $configLDAP}}
    <button class="new" onclick="createUserFromLDAP()">
      {{tr}}CMediusers_create-ldap{{/tr}}
    </button>
  {{/if}}
{{/if}}

<style>
  fieldset.fieldset_search div {
    display: inline-block;
  }
</style>

<button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button>
<hr/>
<form name="listFilter" action="?m={{$m}}" method="get" onsubmit="return onSubmitFormAjax(this, null, 'result_search_mb')">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="a" value="ajax_search_mediusers" />
  <input type="hidden" name="page" value="0" onchange="this.form.onsubmit()"/>
  <input type="hidden" name="order_col" value="function_id"/>
  <input type="hidden" name="order_way" value="ASC""/>

  <fieldset class="fieldset_search">
      <legend>Recherche d'{{tr}}CMediusers{{/tr}}</legend>

    <div>
      <label>
        Mots clés :
        <input type="text" name="filter" value="{{$filter}}" style="width: 15em;" onchange="$V(this.form.page, 0)" />
      </label>
    </div>

    <div>
      <label>{{tr}}CFunctions{{/tr}}
        <select name="function_id">
          <option value="">&mdash; {{tr}}CFunctions.all{{/tr}}</option>
          {{foreach from=$group->_ref_functions item=_function}}
            <option value="{{$_function->_id}}">{{$_function}}</option>
          {{/foreach}}
        </select>
      </label>
    </div>

    <div>
      {{mb_label class="CMediusers" field="_user_type"}}
      <select name="_user_type" style="width: 15em;">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$utypes key=curr_key item=_type}}
          <option value="{{if $curr_key != 0}}{{$curr_key}}{{/if}}" {{if $type == $curr_key}}selected="selected"{{/if}}>
            {{$_type}}
          </option>
        {{/foreach}}
      </select>
    </div>

    <div>
      <label>Verrouillage
        <select name="locked">
          <option value="">Tous</option>
          <option value="1">Verrouillés seulement</option>
          <option value="0">Non Verrouillés seulement</option>
        </select>
      </label>
    </div>

    <fieldset style="display:inline-block;">
      <legend>Afficher seulement</legend>
        <label>
          <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="pro_sante" />
          Professionnel de santé
        </label>
        <label>
          <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="inactif" />
          Inactif
        </label>
      {{if $configLDAP}}
          <label>
            <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="ldap_bound" />
            Associé au LDAP
          </label>
      {{/if}}
        <label>
          <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="human" />
          Humain
        </label>
        <label>
          <input onchange="$V(this.form.page, 0, false)" type="checkbox" name="robot" />
          Robot
        </label>
    </fieldset>

    <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
  </fieldset>
</form>

<div id="result_search_mb" style="overflow: hidden"></div>
