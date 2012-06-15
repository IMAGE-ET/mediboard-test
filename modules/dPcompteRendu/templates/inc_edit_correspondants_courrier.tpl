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
  
  {{if $patient->_id}}
    {{mb_script module=patients script=correspondant ajax=true}}
  {{/if}}
{{/if}}

<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      <button type="button" class="add notext" style="float: left;"
        onclick="Correspondant.edit(0, '{{$patient->_id}}', openCorrespondants.curry('{{$compte_rendu->_id}}', '{{$compte_rendu->_ref_object->_guid}}', 0))"></button>
      Correspondants
      {{if $compte_rendu->_id}}
        <input type="text" name="_view" class="autocomplete"/>
        <div id="correspondants_area" style="color: #000; text-align: left; width: 35px;" class="autocomplete"></div>
      {{/if}}
    </th>
  </tr>
  {{foreach from=$destinataires key=_class item=_destinataires}}
    <tr>
      <th class="category" colspan="3">
        {{tr}}{{$_class}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_destinataires key=_index item=_destinataire}}
      {{assign var=object_guid value=$_destinataire->_guid_object}}
      {{assign var=tag value=$_destinataire->tag}}
      {{if @isset($correspondants.$tag.$object_guid|smarty:nodefaults)}}
        {{assign var=correspondant value=$correspondants.$tag.$object_guid}}
      {{else}}
        {{assign var=correspondant value=$empty_corres}}
      {{/if}}
      <tr>
        <td class="narrow">
          <input type="checkbox" name="_dest_{{$_class}}_{{$_index}}" id="editFrm__dest_{{$_class}}_{{$_index}}"
            {{if $correspondant->_id}}checked="checked"{{/if}}/>
        </td>
        <td>
          <label for="editFrm__dest_{{$_class}}_{{$_index}}">
            {{$_destinataire->nom}} ({{tr}}CDestinataire.tag.{{$tag}}{{/tr}})
          </label>
        </td>
        <td>
          <input type="text" name="_count_{{$_class}}_{{$_index}}" id="editFrm__count_{{$_class}}_{{$_index}}"
            value="{{$correspondant->quantite}}" style="width: 3em;"/>
          <script type="text/javascript">
            Main.add(function() {
              $('editFrm__count_{{$_class}}_{{$_index}}').addSpinner({min: 1});
            });
          </script>
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>
<p style="text-align: center;">
  <button type="button" class="tick" onclick="saveAndMerge();">Fusionner</button>
  <button type="button" class="cancel" onclick="Control.Modal.close();">Fermer</button>
</p>