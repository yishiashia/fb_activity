<div id="profile_content">
<h1>嗨! 請先登入以進行留言</h1><span class="fb-name-tmp">
<p><a href="#" class="fb_login_link" onClick="logInWithFacebook()"></a></p>

<script>
  logInWithFacebook = function() {
    FB.login(function(response) {
      if (response.authResponse) {
        window.location = "/fbactivity/login";
      } else {
        alert('User cancelled login or did not fully authorize.');
      }
    });
    return false;
  };
  window.fbAsyncInit = function() {
    FB.init({
      appId: '<?php echo $fb_appid; ?>',
      cookie: true,
      version: 'v2.5'
    });
  };

  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
</div>
<hr>
