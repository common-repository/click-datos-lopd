//All the cookie setting stuff
function catapultSetCookie(cookieName, cookieValue, nDays) {
    var today = new Date();
    var expire = new Date();
    if (nDays == null || nDays == 0)
        nDays = 1;
    expire.setTime(today.getTime() + 3600000 * 24 * nDays);
    document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
}
function catapultReadCookie(cookieName) {
    var theCookie = " " + document.cookie;
    var ind = theCookie.indexOf(" " + cookieName + "=");
    if (ind == -1)
        ind = theCookie.indexOf(";" + cookieName + "=");
    if (ind == -1 || cookieName == "")
        return "";
    var ind1 = theCookie.indexOf(";", ind + 1);
    if (ind1 == -1)
        ind1 = theCookie.length;
    // Returns true if the versions match
    return cdlopd_vars.version == unescape(theCookie.substring(ind + cookieName.length + 2, ind1));
}
function catapultDeleteCookie(cookieName) {
    document.cookie = cookieName + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
}
function catapultAcceptCookies(dias) {
    catapultSetCookie('catAccCookies', cdlopd_vars.version, dias);
    jQuery("html").removeClass('has-cookie-bar');
    jQuery("html").css("margin-top", "0");
    jQuery("#catapult-cookie-bar").fadeOut();
    document.location.reload(true);
}

function catapultDenyCookies(dias) {
    catapultSetCookie('catAccCookiesDeny', cdlopd_vars.version, dias);
    jQuery("body").removeClass('has-cookie-bar');    
    jQuery("html").css("margin-top", "0");
    jQuery("#catapult-cookie-bar").fadeOut();
}

//function cookiesinaceptarnirechazar() {
//    var date = new Date();
//    date.setTime(date.getTime() + (5 * 60 * 1000));
//    var expires = ";expires=" + date.toGMTString();
//    document.cookie = "catAccCookiesUnan = 1" + expires + "; path=/";
//    jQuery("html").removeClass('has-cookie-bar');
//    jQuery("html").css("margin-top", "0");
//    jQuery("#catapult-cookie-bar").fadeOut();
//}

// The function called by the timer
/*function cdlopdCloseNotification() {
    catapultAcceptCookies();

}*/
// The function called if first page only is specified

/*jQuery(document).ready(function ($) {
    $('.x_close').on('click', function () {
        catapultAcceptCookies();
    });
});*/

function saveCookieAceptar(dias){
    var base_url = window.location.href;
    var url = base_url;
    jQuery(document).ready(function($){
        $.ajax({
            'url': url,
            'type': 'POST',
            'success': function (data) {
                if (data) {
                    var url = base_url;
                    $.ajax({
                        'url': url,
                        'type': 'POST',
                        'success': function (response) {
                            if (response) {
                                catapultSetCookie('catAccCookies', cdlopd_vars.version, dias);
                                jQuery("html").removeClass('has-cookie-bar');
                                jQuery("html").css("margin-top", "0");
                                jQuery("#catapult-cookie-bar").fadeOut();
                                location.reload();
                            }
                        }
                    });
                }
            }
        });
    });    
}

function obtenerCookie(clave) {
    var name = clave + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1);
        if (c.indexOf(name) === 0)
            return c.substring(name.length, c.length);
    }
    return "";
}

function comprobarCookie(clave) {
    var cookie = obtenerCookie(clave);
    if (cookie !== "") {
        return true;
    }
    return false;
}

jQuery(document).ready(function(){
    if (document.getElementById('opcionCookie') === null || document.getElementById('opcionCookie') === undefined) {
        var opcion = 'botones';
        jQuery('.cdlopd-right-side').html('<button id="catapultCookieAceptar" tabindex=0 onclick="catapultAcceptCookies();">Aceptar</button><button id="catapultCookieRechazar" tabindex=0 onclick="catapultDenyCookies();">Rechazar</button>');
    }
    else{
        var opcion = document.getElementById('opcionCookie').value;
    }
    var url = document.getElementById('pag_informacion');
    if(url == window.location.href){
        if(opcion === 'scroll'){
            jQuery(document).ready(function(){
                jQuery(window).scroll(function (e){
                    var scrollTop = jQuery(window).scrollTop();
                    var docHeight = jQuery(document).height();
                    var winHeight = jQuery(window).height();
                    var scrollPercent = (scrollTop) / (docHeight - winHeight);
                    var scrollPercentRounded = Math.round(scrollPercent * 100);
                    var diasAceptar = document.getElementById('scrollCaducidad').value;
                    var diasRechazar = document.getElementById('caducidadRechazar').value;
                    var textAceptar = document.getElementById('textAceptar').value;
                    var textRechazar = document.getElementById('textRechazar').value;
                    var scrollCookies = document.getElementById("texto_scroll");
                    var textoScroll = document.getElementById('textoScroll').value;
                    if(!comprobarCookie('catAccCookies')){
                        if(scrollPercentRounded >= 90){
                            scrollCookies.innerHTML = '<button id="catapultCookieAceptar" \n\
                            tabindex=0 onclick="catapultAcceptCookies('+diasAceptar+');">'+textAceptar+
                            '</button><button id="catapultCookieRechazar" \n\
                            tabindex=0 onclick="catapultDenyCookies('+diasRechazar+');">'+textRechazar+'</button>';
                        }
                        else{
                            scrollCookies.innerHTML = '<p>'+textoScroll+'</p>';
                        }
                    }
                });
            });
        }
    }
    else{
        if (opcion === 'scroll') {
            jQuery(document).ready(function () {
                jQuery(window).scroll(function (e) {
                    var scrollTop = jQuery(window).scrollTop();
                    var docHeight = jQuery(document).height();
                    var winHeight = jQuery(window).height();
                    var scrollPercent = (scrollTop) / (docHeight - winHeight);
                    var scrollPercentRounded = Math.round(scrollPercent * 100);
                    var dias = document.getElementById('scrollCaducidad').value;
                    var porcentaje_scroll = parseFloat(document.getElementById('porcent_scroll').value);
                    if (!comprobarCookie('catAccCookies')) {
                        if (scrollPercentRounded > porcentaje_scroll) {
                            catapultSetCookie('catAccCookies', cdlopd_vars.version, dias);
                            jQuery("html").removeClass('has-cookie-bar');
                            jQuery("html").css("margin-top", "0");
                            jQuery("#catapult-cookie-bar").fadeOut();
                            location.reload();
                        }
                    }
                });
            });
        }
    }
    
});

