<script type="text/javascript">

function submitImeds(oForm, type){
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadUserImeds(oForm.object_id.value, oForm.tag.value, oForm.type.value) } });
}

function reloadUserImeds(user_id, tag, type){
  var url = new Url;
  url.setModuleAction("dPImeds", "httpreq_vw_id_imeds");
  url.addParam("user_id", user_id);
  url.addParam("tag"    , tag);
  url.addParam("type"   , type);
  url.requestUpdate('user-'+user_id+'-'+tag);
}

</script>


<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Nom de l'utilisateur</th>
          <th>Login Imeds</th>
          <th>Mot de passe Imeds</th>
        </tr>
        {{foreach from=$tab key=user_id item=user}}
        {{assign var=mediuser value=$mediusers.$user_id}}
        {{assign var=id_login value=$tab.$user_id.login}}
        {{assign var=id_password value=$tab.$user_id.password}}
        <tr>
          <td>{{$mediuser->_view}}</td>
          <td id="user-{{$mediuser->_id}}-{{$tag_login}}">
            {{include file="inc_vw_id_imeds.tpl" type="text" tag=$tag_login id_externe=$id_login}}
					</td>
					<td id="user-{{$mediuser->_id}}-{{$tag_password}}">
					  {{include file="inc_vw_id_imeds.tpl" type="password" tag=$tag_password id_externe=$id_password}}
					</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>