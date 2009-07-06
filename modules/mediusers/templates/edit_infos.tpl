{{mb_include_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
Main.add(function () {
  initInseeFields("editFct", "cp", "ville");
  var tabs = Control.Tabs.create('tab_edit_mediuser', true);
  
  var url = new Url("admin", "edit_prefs");
  url.addParam("user_id", "{{$user->user_id}}");
  url.requestUpdate("edit-preferences");
});
</script>

<ul id="tab_edit_mediuser" class="control_tabs">
  <li><a href="#edit-mediuser">{{tr}}Account{{/tr}}</a></li>
  <li><a href="#edit-preferences">{{tr}}Preferences{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<div id="edit-mediuser" style="display: none;">
<table class="main">
  <tr>
    <td class="halfPane">
      <form name="editUser" action="?m={{$m}}&amp;a=edit_infos" method="post" onsubmit="return onSubmitFormAjax(this);">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="user_id" value="{{$user->user_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="modifDroit" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{$user->_view}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_last_name"}}</th>
          <td>{{mb_field object=$user field="_user_last_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_first_name"}}</th>
          <td>{{mb_field object=$user field="_user_first_name"}}</td>
        </tr>
        
        <tbody {{if ($user->_user_type != 3) && ($user->_user_type != 4) && ($user->_user_type != 13)}}style="display:none"{{/if}}>
        
          {{include file="inc_infos_praticien.tpl" object=$user}}     
          
        </tbody>
                
        <tr>
          <th>{{mb_label object=$user field="_user_email"}}</th>
          <td>{{mb_field object=$user field="_user_email"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_phone"}}</th>
          <td>{{mb_field object=$user field="_user_phone"}}</td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button type="submit" class="modify">{{tr}}Modify{{/tr}}</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    
    <td class="halfPane">
      <form name="editFct" action="?m={{$m}}" method="post" onSubmit="return onSubmitFormAjax(this)">
      <input type="hidden" name="dosql" value="do_functions_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="function_id" value="{{$fonction->function_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{$fonction->_view}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="soustitre"}}</th>
          <td>{{mb_field object=$fonction field="soustitre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="adresse"}}</th>
          <td>{{mb_field object=$fonction field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="cp"}}</th>
          <td>
            {{mb_field object=$fonction field="cp"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="ville"}}</th>
          <td>
            {{mb_field object=$fonction field="ville"}}
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="tel"}}</th>
          <td>{{mb_field object=$fonction field="tel"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="fax"}}</th>
          <td>{{mb_field object=$fonction field="fax"}}</td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button type="submit" class="modify">{{tr}}Modify{{/tr}}</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
</div>

<div id="edit-preferences" style="display: none;"></div>
