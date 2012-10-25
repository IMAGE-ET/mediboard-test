{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}

<ul style="text-align: left;">
  {{foreach from=$packs item=_pack}}
    {{if $_pack->_owner == "user"}}
      {{if $_pack->user_id == $app->user_id}}
        {{assign var=owner_icon value="user-glow"}}
      {{else}}
        {{assign var=owner_icon value="user"}}
      {{/if}}
    {{elseif $_pack->_owner == "func"}}
      {{assign var=owner_icon value="user-function"}}
    {{else}}
      {{assign var=owner_icon value="group"}}
    {{/if}}
      
    <li data-modeles_ids="{{'|'|implode:$_pack->_modeles_ids}}">
      <img style="float: right; clear: both; margin: -1px;" 
        src="images/icons/{{$owner_icon}}.png" />
        {{if $_pack->fast_edit_pdf && $_pack->fast_edit && $pdf_thumbnails && $pdf_and_thumbs}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
        {{elseif $_pack->fast_edit}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
        {{/if}}
        {{if $_pack->fast_edit}}
          <img style="float: right;" src="images/icons/lightning.png"/>
        {{/if}}
      
      <div class="{{if $_pack->fast_edit}}fast_edit{{/if}} {{if !$_pack->merge_docs}}merge_docs{{/if}}">{{$_pack->nom|emphasize:$keywords}}</div>
      
      <div style="display: none;" class="id">{{$_pack->_id}}</div>
    </li>
  {{/foreach}}
</ul>