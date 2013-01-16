<script>
Main.add(Control.Tabs.create.curry('tabs-owner', true));
</script>

<ul id="tabs-owner" class="control_tabs">
  <li>
    <a href="#owner-user" {{if !$packs.user|@count}}class="empty"{{/if}}>
      {{$user}} <small>({{$packs.user|@count}})</small>
    </a>
  </li>
  <li>
    <a href="#owner-func" {{if !$packs.func|@count}}class="empty"{{/if}}>
      {{$user->_ref_function}} <small>({{$packs.func|@count}})</small>
    </a>
  </li>
  <li>
    <a href="#owner-etab" {{if !$packs.etab|@count}}class="empty"{{/if}}>
      {{$user->_ref_function->_ref_group}} <small>({{$packs.etab|@count}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<table class="tbl">
  <tr>
    <th style="width: 12em;">{{mb_label class=CPack field=nom}}</th>
    <th style="width: 16em;">Modèles</th>
    <th style="width:  8em;">Type</th>
  </tr>
  
  {{foreach from=$packs item=packs_by_owner key=owner}}
    <tbody id="owner-{{$owner}}" style="display: none">
      {{foreach from=$packs_by_owner item=_pack}}
      <tr id="{{$_pack->_guid}}">
        {{assign var=header value=$_pack->_header_found}}
        {{assign var=footer value=$_pack->_footer_found}}
        <td class="text">
          
          {{if $_pack->fast_edit_pdf}}
            <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
          {{elseif $_pack->fast_edit}}
            <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
          {{/if}}
          {{if $_pack->fast_edit || $_pack->fast_edit_pdf}}
            <img style="float: right;" src="images/icons/lightning.png"/>
          {{/if}}
          
          <button class="edit notext" onclick="Pack.edit('{{$_pack->_id}}');">{{tr}}Modify{{/tr}}</button>
          {{$_pack}}
          <div class="compact">
            {{if $header->_id}}
              <div>
                Entête : <span onmouseover="ObjectTooltip.createEx(this, '{{$header->_guid}}')">{{$header->nom}}</span>
              </div>
            {{/if}}
            {{if $footer->_id}}
              <div>
                Pied de page : <span onmouseover="ObjectTooltip.createEx(this, '{{$footer->_guid}}')">{{$footer->nom}}</span>
              </div>
            {{/if}}
          </div>
        </td>
        <td class="text">
          {{foreach from=$_pack->_back.modele_links item=_link name=links}}
            {{if $smarty.foreach.links.index < 5}}
              <div class="compact">{{$_link|spancate:60}}</div>
            {{/if}}
          {{foreachelse}}
          <div class="empty">{{tr}}CPack-back-modele_links.empty{{/tr}}</div>
          {{/foreach}}
          {{if $_pack->_back.modele_links|@count > 5}}
            <div class="compact">
              <strong>
                + {{math equation="x-5" x=$_pack->_back.modele_links|@count}} {{tr}}others{{/tr}}
              </strong>
            </div>
          {{/if}}
        </td>
        <td class="text">{{tr}}{{$_pack->object_class}}{{/tr}}</td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="10" class="empty">{{tr}}CPack.none{{/tr}}</td>
      </tr>
      {{/foreach}}
    </tbody>
  {{/foreach}}
</table>