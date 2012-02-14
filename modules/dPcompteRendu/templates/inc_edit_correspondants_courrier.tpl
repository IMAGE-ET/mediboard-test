{{assign var=correspondants value=$compte_rendu->_refs_correspondants_courrier_by_tag_guid}}

{{if $compte_rendu->_id}}
  <script type="text/javascript">
    Main.add(function () {
      var form = getForm("editFrm");
      var formCorres = getForm("addCorrespondant");
      var url = new Url("dPpatients", "httpreq_do_medecins_autocomplete");
      url.autoComplete(form._view, "correspondants_area", {
        minChars: 2,
        dropdown: true,
        afterUpdateElement : function(input, selected) {
          $V(formCorres.object_id, selected.id.split("-")[1]);
          $V(formCorres.compte_rendu_id, '{{$compte_rendu->_id}}');
          onSubmitFormAjax(formCorres, {onComplete: function(){
            Control.Modal.close();
            openCorrespondants('{{$compte_rendu->_id}}', '{{$compte_rendu->_ref_object->_guid}}', 1);
          } });
        }
      });
    });
  </script>
{{/if}}

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      Correspondants
      {{if $compte_rendu->_id}}
        <input type="text" name="_view" class="autocomplete"/>
        <div id="correspondants_area" style="color: #000; text-align: left; width: 35px;" class="autocomplete"></div>
      {{/if}}
    </th>
  </tr>
  {{foreach from=$destinataires key=_class item=_destinataires}}
    <tr>
      <th class="category" colspan="2">
        {{tr}}{{$_class}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_destinataires key=_index item=_destinataire}}
      {{assign var=object_guid value=$_destinataire->_guid_object}}
      {{assign var=tag value=$_destinataire->tag}}
      <tr>
        <td class="narrow">
          <input type="checkbox" name="_dest_{{$_class}}_{{$_index}}" id="editFrm__dest_{{$_class}}_{{$_index}}"
            {{if @isset($correspondants.$tag.$object_guid|smarty:nodefaults)}}checked="checked"{{/if}}/>
        </td>
        <td>
          <label for="editFrm__dest_{{$_class}}_{{$_index}}">
            {{$_destinataire->nom}} {{if $tag != "patient"}}({{tr}}CDestinataire.tag.{{$tag}}{{/tr}}){{/if}}
          </label>
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>
<p style="text-align: center;">
  <button type="button" class="tick" onclick="saveAndMerge();">Fusionner</button>
  <button type="button" class="cancel" onclick="Control.Modal.close();">Fermer</button>
</p>