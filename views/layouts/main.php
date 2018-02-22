<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="utf-8"> 
       <!-- google login -->
<script src="https://apis.google.com/js/platform.js" async defer></script>
<meta name="google-signin-client_id" access_type="offline" content="1034355052420-6sgas8fapqm2j7hvaiokrcrgekt68n4q.apps.googleusercontent.com">
    <!-- google login ends -->

    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TVFR5LQ');</script>
<!-- End Google Tag Manager -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-110422585-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-110422585-1');
</script>

    <meta charset="ISO-8859-1">

    <title><?php echo $template['title'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />

<?php
// if (strpos($_SERVER['HTTP_HOST'],'www.') !== false) {
// $a= str_replace("www.","",$_SERVER['HTTP_HOST']);
// $redirect= $a.$_SERVER['REQUEST_URI'];
//     header('Location:https://'.$redirect);
//   }

// $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// if($_SERVER["HTTPS"]!="on") {
//     header('Location:'.$redirect);
//  } 
 



?>
    <!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
 fbq('init', '374299199690185'); 
fbq('track', 'PageView');
</script>

<!-- End Facebook Pixel Code -->

    <?php echo $template['metadata'] ?> 
    <link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <?php echo $template['partials']['header']; ?>
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
        function hideURLbar(){ window.scrollTo(0,1); } </script>

    <script type="text/javascript">
    $(document).ready(function($) {
        $(".scroll").click(function(event){     
            event.preventDefault();
            $('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
        });
    });
    </script>
  </head>
<body class="skin-black <?php echo $body_class ?>">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TVFR5LQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <?php date_default_timezone_set('Asia/Kuala_Lumpur'); ?>

    <div class="wrapper row-offcanvas row-offcanvas-left" style="margin-top: 6%">
        <?php echo $template['partials']['flash_messages']; ?>
         <?php echo $template['body'];?> 
        <?php echo $template['partials']['footer']; ?>
</body>
  <?php
   // }
    ?>

<style type="text/css">
  #preloader { position: fixed; left: 0; top: 0; z-index: 999; width: 100%; height: 100%; overflow: visible; background: rgba(51, 51, 51, 0.39) url(http://localhost/biggle/SevenApp/web2/assets/img/favicon.png ) no-repeat center center; background-size: 100px 110px; }
</style>
</html>
<div id="preloaders"></div>
<script type="text/javascript">
  jQuery(document).ready(function($) {  
$(window).load(function(){
  $('#preloader').fadeOut('slow',function(){$(this).remove();});
});
});
</script>