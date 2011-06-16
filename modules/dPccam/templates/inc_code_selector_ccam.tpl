<script type="text/javascript">
  Main.add(function() {
    new Control.Tabs('tabs-code', true);
  });

  showDetail = function(code, object_class) {
    var url = new Url("dPccam", "ajax_vw_detail_cccam");
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
    <a href="#{{$profile}}_code">
      {{tr}}Profile.{{$profile}}{{/tr}} 
      {{$user->_view}}
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
    <th>
      Code
    </th>
    <th>
      Libellé
    </th>
    <th>
      Occurences
    </th>
    <th>
    </th>
    </tr>
    {{foreach from=$list item=curr_code name=fusion}}
      <tr>
        <td style="background-color: #{{$curr_code->couleur}}">
          <button type="button" class="search notext" onclick="showDetail('{{$curr_code->code}}');">Détail</button>
          {{$curr_code->code}}
        </td>
        <td class="text">{{$curr_code->libelleLong|emphasize:$_keywords_code}}</td>
        <td>
          {{if array_key_exists($curr_code->code, $list_favoris)}}
            Favoris
          {{elseif array_key_exists($curr_code->code, $list_stats)}}
            {{assign var=_code value=$curr_code->code}}
            {{$list_stats.$_code.nb_acte}}
          {{else}}
            -
          {{/if}}
        </td>
        <td>
          <button type="button" class="tick" onclick="CCAMSelector.set('{{$curr_code->code}}', '{{$curr_code->_default}}'); Control.Modal.close();">Sélectionner</button>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="4">Aucun code</td>
      </tr>
    {{/foreach}}
    </table>
  </div>
{{/foreach}}
