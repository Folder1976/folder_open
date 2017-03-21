<footer id="footer"><?php echo $text_footer; ?><br /><?php echo $text_version; ?></footer></div>
</body></html>

<style>
    .links:hover{
        cursor: pointer;
    }
    .clearfix{
        display: inline;
        float: left;
    }
    .clearfix li{
        float: left;
        padding-right: 20px;
    }
</style>
<script>
    
    $(document).on('click', '.links', function(){
        location.href = $(this).data('link');
    });
    
    
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-66121438-1', 'auto');
  ga('send', 'pageview');

</script>