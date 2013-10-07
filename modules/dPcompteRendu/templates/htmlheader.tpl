<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <style type="text/css">
      {{$style|smarty:nodefaults}}
    </style>
  </head>
  <body>
    {{if $auto_print}}
      <script type="text/javascript">
        try {
          this.print();
        }
        catch(e){ }
      </script>
    {{/if}}
    {{$content|smarty:nodefaults}}
  </body>
</html>