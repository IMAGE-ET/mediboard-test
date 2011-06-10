{{if $admin_admission}}
  <script type="text/javascript">
    // A la fermeture de la modale, actions qui peuvent être effectuées
    someReload = function() {
      {{if isset($patient_id|smarty:nodefaults)}}
        // Rechargement du dossier patient
        if (window.reloadPatient && "{{$patient_id}}") {
          reloadPatient("{{$patient_id}}");
        }
      {{/if}}
      // Rechargement des pré-admissions
      if (window.reloadPreAdmission) {
        reloadPreAdmission();
      }
      // Rechargement des admissions
      if (window.reloadAdmission) {
        reloadAdmission();
      }
      // Rechargement des sorties
      if (window.reloadSorties) {
        reloadSorties();
      }
      // On relance le periodical updater de l'identito-vigilance
      if (window.IdentitoVigilance) {
        IdentitoVigilance.start(0, 60);
      }
    }
    
    Main.add(function() {
      if (window.IdentitoVigilance) {
        IdentitoVigilance.stop();
      }
      var div = getForm("editTags").up("div").up("div");
      var cancel_button = div.down("button.cancel");
      var reload_button =  div.down("button.change");
  
      cancel_button.observe("click", someReload);
      reload_button.observe("click", function() {
        cancel_button.stopObserving("click", someReload);
      });
    });
    
    trashNumDossier = function(idSante400_id) {
      var url = new Url("dPsante400", "ajax_trash_id400");
      url.addParam("idSante400_id", idSante400_id);
      url.requestUpdate("systemMsg", {onComplete : function() {
        getForm("editTags").up("div").up("div").down("button.change").click();
      }});
    }
  </script>
{{/if}}
<form name="editTags" method="get" action="?">
  <table class="tbl">
    <tr>
      <th class="title" colspan="{{if !$admin_admission}}5{{else}}6{{/if}}">
        {{tr}}CMbObject-back-identifiants{{/tr}}
      </th>
    </tr>
    <tr>
      {{if $admin_admission}}
        <th></th>
        <th>{{mb_title class=CIdSante400 field=object_class}}</th>  
        <th>{{mb_title class=CIdSante400 field=last_update}}</th>
        <th>{{mb_title class=CSejour field=_num_dossier}}</th> 
        <th>{{mb_title class=CIdSante400 field=tag}}</th>
      {{/if}}
    </tr>
    {{foreach from=$listIdSante400 item=_idSante400}}
      <tr>
        {{if $admin_admission && $sip_active}}
          <td>
            <input type="radio" name="radio[]" {{if $_idSante400->_id == $idSante400_id}}checked="checked"{{/if}}
              onchange="trashNumDossier('{{$_idSante400->_id}}');"/>
          </td>
        {{else}}
          <td></td>
        {{/if}}
        <td>{{$_idSante400->object_class}}</td>

        <td>{{$_idSante400->last_update|date_format:$conf.datetime}}</td>
        <td>{{$_idSante400->id400}}</td>
        <td>{{$_idSante400->tag}}</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="5">{{tr}}CIdSante400.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>

{{if !$admin_admission}}
  <div class="info">
    {{tr}}CIdSante400.cannot_modify_id400{{/tr}}
  </div>
{{/if}}