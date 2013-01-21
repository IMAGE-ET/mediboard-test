<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-code', true);
  });

  showDetail = function(code, object_class) {
    var url = new Url("dPcim10", "ajax_vw_detail_cim10");
    url.addParam("codeacte", code);
    url.addParam("object_class", "{{$object_class}}");
    url.requestModal(500,300);
  }
</script>

<style type="text/css">
em {
  text-decoration: underline;
}
</style>

<ul id="tabs-code" class="control_tabs">
{{foreach from=$listByProfile key=profile item=list}}
  {{assign var=user value=$users.$profile}}
  <li>
    <a href="#{{$profile}}_code" {{if !$list.list|@count}}class="empty"{{/if}}>
      {{tr}}Profile.{{$profile}}{{/tr}} 
      {{$user->_view}} ({{$list.list|@count}})
    </a>
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$listByProfile key=profile item=_profile}}
  {{assign var=list         value=$_profile.list}}
  {{assign var=list_favoris value=$_profile.favoris}}
  {{assign var=list_stats   value=$_profile.stats}}
  <div id="{{$profile}}_code" style="display: none; height: 110%; overflow-y: scroll;">
    <table class="tbl">
      <tr>
        <th>Code</th>
        <th>Libellé</th>
        {{if !$tag_id}}
          <th></th>
        {{/if}}
        <th></th>
      </tr>
      {{foreach from=$list item=curr_code name=fusion}}
        <tr>
          <td>
            <button type="button" class="save notext compact" onclick="addCIM10Favori('{{$curr_code->code}}');"
              {{if $can->edit && array_key_exists($curr_code->code, $list_favoris)}}disabled="disabled"{{/if}}>
              Ajouter aux favoris
            </button>
            <span>{{$curr_code->code}}</span>
          </td>
          <td class="text">{{$curr_code->libelle|emphasize:$_keywords_code}}</td>
          {{if !$tag_id}}
            <td>
              {{if array_key_exists($curr_code->code, $list_stats)}}
                {{assign var=_code value=$curr_code->code}}
                {{$list_stats.$_code->occ}}
              {{elseif array_key_exists($curr_code->code, $list_favoris)}}
                Favoris
              {{else}}
                -
              {{/if}}
            </td>
          {{/if}}
          <td class="narrow">
            <button type="button" class="tick compact" onclick="CIM10Selector.set('{{$curr_code->code}}'); Control.Modal.close();">Sélectionner</button>
          </td>
        </tr>
      {{foreachelse}}
        <tr>
          <td class="empty" colspan="4">{{if $_all_codes}}Aucun code{{else}}Aucun favori{{/if}}</td>
        </tr>
      {{/foreach}}
    </table>
  </div>
{{/foreach}}
