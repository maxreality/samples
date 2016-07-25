
<?php

/*
Template Name: Sprout Takeover
*/

wp_enqueue_script( 'make-sproutgrid', get_stylesheet_directory_uri() . '/version-2/js/sproutgrid.js', array( 'jquery' ), false, true );
require_once 'version-2/includes/Mobile_Detect.php';
$detect = new Mobile_Detect;
$device = 'pc';
if ( $detect->isMobile() ) {
  $device = 'mobile';
}
if( $detect->isTablet() ){
  $device = 'tablet';
}

get_header( 'version-2' );

?>
<div class="container">
  <div class="row sprout-sponsored-row"><h2 class="sponsored">SPROUT BY HP</h2><p class="sponsored">sponsored</p></div>
  <div class="row"><img src="<?php echo get_template_directory_uri(); ?>/images/sproutBanner.jpg" alt="Sprout Banner" class="img-responsive" width="100%" /></div>
</div>
<div class="all-projects <?php echo $device ?> all-projects-sprout">
  <div class="content container">
    <div class="posts-list container">
      <?php sorting_posts_sprout(); //TODO Rename Function ?>  
    </div>
  </div>
  <div id="temp_post_list" style="display: none"></div>
</div>

<div class="container shed-row shed-row-sprout"> 
  <!-- HP Items Feed -->
  <?php echo make_shopify_featured_products_slider_sprout( 'row-fluid' ); ?>
</div>

<div class="container sprout-container">
  <div class="sprout-margins">
    <div class="row post_row home-sprout-row"> 
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/nHyaYb7RkqA?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/nHyaYb7RkqA/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
            <p class="sprout-video-caption">Build an Air Powered Rocket using Sprout by HP</p>
        </div>
      </div> 
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/W3zJCG_JtfI?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/W3zJCG_JtfI/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
        <p class="sprout-video-caption">3D Capture Stage is a special turntable that allows true 3d scanning using 3d Object Capture.</p>
        </div>
      </div>
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/HyWn4_22cN8?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/HyWn4_22cN8/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
            <p class="sprout-video-caption">Our edit team checks in with the Sprout team to see how the platform is inspiring makers to learn and create.</p>
        </div>
      </div>      
    </div><!-- end sprout-video-row -->
    <div class="row post_row home-sprout-row">
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/wYP_UIBQExk?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/wYP_UIBQExk/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
            <p class="sprout-video-caption">Designers and developers describe their first experiences with the Sprout and its potential to transform the way digital media is created.</p>
        </div>
      </div>      
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/re7xpfUuCXE?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/re7xpfUuCXE/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
        <p class="sprout-video-caption">The story of how Sprout by HP was used to create a one-of-a-kind cast cover and made a young boy feel like a superhero.</p>
        </div>
      </div>
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/Tw5v00RAqow?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/Tw5v00RAqow/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
            <p class="sprout-video-caption">HP's Sprout division and Dremel move toward a full-cycle approach to 3D printing and scanning.</p>
        </div>
      </div>
    </div><!-- end sprout-video-row -->
    <div class="row post_row home-sprout-row">
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="http://www.youtube.com/embed/icKZ_ND9p0Q?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/icKZ_ND9p0Q/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
            <p class="sprout-video-caption">Sprout is an all-new computer interface that embraces creativity and accessibility. The creator, Brad Short, gives a feature tour.</p>
        </div>
      </div>  
      <div class="post col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sprout-video">
            <a class="fancytube fancybox.iframe" href="https://www.youtube.com/embed/LETmOKDkH-A?autoplay=1">
              <img class="img-responsive" src="http://img.youtube.com/vi/LETmOKDkH-A/mqdefault.jpg" alt="MakerCon Conference Videos" height="180" width="100%" />
              <img class="yt-play-btn" src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-btn.png" alt="Youtube overlay play button" />
            </a>
        <p class="sprout-video-caption">Inspired by family mementos, this set of grandparents uses the HP Sprout to create a unique and lovely mobile for their baby granddaughter.</p>
        </div>
      </div>
    </div><!-- end sprout-video-row -->  

  </div>
</div>

      <?php echo do_shortcode( '[show_twitter_instagram]' ); ?>

<?php get_footer(); ?>

<script>
    (function(v,n){"function"===typeof define&&define.amd?define([],n):"object"===typeof exports?module.exports=n():n()})(this,function(){function v(a){return a.replace(/<b[^>]*>(.*?)<\/b>/gi,function(a,f){return f}).replace(/class=".*?"|data-query-source=".*?"|dir=".*?"|rel=".*?"/gi,"")}function n(a){a=a.getElementsByTagName("a");for(var c=a.length-1;0<=c;c--)a[c].setAttribute("target","_blank")}function m(a,c){for(var f=[],g=new RegExp("(^| )"+c+"( |$)"),h=a.getElementsByTagName("*"),b=0,k=h.length;b<
    k;b++)g.test(h[b].className)&&f.push(h[b]);return f}var A="",k=20,B=!0,t=[],w=!1,u=!0,q=!0,x=null,y=!0,C=!0,z=null,D=!0,E=!1,r=!0,F={fetch:function(a){void 0===a.maxTweets&&(a.maxTweets=20);void 0===a.enableLinks&&(a.enableLinks=!0);void 0===a.showUser&&(a.showUser=!0);void 0===a.showTime&&(a.showTime=!0);void 0===a.dateFunction&&(a.dateFunction="default");void 0===a.showRetweet&&(a.showRetweet=!0);void 0===a.customCallback&&(a.customCallback=null);void 0===a.showInteraction&&(a.showInteraction=!0);
        void 0===a.showImages&&(a.showImages=!1);void 0===a.linksInNewWindow&&(a.linksInNewWindow=!0);if(w)t.push(a);else{w=!0;A=a.domId;k=a.maxTweets;B=a.enableLinks;q=a.showUser;u=a.showTime;C=a.showRetweet;x=a.dateFunction;z=a.customCallback;D=a.showInteraction;E=a.showImages;r=a.linksInNewWindow;var c=document.createElement("script");c.type="text/javascript";c.src="//cdn.syndication.twimg.com/widgets/timelines/"+a.id+"?&lang="+(a.lang||"en")+"&callback=twitterFetcher.callback&suppress_response_codes=true&rnd="+
        Math.random();document.getElementsByTagName("head")[0].appendChild(c)}},callback:function(a){var c=document.createElement("div");c.innerHTML=a.body;"undefined"===typeof c.getElementsByClassName&&(y=!1);a=[];var f=[],g=[],h=[],b=[],p=[],e=0;if(y)for(c=c.getElementsByClassName("tweet");e<c.length;){0<c[e].getElementsByClassName("retweet-credit").length?b.push(!0):b.push(!1);if(!b[e]||b[e]&&C)a.push(c[e].getElementsByClassName("e-entry-title")[0]),p.push(c[e].getAttribute("data-tweet-id")),f.push(c[e].getElementsByClassName("p-author")[0]),
        g.push(c[e].getElementsByClassName("dt-updated")[0]),void 0!==c[e].getElementsByClassName("inline-media")[0]?h.push(c[e].getElementsByClassName("inline-media")[0]):h.push(void 0);e++}else for(c=m(c,"tweet");e<c.length;)a.push(m(c[e],"e-entry-title")[0]),p.push(c[e].getAttribute("data-tweet-id")),f.push(m(c[e],"p-author")[0]),g.push(m(c[e],"dt-updated")[0]),void 0!==m(c[e],"inline-media")[0]?h.push(m(c[e],"inline-media")[0]):h.push(void 0),0<m(c[e],"retweet-credit").length?b.push(!0):b.push(!1),e++;
        a.length>k&&(a.splice(k,a.length-k),f.splice(k,f.length-k),g.splice(k,g.length-k),b.splice(k,b.length-k),h.splice(k,h.length-k));c=[];e=a.length;for(b=0;b<e;){if("string"!==typeof x){var d=g[b].getAttribute("datetime"),l=new Date(g[b].getAttribute("datetime").replace(/-/g,"/").replace("T"," ").split("+")[0]),d=x(l,d);g[b].setAttribute("aria-label",d);if(a[b].innerText)if(y)g[b].innerText=d;else{var l=document.createElement("p"),G=document.createTextNode(d);l.appendChild(G);l.setAttribute("aria-label",
            d);
            g[b]=l}else g[b].textContent=d}d="";B?(r&&(n(a[b]),q&&n(f[b])),q&&(d+='<h2 class="user" style="margin-bottom:0px;">'+v(f[b].innerHTML)+"</h2>"),u&&(d+='<h2 class="timePosted">'+g[b].getAttribute("aria-label")+"</h2>",d+='<p class="tweet">'+v(a[b].innerHTML)+"</p>")):a[b].innerText?(q&&(d+='<p class="user">'+f[b].innerText+"</p>"),d+='<p class="tweet">'+a[b].innerText+"</p>",u&&(d+='<p class="timePosted">'+g[b].innerText+"</p>")):(q&&(d+='<p class="user">'+f[b].textContent+"</p>"),d+='<p class="tweet">'+a[b].textContent+
        "</p>",u&&(d+='<p class="timePosted">'+g[b].textContent+"</p>"));
            D&&(d+='<p class="interact" style="display: none;"><a href="https://twitter.com/intent/tweet?in_reply_to='+p[b]+'" target="_blank" class="twitter_reply_icon"'+(r?' target="_blank">':">")+'Reply</a><a href="https://twitter.com/intent/retweet?tweet_id='+p[b]+'" target="_blank" class="twitter_retweet_icon"'+(r?' target="_blank">':">")+'Retweet</a><a href="https://twitter.com/intent/favorite?tweet_id='+p[b]+'" target="_blank" class="twitter_fav_icon"'+(r?' target="_blank">':">")+"Favorite</a></p>");E&&void 0!==
            h[b]&&(l=h[b],void 0!==l?(l=l.innerHTML.match(/data-srcset="([A-z0-9%_\.-]+)/i)[0],l=decodeURIComponent(l).split('"')[1]):l=void 0,d+='<div class="media"><img src="'+l+'" alt="Image from tweet" /></div>');c.push(d);b++}if(null===z){a=c.length;f=0;g=document.getElementById(A);for(h="<div>";f<a;)h+="<div>"+c[f]+"</div>",f++;g.innerHTML=h+"</div>"}else z(c);w=!1;0<t.length&&(F.fetch(t[0]),t.splice(0,1))}};return window.twitterFetcher=F});

    var config1 = {
        "id": '691747938259763201',
        "domId": 'recent-twitter',
        "maxTweets": 4,
        "enableLinks": true
    };
    twitterFetcher.fetch(config1);
    
</script>