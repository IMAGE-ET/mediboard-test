{literal}
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
  
  var AddUrl = new Url;
  AddUrl.setModuleAction("dPpmsi", "httpreq_do_ghs_action");
  AddUrl.addParam("type", sType);
  AddUrl.requestUpdate(sType, oOptions);
}

</script>
{/literal}

<h2>Création et remplissage des la base des GHS / GHM</h2>

<table class="main">
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" onclick="doGHSAction('extractFiles', true);"><button>Go</button></th>
          <th class="category">Action</th>
          <th class="category">Résultat</th>
        </tr>
        <tr>
          <td onclick="doGHSAction('extractFiles');"><button>Go</button></td>
          <td>Extraction des fichiers</td>
          <td class="text" id="extractFiles"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddCM');"><button>Go</button></td>
          <td>Remplissage des Catégories Majeures</td>
          <td class="text" id="AddCM"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddDiagCM');"><button>Go</button></td>
          <td>Ajout des diagnostics d'entrée dans les CM</td>
          <td class="text" id="AddDiagCM"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddActes');"><button>Go</button></td>
          <td>Ajout des actes/diagnostics dans les listes</td>
          <td class="text" id="AddActes"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddGHM');"><button>Go</button></td>
          <td>Ajout des Groupements Homogènes de Malades</td>
          <td class="text" id="AddGHM"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddCMA');"><button>Go</button></td>
          <td>Ajout des Complications ou Morbidités Associées</td>
          <td class="text" id="AddCMA"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddIncomp');"><button>Go</button></td>
          <td>Ajout des incompatibilités DP - CMA</td>
          <td class="text" id="AddIncomp"></td>
        </tr>
        <tr>
          <td onclick="doGHSAction('AddArbre');"><button>Go</button></td>
          <td>Ajout de l'arbre de décision</td>
          <td class="text" id="AddArbre"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>