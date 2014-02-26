<script>
Main.add(function() {
  Datamining.auto.delay(5);
});
</script>

<h2>{{tr}}Datamining{{/tr}}</h2>

<div class="small-info">
  Ce tableau de bord permet de connaitre le statut du datamining des opérations,
  permettant l'extraction de statistiques d'activité à hautes performances.
</div>

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="3">Décomptes des opérations</th>
  </tr>

  <tr>
    <th style="width: 33%;">Toutes</th>
    <th style="width: 33%;">A miner (< J)</th>
    <th style="width: 33%;">A consolider (< J-28)</th>
  </tr>

  <tr>
    <td><strong>{{$counts.overall    }}</strong></td>
    <td><strong>{{$counts.tobemined  }}</strong></td>
    <td><strong>{{$counts.toberemined}}</strong></td>
  </tr>
</table>

<hr />

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="10">
      <span style="float: right;">
        <label>
          <input type="checkbox" id="automine" {{if $automine}} checked="checked" {{/if}} />
          auto
        </label>
        par
        <input type="text" id="limit" size="4" value="{{$limit}}">
      </span>
      Statut des explorateurs d'opération
    </th>
  </tr>
  <tr>
    <th>Mineur</th>
    <th>Dernière exploration</th>
    <th colspan="2">Non encore explorées</th>
    <th colspan="2">Non encore consolidées</th>
  </tr>

  {{foreach from=$miners item=_miner}}
  <tr>
    <td>
      <strong>{{tr}}{{$_miner->_class}}{{/tr}}</strong>
    </td>
    <td>
      {{mb_value object=$_miner field=date}}
    </td>
    <td>
      {{$_miner->_count_unmined}}
    </td>
    <td class="narrow">
      <button type="button" class="change notext compact oneclick" onclick="Datamining.mine('{{$_miner->_class}}', 0)">
        {{tr}}Do{{/tr}}
      </button>
    </td>
    <td>
      {{$_miner->_count_unremined}}
    </td>
    <td class="narrow">
      <button type="button" class="change notext compact oneclick" onclick="Datamining.mine('{{$_miner->_class}}', 1)">
        {{tr}}Do{{/tr}}
      </button>
    </td>
  </tr>

  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}COperationMiner.none{{/tr}}<</td>
  </tr>
  {{/foreach}}

</table>
