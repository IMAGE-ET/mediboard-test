<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form"> 
  <tr>
    <th class="category" colspan="100">Systeme de facturation</th>
  </tr>
  <tr>
    {{assign var="var" value="systeme_facturation"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select class="enum list|siemens|t2a" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}">
        <option value="">Aucun</option>
        <option value="siemens" {{if $dPconfig.$m.$var == "siemens"}}selected="selected"{{/if}}>Siemens</option>
        <option value="t2a" {{if $dPconfig.$m.$var == "t2a"}}selected="selected"{{/if}}>T2A</option>
      </select>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>

</table>

</form>

<h2>Création et remplissage des la base des GHS / GHM</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=GHS1010}}

<script type="text/javascript">

var aActionTypes = ["extractFiles", "AddCM", "AddDiagCM", "AddActes", "AddGHM", "AddCMA", "AddIncomp", "AddArbre"];

function doGHSAction(sType, bFollowed) {
  var nType = aActionTypes.indexOf(sType);
  if (nType == null) {
    return;
  }

  var sNextType = aActionTypes[++nType];
  var oOptions = {
    onComplete : function () {
      doGHSAction(sNextType, true);
    }
  };
 
  if (!bFollowed) {
    oOptions.onComplete = null;
  }
  
  var url = new Url;
  url.setModuleAction("dPpmsi", "httpreq_do_ghs_action");
  url.addParam("type", sType);
  url.requestUpdate(sType, oOptions);
}

</script>
<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="category" onclick="doGHSAction('extractFiles', true);"><button class="tick">Go</button></th>
          <th class="category">Action</th>
          <th class="category">Résultat</th>
        </tr>
        <tr>
          <td onclick="doGHSAction('extractFiles');"><button class="tick">Go</button></td>
          <td>Extraction des fichiers</td>
          <td class="text" id="extractFiles"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddCM');"><button class="tick">Go</button></td>
          <td>Remplissage des Catégories Majeures</td>
          <td class="text" id="AddCM"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddDiagCM');"><button class="tick">Go</button></td>
          <td>Ajout des diagnostics d'entrée dans les CM</td>
          <td class="text" id="AddDiagCM"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddActes');"><button class="tick">Go</button></td>
          <td>Ajout des actes/diagnostics dans les listes</td>
          <td class="text" id="AddActes"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddGHM');"><button class="tick">Go</button></td>
          <td>Ajout des Groupements Homogènes de Malades</td>
          <td class="text" id="AddGHM"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddCMA');"><button class="tick">Go</button></td>
          <td>Ajout des Complications ou Morbidités Associées</td>
          <td class="text" id="AddCMA"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddIncomp');"><button class="tick">Go</button></td>
          <td>Ajout des incompatibilités DP - CMA</td>
          <td class="text" id="AddIncomp"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddArbre');"><button class="tick">Go</button></td>
          <td>Ajout de l'arbre de décision</td>
          <td class="text" id="AddArbre"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>