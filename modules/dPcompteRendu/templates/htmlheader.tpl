<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <style type="text/css">
      {{$style|smarty:nodefaults}}
    </style>
  </head>
  <body>
  <script type="text/javascript">
    var pp = getPrintParams();
    //app.alert(pp.toSource());
    pp.reversePages = true;
    //pp.printerName="PDFCreator";
    pp.interactive = pp.constants.interactionLevel.full;
    
    
    print(pp);
    
  </script>
    {{$content|smarty:nodefaults}}
  </body>
</html>