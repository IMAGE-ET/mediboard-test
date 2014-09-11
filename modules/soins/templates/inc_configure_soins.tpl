<script>
  redirectOffline = function(type, embed) {
    switch(type) {
      case 'bilan':
        var url = new Url("soins", "offline_bilan_service");
        url.addParam("dialog", 1);
        break;
      case 'plan':
        var url = new Url("soins", "offline_plan_soins");
        url.addParam("dialog", 1);
        break;
      case 'sejour':
        var url = new Url("soins", "offline_sejours");
        url.addParam("dialog", 1);
        break;
      case 'ordonnances':
        var url = new Url("soins", "offline_prescriptions_multipart", "raw");
        url.addParam("dialog", 0);
    }

    url.addParam("service_id", $("service_id").value);
    url.addParam("g", '{{$g}}');

    if (embed) {
      url.addParam("embed", 1);
      url.addParam("_aio", "savefile");
      url.pop(500, 400, "Vue embarquée");
    }
    else {
      url.redirect();
    }
  }
  checkTasks = function(type) {
    var url = new Url('soins', 'ajax_purge_tasks');
    url.addParam('type', type);
    url.requestUpdate('purge_tasks');
  }
</script>

<table class="form">
  <col style="width: 50%" />
  <tr>
    <th class="category" colspan="2">
      {{tr}}Offline{{/tr}}
    </th>
  </tr>
  <tr>
    <td style="text-align: right">
      {{tr}}CChambre-service_id{{/tr}} :
      <select id="service_id">
        {{foreach from=$services item=_service}}
          <option value='{{$_service->_id}}'>{{$_service->nom}}</option>
        {{/foreach}}
      </select>
    </td>
    <td>
      <button type="button" class="search" onclick="redirectOffline('sejour');">{{tr}}CSejour.all{{/tr}}</button>
      <button type="button" class="download" onclick="redirectOffline('sejour', true);">{{tr}}Download{{/tr}} {{tr}}CSejour.all{{/tr}}</button>
      {{if "dPprescription"|module_active}}
        <br/>
        <button type="button" class="search" onclick="redirectOffline('bilan');">{{tr}}CService.bilan{{/tr}}</button>
        <br />
        <button type="button" class="search" onclick="redirectOffline('plan');">{{tr}}CService.plan_soins{{/tr}}</button>
        <br />
        <button type="button" class="search" onclick="redirectOffline('ordonnances');">{{tr}}CService.ordonnances{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
  <tr>
    <th class="category" colspan="2">
      {{tr}}Tools{{/tr}}
    </th>
  </tr>
  <tr>
    <td>
      <button type="button" class="search" onclick="checkTasks('check');">Voir les taches réalisées sans auteur</button><br/>
      <button type="button" class="change" onclick="checkTasks('repair');">Corriger les taches réalisées sans auteur (par 100)</button>
    </td>
    <td id="purge_tasks"></td>
  </tr>
</table>