<form name="Edit-{{$appel->_guid}}" method="post" onsubmit="return Appel.submit(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  {{mb_key    object=$appel}}
  {{mb_class  object=$appel}}
  <input type="hidden" name="del" value="0"/>
  {{mb_field object=$appel field=sejour_id hidden=true}}
  {{mb_field object=$appel field=user_id hidden=true}}
  {{mb_field object=$appel field=type hidden=true}}
  {{mb_field object=$appel field=etat hidden=true}}

  <table class="form">
    {{if !$appel_id}}
      <tr>
        <th colspan="2" class="title">Liste des appels</th>
      </tr>
      {{foreach from=$sejour->_ref_appels_by_type.$type item=_appel}}
        <tr>
          <th>{{mb_value object=$_appel field=etat}}</th>
          <td>
            {{if $app->user_id == $_appel->user_id && $_appel->datetime|date_format:$conf.date == $smarty.now|date_format:$conf.date}}
              <button type="button" class="edit notext" title="{{tr}}Modify{{/tr}}" onclick="Appel.edit('{{$_appel->_id}}', '{{$type}}', '{{$sejour->_id}}');"></button>
            {{/if}}

            <strong>[{{mb_value object=$_appel field=datetime}}]</strong>
            <span class="compact">{{$_appel->commentaire}} &nbsp;</span>
            <span style="float: right;">
            réalisé par {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_appel->_ref_user}} &nbsp;
              {{mb_include module=system template=inc_object_history object=$_appel}}
            </span>
          </td>
        </tr>
      {{/foreach}}
    {{/if}}
     {{if $appel_id || (!$appel->_id && !($sejour->_ref_appels_by_type.$type instanceof CAppelSejour))}}
       {{if $appel->_id}}
         <th class="title modify" colspan="2">
           {{mb_include module=system template=inc_object_notes     object=$appel}}
           {{mb_include module=system template=inc_object_idsante400 object=$appel}}
           {{mb_include module=system template=inc_object_history   object=$appel}}
           {{tr}}{{$appel->_class}}-title-modify{{/tr}}
         </th>
       {{/if}}
       <tr>
         <th class="title" colspan="2">
           {{assign var=patient value=$sejour->_ref_patient}}
           <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
          {{$patient->_view}} - {{mb_value object=$patient field=_age}} ({{mb_value object=$patient field=naissance}})
        </span>
           <br/>
         </th>
       </tr>
       <tr>
         <th></th>
         <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
          {{$sejour->_view}}
        </span>
         </td>
       </tr>
       <tr>
         <th>{{mb_label object=$patient field=tel}}</th>
         <td>{{mb_value object=$patient field=tel}}</td>
       </tr>
       <tr>
         <th>{{mb_label object=$patient field=tel2}}</th>
         <td>{{mb_value object=$patient field=tel2}}</td>
       </tr>
       {{assign var=appel_guid value=$appel->_guid}}
       <tr>
         <th>{{mb_label object=$appel field=datetime}}</th>
         <td>{{mb_field object=$appel field=datetime form="Edit-$appel_guid" canNull="true" register=true}}</td>
       </tr>
       <tr>
         <th>{{mb_label object=$appel field=commentaire}}</th>
         <td>{{mb_field object=$appel field=commentaire textearea=true}}</td>
       </tr>
       <tr>
         <td class="button" colspan="2">
           {{if $appel->_id}}
             <button class="tick" type="button" onclick="Appel.changeEtat(this.form, 'realise');">{{tr}}CAppelSejour.etat.realise{{/tr}}</button>
             <button class="cancel" type="button" onclick="Appel.changeEtat(this.form, 'echec');">{{tr}}CAppelSejour.etat.echec{{/tr}}</button>
             <button class="cancel" type="button" onclick="Appel.submit(this.form);">{{tr}}Close{{/tr}}</button>
             <button class="trash" type="button" onclick="$V(this.form.del, 1);Appel.onDeletion(this.form);">{{tr}}Delete{{/tr}}</button>

           {{else}}
             <button class="tick" type="button" onclick="Appel.changeEtat(this.form, 'realise');">{{tr}}CAppelSejour.etat.realise{{/tr}}</button>
             <button class="cancel" type="button" onclick="Appel.changeEtat(this.form, 'echec');">{{tr}}CAppelSejour.etat.echec{{/tr}}</button>
             <button class="cancel" type="button" onclick=" Appel.modal.close();">{{tr}}Cancel{{/tr}}</button>
           {{/if}}
         </td>
       </tr>
     {{/if}}
  </table>
</form>