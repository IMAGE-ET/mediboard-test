<script type="text/javascript">
CacheTester = {
  users: function(purge) {
    new Url('developpement', 'cache_tester_users') .
      addParam('purge', purge) .
      requestUpdate('users');
  },

  metamodel: function() {
    new Url('developpement', 'cache_tester_metamodel') .
      requestUpdate('metamodel');
  }
}

Main.add(function() {
  Control.Tabs.create('tabs-tests', true).activeLink.onmouseup();
});
</script>


<ul id="tabs-tests" class="control_tabs">
  <li><a href="#users"     onmouseup="CacheTester.users();">Utilisateurs et fonctions</a></li>
  <li><a href="#metamodel" onmouseup="CacheTester.metamodel();">Métamodèle</a></li>
</ul>

<hr class="control_tabs" />

<div id="users" style="display: none;">
</div>

<div id="metamodel" style="display: none;">
</div>