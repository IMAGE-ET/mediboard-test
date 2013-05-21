<h2>Import de fichier V11 pour les {{tr}}{{$facture_class}}{{/tr}}</h2>
<div class="big-info">
  Téléversez un fichier v11.
</div>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="hidden" name="facture_class" value="{{$facture_class}}" />
  <input type="file" name="import" />
  <input type="checkbox" name="dryrun" value="1" checked="checked" />
  <label for="dryrun">Essai à blanc</label>
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="13">{{$results|@count}} règlements trouvées</th>
    </tr>
    <tr>
      <th>Etat</th>
      <th>Genre</th>
      <th>Numéro client</th>
      <th>Référence</th>
      <th>Montant</th>
      <th>Référence de dépot</th>
      <th>Date de dépot</th>
      <th>Date de traitement</th>
      <th>Date d'inscription</th>
      <th>Numéro microfilm</th>
      <th>Code rejet</th>
      <th>Réserve</th>
      <th>Prix</th>
    </tr>
    {{foreach from=$results item=_reglement}}
      <tr>
        {{if count($_reglement.errors)}}
          <td class="text warning compact">
            {{foreach from=$_reglement.errors item=_error}}
              <div>{{$_error}}</div>
            {{/foreach}}
          </td>
        {{else}}
          <td class="text ok">
            OK
          </td>
        {{/if}}
        <td class="text">{{$_reglement.genre}}</td>
        <td class="text">{{$_reglement.num_client}}</td>
        <td class="text">{{$_reglement.reference}}</td>
        <td class="text">{{$_reglement.montant}}</td>
        <td class="text">{{$_reglement.ref_depot}}</td>
        <td class="text">{{$_reglement.date_depot}}</td>
        <td class="text">{{$_reglement.date_traitement}}</td>
        <td class="text">{{$_reglement.date_inscription}}</td>
        <td class="text">{{$_reglement.num_microfilm}}</td>
        <td class="text">{{$_reglement.code_rejet}}</td>
        <td class="text">{{$_reglement.reserve}}</td>
        <td class="text">{{$_reglement.prix}}</td>
      </tr>
    {{/foreach}}
  </table>
{{/if}}