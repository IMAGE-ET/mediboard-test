{{mb_script module=developpement script=regression_checker}}

<script type="text/javascript">
  Main.add(function () {
    PairEffect.initGroup('tree-content');
  });
</script>

<script type="text/javascript">
  Main.add(ViewPort.SetAvlHeight.curry('tree-files', 1));
</script>

<div>
  <h1>
    Rapport de non régression par vue
    ({{$count}} vues)
  </h1>
  <div id="tree-files">
    {{mb_include template=tree_regression_views dir=modules basename=modules views=$views}}
  </div>
</div>

