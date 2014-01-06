<script>
  {{assign var=timer value="CMbPerformance::out"|static_call:""}}
  {{mb_default var=dosql value=''}}

  // Perfomance log
  (function(){
    var offset = (performance.timing.responseEnd - performance.timing.fetchStart);
    MbPerformance.log.defer("page", "{{$m}}|{{$dosql|ternary:$dosql:$a}}", {{$timer|smarty:nodefaults}}, 0, offset);
  })();
</script>

</body>
</html>