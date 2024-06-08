
  
!function (f, b, e, v, n, t, s) {
    if (f.fbq) return; n = f.fbq = function () {
        n.callMethod ?
        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
    };
    if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
    n.queue = []; t = b.createElement(e); t.async = !0;
    t.src = v; s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s)
}(window, document, 'script',
    'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', 'your-pixel-id');
fbq('track', 'PageView');



  
  
  window.fbAsyncInit = function () {
    // JavaScript SDK configuration and setup
    FB.init({
      appId: '1288932111973164', // Facebook App ID
      cookie: true, // enable cookies
      xfbml: true, // parse social plugins on this page
      version: 'v20.0' // Graph API version
    });
  };

  // Load the JavaScript SDK asynchronously
  (function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Facebook Login with JavaScript SDK
  function launchWhatsAppSignup() {
    // Conversion tracking code
    fbq && fbq('trackCustom', 'WhatsAppOnboardingStart', { appId: '1288932111973164', feature: 'whatsapp_embedded_signup' });

    // Launch Facebook login
    FB.login(function (response) {
      if (response.authResponse) {
        const code = response.authResponse.code;
        // The returned code must be transmitted to your backend,
        // which will perform a server-to-server call from there to our servers for an access token
      } else {
        console.log('User cancelled login or did not fully authorize.');
      }
    }, {
      config_id: '382513671461775', // configuration ID goes here
      response_type: 'code',    // must be set to 'code' for System User access token
      override_default_response_type: true, // when true, any response types passed in the "response_type" will take precedence over the default types
      extras: {
        setup: {
          // Prefilled data can go here
        }
      }
    });
  }

