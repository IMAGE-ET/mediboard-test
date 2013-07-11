
<h2>Environnement d'execution</h2>

<form name="editCIncrementer" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />

  <table class="form">
    {{assign var=class value=CIncrementer}}
    <tr>
      <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_str var=cluster_count}}
    <tr>
      <th>
        <label title="{{tr}}config-dPsante400-CIncrementer-cluster_position-desc{{/tr}}">
          {{tr}}config-dPsante400-CIncrementer-cluster_position{{/tr}}
        </label>
      </th>
      <th>
        <div class="small-info">
          La position dans le cluster doit être définie dans le fichier <code>config_overload.php</code>,
          <br />
          A la position : <code>dPsante400 CIncrementer cluster_position</code>
          <br />
          Elle est définie à <strong>{{$conf.dPsante400.CIncrementer.cluster_position}}</strong> sur ce serveur.
        </div>
      </th>
    </tr>

    <tr>
      <td class="button" colspan="6">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>

</form>