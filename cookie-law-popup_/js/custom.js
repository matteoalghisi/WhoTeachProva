 if ($('html').is(':lang(it)')) {

$(document).euCookieLawPopup().init({
  cookiePolicyUrl : '/?cookie-policy',
  popupPosition : 'bottom',
  colorStyle : 'default',
  compactStyle : false,
  popupTitle : 'Il sito fa uso di cookie',
  popupText :  "I cookie sono file di testo che vengono registrati sul terminale dell\'utente oppure che consentono l\'accesso ad informazioni sul terminale dell\'utente."+
  "I cookie permettono di conservare informazioni sulle preferenze dei visitatori, sono utilizzati al fine di verificare il corretto funzionamento del sito e di migliorarne le"+
 ' funzionalità personalizzando il contenuto delle pagine in base al tipo del browser utilizzato, oppure per semplificarne la navigazione automatizzando le procedure '+
 '(es. Login, lingua sito), ed infine per l\'analisi dell\'uso del sito da parte dei visitatori. '+
 'Per ulteriori informazioni sui cookie, consultare <a class="privacy" href="https://www.whoteach.eu/lms/policy/privacy_policy.html">l\'informativa sulla privacy</a> e la <a class="privacy" href="https://www.whoteach.eu/lms/policy/cookie_policy.html">cookie policy</a>. '+
 '</br></br> <p class= "abc">Se non si accetta l\'utilizzo, non verrà tenuta traccia del comportamento durante visita, ma verrà utilizzato un unico cookie nel browser per ricordare che si è scelto di non'+
' registrare informazioni sulla navigazione.</p>',
  buttonContinueTitle : 'Accetta',
  agreementExpiresInDays : 100,
  autoAcceptCookiePolicy : false,
  htmlMarkup : null
});

}else if ($('html').is(':lang(en)')) {

$(document).euCookieLawPopup().init({
  cookiePolicyUrl : '/?cookie-policy',
  popupPosition : 'bottom',
  colorStyle : 'default',
  compactStyle : false,
  popupTitle : 'The site uses cookies',
  popupText :  "This website uses cookies, text files that are collected on the user's terminal or that allow access to information on the user's terminal."+
"Cookies allow you to store information about visitors' preferences, are used to verify the proper functioning of the site and improve "+
'its functionality by customizing the content of the pages according to the type of browser used, or to simplify navigation by '+ 
'automating the procedures (eg Login, site language), and finally for the analysis of the use of the site by visitors. '+
'For further information on cookies, please consult the <a class="privacy" href="https://www.whoteach.eu/lms/policy/privacy_policy_.html">privacy policy</a> and the <a class="privacy" href="https://www.whoteach.eu/lms/policy/cookie_policy_.html">cookie policy</a>.'+
'</br></br><p class= "abc">If you do not accept the use, we will not track your behaviour during your visit, but we will use a single cookie in your browser to'+
'remind you that you have chosen not to record navigation information.</p',
  buttonContinueTitle : 'Accept',
  agreementExpiresInDays : 100,
  autoAcceptCookiePolicy : false,
  htmlMarkup : null
});

}