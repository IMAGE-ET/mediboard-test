{{mb_include_script module="dPmedicament" script="medicament_selector"}}


<form name="livretTherapeutique" action="?" method="get">
  <input type="text" name="nom_produit" />
  <button class="search notext" type="button" onclick="MedSelector.init()">Rechercher</button>
          
  <script type="text/javascript">   
    MedSelector.init = function(){
      this.sForm = "livretTherapeutique";
      this.sView = "nom_produit";
      this.pop();
    }
  </script>
        
        
</form>