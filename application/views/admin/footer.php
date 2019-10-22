        </div>
      </div>
      
    </div>
	
    <!-- Bootstrap core JavaScript -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/js/bootstrap.js"></script>
    <script src="/js/holder.js"></script>
    <script src="/js/functions.js"></script>
    <script src="/js/dashboard.js"></script>
    <?php if(!empty($script_bottom)): 
        foreach ($script_bottom as $file): ?>
    <script src="/js/<?= $file; ?>"></script>
    <?php endforeach; 
    endif; ?>
    <!-- Put this into a custom JavaScript file to make things more organized -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
    });
    </script>
  </body>
</html>