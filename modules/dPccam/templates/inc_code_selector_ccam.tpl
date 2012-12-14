<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-code', true);
  });

  showDetail = function(code, object_class) {
    var url = new Url("dPccam", "ajax_vw_detail_cccam");
    url.addParam("codeacte", code);
    url.addParam("object_class", "{{$object_class}}");
    url.requestModal(600, 400);
  }
  
  addMultiples = function() {
    var div = $("code_area");
    var inputs = div.select(".multiples_codes[checked]");
    if (inputs.length) {
      CCAMSelector.setMulti(inputs);
      Control.Modal.close();
    }
  }
</script>

<style type="text/css">
em {
  text-decoration: underline;
}
</style>

{{assign var=multiple_select value=$app->user_prefs.multiple_select_ccam}}

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
        <th>Tarifs</th>
        <th>Occurences</th>
        <th class="narrow"></th>
      </tr>
      {{foreach from=$list item=curr_code name=fusion}}
        <tr>
          <td style="background-color: #{{$curr_code->couleur}}">
            <button type="button" class="search notext compact" onclick="showDetail('{{$curr_code->code}}');">Détail</button>
            {{$curr_code->code}}
          </td>
          <td class="text compact">{{$curr_code->libelleLong|emphasize:$_keywords_code}}</td>
          <td class="compact" style="cursor: pointer">
            {{foreach from=$curr_code->activites item=_activite}}
              {{foreach from=$_activite->phases item=_phase}}
                 {{if $_phase->tarif}}
                 <div title="activité {{$_activite->numero}}, phase {{$_phase->phase}}">
                   {{$_phase->tarif|currency}}
                 </div>
                 {{/if}}
              {{/foreach}}
            {{/foreach}}
          </td>
          <td>
            {{if array_key_exists($curr_code->code, $list_stats)}}
              {{assign var=_code value=$curr_code->code}}
              {{$list_stats.$_code.nb_acte}}
            {{elseif array_key_exists($curr_code->code, $list_favoris)}}
              Favoris
            {{else}}
              -
            {{/if}}
          </td>
          <td>
            {{if $multiple_select}}
              <input type="checkbox" class="multiples_codes" value="{{$curr_code->code}}"/>
            {{else}}
              <button type="button" class="tick compact" onclick="CCAMSelector.set('{{$curr_code->code}}', '{{$curr_code->_default}}'); Control.Modal.close();">Sélectionner</button>
            {{/if}}
          </td>
        </tr>
      {{foreachelse}}
        <tr>
          <td class="empty" colspan="4">{{if $_all_codes}}Aucun code{{else}}Aucun favori / statistique{{/if}} </td>
        </tr>
      {{/foreach}}
    </table>
  </div>
{{/foreach}}
