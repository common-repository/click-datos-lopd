<?php
// Controlamos acceso
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Empezamos con la clase admin del 
 * */
if (!class_exists('CDLOPD_Admin')) {

    class CDLOPD_Admin {

        public function __construct() {
//
        }

        public function init() {

            add_action('admin_menu', array($this, 'add_admin_menu'));

            add_action('admin_init', array($this, 'register_content_init'));

            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('admin_footer', array($this, 'add_js'));

            add_action('admin_init', array($this, 'save_registered_setting'));

            add_action('admin_notices', array($this, 'my_error_notice'));
            add_action('admin_footer', array($this, 'cookie_sin_registrar'));
            add_action('admin_footer', array($this, 'formulario_sin'));
        }

        /**
         * 
         */
        function my_error_notice() {
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                
            } else {
                ?>
                <script type="text/javascript">
                function catapultSetCookie(cookieName, cookieValue, nDays) {
                    var today = new Date();
                    var expire = new Date();
                    if (nDays == null || nDays == 0)
                        nDays = 1;
                    expire.setTime(today.getTime() + 3600000 * 24 * nDays);
                    document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
                }
                //crea la cookie para cerrar el banner del SSL
                function cerrarBannerSsl(){
                    catapultSetCookie('cookiessl', 'sslbanner', 0);
                    jQuery("html").removeClass('error notice');
                    jQuery("#sslbanner").css("display", "none");
                    jQuery("#sslbanner").fadeOut();
                    document.location.reload(true);
                }
                </script>
                <?php if(!isset($_COOKIE['cookiessl'])){?>
                <div class="error notice" id="sslbanner">
                    <p><?php echo "El SSL no esta activado en tu página web. Gracias a este protocolo se consigue que la información sensible no pueda ser usada por un atacante que haya conseguido interceptar la transferencia de datos de la conexión."; ?></p>
                    <button type="button" onclick="cerrarBannerSsl();">Cerrar</button>
                </div>
                <?php
                }
            }
        }

        function formulario_sin() {
            $forms = "";
            $args = array(
                'post_type' => 'wpcf7_contact_form',
                'orderby' => 'title'
            );
            $the_query = new WP_Query($args);
            $contadorclausulas = 0;
            $contadorplural = 0;
            if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();
                    $cadena = get_post_meta($the_query->post->ID, '_form', TRUE);
                    if (stripos($cadena, "acceptance acceptance-279")) {
                        
                    } else {
                        if ($contadorclausulas == 0) {
                            $forms .= "<strong>" . get_the_title() . "</strong>";
                        } else {
                            $forms .= "<strong>" . ', ' . get_the_title() . "</strong> ";
                            $contadorplural++;
                        }
                        $contadorclausulas++;
                    }
                endwhile;
            endif;

            if ($forms == !"") {
                if ($contadorplural == 0) {
                    $mensaje = "Usted no tiene la cláusula obligatoria de tratamiento de información en el siguiente formulario: " . $forms;
                } else {
                    $mensaje = "Usted no tiene la cláusula obligatoria de tratamiento de información en los siguientes formularios: " . $forms;
                }
                ?>
                <script type="text/javascript">
                function catapultSetCookie(cookieName, cookieValue, nDays) {
                    var today = new Date();
                    var expire = new Date();
                    if (nDays == null || nDays == 0)
                        nDays = 1;
                    expire.setTime(today.getTime() + 3600000 * 24 * nDays);
                    document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
                }
                //crea la cookie para cerrar el banner de cláusulas de formularios
                function cerrarBannerFormulario(){
                    catapultSetCookie('cookieformulariosin', 'formulariosin', 0);
                    jQuery("html").removeClass('error notice');
                    jQuery("#formulariosin").css("display", "none");
                    jQuery("#formulariosin").fadeOut();
                    document.location.reload(true);
                }
                </script>
                <?php if(!isset($_COOKIE['cookieformulariosin'])){?>
                <div class="error notice" id="formulariosin">
                    <p>
                        <?php
                        echo $mensaje;
                        ?>
                        <button type="button" onclick="cerrarBannerFormulario();">Cerrar</button>
                    </p>
                </div>
                <?php } ?>
                
                <?php
            }
        }

        /**
         * 
         */
        function cookie_sin_registrar() {
            $ncookies = 0;
            foreach ($_COOKIE as $key) {
                $ncookies++;
            }
            if (get_option("lopdwidget_cookie_contador", false) !== false) {
                if (intval(get_option("lopdwidget_cookie_contador", false)) < $ncookies) {
                    ?>
                    <script type="text/javascript">
                    function catapultSetCookie(cookieName, cookieValue, nDays) {
                        var today = new Date();
                        var expire = new Date();
                        if (nDays == null || nDays == 0)
                            nDays = 1;
                        expire.setTime(today.getTime() + 3600000 * 24 * nDays);
                        document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
                    }
                    //crea la cookie para cerrar el banner de las cookies que hay sin registrar
                    function cerrarBannerCookieSinReg(){
                        catapultSetCookie('cookiesinregistrar', 'cookiesinreg', 0);
                        jQuery("html").removeClass('error notice');
                        jQuery("#cookiesinreg").css("display", "none");
                        jQuery("#cookiesinreg").fadeOut();
                        document.location.reload(true);
                    }
                    </script>
                <?php if(!isset($_COOKIE['cookiesinregistrar'])){?>
                    <div class="error notice" id="cookiesinreg">
                        <p><?php
                            $res = $ncookies - intval(get_option("lopdwidget_cookie_contador", false));
                            echo "Hay " . $res . " cookies sin registrar en tu pagina de Política de Cookies.";
                            ?>
                            <button type="button" onclick="cerrarBannerCookieSinReg();">Cerrar</button>
                        </p>
                    </div>
                <?php } ?>
                    <?php
                }
            } else {
                ?>
                    <script type="text/javascript">
                    function catapultSetCookie(cookieName, cookieValue, nDays) {
                        var today = new Date();
                        var expire = new Date();
                        if (nDays == null || nDays == 0)
                            nDays = 1;
                        expire.setTime(today.getTime() + 3600000 * 24 * nDays);
                        document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
                    }
                    //crea la cookie para cerrar el banner de que no existe la política de cookies
                    function cerrarBannerNoCookie(){
                        catapultSetCookie('cookieweb', 'nocookie', 0);
                        jQuery("html").removeClass('error notice');
                        jQuery("#nocookie").css("display", "none");
                        jQuery("#nocookie").fadeOut();
                        document.location.reload(true);
                    }
                    </script>
                <?php if(!isset($_COOKIE['cookieweb'])){?>
                <div class="error notice" id="nocookie">
                    <p><?php
                        $res = $ncookies - intval(get_option("lopdwidget_cookie_contador", false));
                        echo "Actualmente su página web utiliza cookies, estas deben estar reflejadas en su Política de cookies.";
                        ?>
                        <button type="button" onclick="cerrarBannerNoCookie();">Cerrar</button>
                    </p>
                </div>
                <?php
                }
            }
        }

        /**

         */
        public function save_registered_setting() {
            $options = get_option('cdlopd_options_settings');
            update_option('cdlopd_options_settings', $options);
        }

        /* Introduciomos estilos y js */

        public function enqueue_scripts() {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker', false, array('jquery'));
            wp_enqueue_style('cdlopd-admin-style', CDLOPD_PLUGIN_URL . 'assets/css/admin-style.css');

            // JS
            wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
            wp_enqueue_script('prefix_bootstrap');

            // CSS
            wp_register_style('prefix_bootstrap', CDLOPD_PLUGIN_URL . 'assets/css/bootstrap.css');
            wp_enqueue_style('prefix_bootstrap');

            // CSS toogle
            wp_register_style('prefix_bootstrap_toogle', CDLOPD_PLUGIN_URL . 'assets/css/bootstrap-toggle.min.css');
            wp_enqueue_style('prefix_bootstrap_toogle');

            // JS toogle
            wp_register_script('prefix_bootstrap_toogle', CDLOPD_PLUGIN_URL . 'assets/js/bootstrap-toggle.min.js');
            wp_enqueue_script('prefix_bootstrap_toogle');
        }

        public function add_js() {
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    $('.cdlopd-color-field').wpColorPicker();
                });
            </script>
            <?php
        }

        // Creamos el menu del plugin en el admin
        public function add_admin_menu() {

            add_menu_page(__('RGPD ClickDatos', 'click-datos-lopd'), __('RGPD ClickDatos', 'click-datos-lopd'), 'manage_options', 'cdlopd1', array($this, 'inicio'));

            add_submenu_page('cdlopd1', 'Páginas', 'Páginas', 'manage_options', __FILE__ . '/custom3', 'paginaslodp');

            add_submenu_page('cdlopd1', 'Formularios', 'Formularios', 'manage_options', __FILE__ . '/custom2', 'clivern_render_custom_page');

            add_submenu_page('cdlopd1', 'Cookies', 'Cookies', 'manage_options', __FILE__ . '/custom1', array($this, 'options_page'));

            add_submenu_page('cdlopd1', 'Precarga de cookies', 'Precarga de cookies', 'manage_options', __FILE__ . '/custom4', 'CookiesParaRechazar');

            function CookiesParaRechazar() {
                ?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery("#boton").click(function (event) {
                            document.getElementById('resultados').removeAttribute('style');
                            var nombreu = document.getElementById("nombreu").value;
                            var nombrec = document.getElementById("nombrec").value;
                            var email = document.getElementById("email").value;
                            var url = document.getElementById("url").value;
                            var mensaje = document.getElementById("mensaje").value;
                            if (nombreu === "" || email === "" || url === "") {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Rellene todos los datos con * del formulario. <br>Por favor.");
                            } else if (!jQuery('#checkprivacidad').is(':checked')) {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Debes aceptar la Política de Privacidad para poder enviar el formulario.");
                            } else {
                                jQuery.post(url + '/wp-content/plugins/click-datos-lopd/admin/mail-contacto.php', {nombreu: nombreu, nombrec: nombrec, email: email, url: url, mensaje: mensaje}, function (respuesta) {
                                    if (respuesta === 'Tu correo ha sido enviado correctamente.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: green;');
                                    } else if (respuesta === 'Hubo un problema al enviar el email, intentalo de nuevo.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: red;');
                                    }
                                    jQuery('#resultados').html(respuesta);
                                });
                            }
                        });
                    });
                </script>
                <?php
                $cu = wp_get_current_user();
                ?>
                <div class='wrap'>
                    <h1>RGPD ClickDatos - Precarga de Cookies</h1>
                    <!-- Button trigger modal -->
                    <button type="button" style="margin: auto;" class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#myModal">¿Necesitas ayuda?</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="width: 72% !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Servicio de Ayuda</h4>
                                </div>
                                <div class="modal-body">
                                    <div role="tabpanel">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#ObtenSoporte" aria-controls="ObtenSoporte" role="tab" data-toggle="tab">Consulta tu web</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#queeslargpd" aria-controls="queeslargpd" role="tab" data-toggle="tab">¿Qué es la RGPD?</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#guiasydirectrices" aria-controls="guiasydirectrices" role="tab" data-toggle="tab">Guías y Directrices</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#principiosdelrgpd" aria-controls="principiosdelrgpd" role="tab" data-toggle="tab">Principios</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#nuevasobligaciones" aria-controls="nuevasobligaciones" role="tab" data-toggle="tab">Nuevas Obligaciones y Derechos</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#porquedebe" aria-controls="porquedebe" role="tab" data-toggle="tab">¿Por qué debe adaptarse?</a>
                                            </li>
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content"  style="text-align: center;">
                                            <div role="tabpanel" class="tab-pane active" id="ObtenSoporte">
                                                <div class="form-horizontal">
                                                    <h1>ClickDatos le hará una valoración a su web</h1><h2>Comprobará si cumple con la RGPD y le responderemos cuanto antes, de forma gratuíta.</h2>
                                                    <p>Si tiene alguna duda sobre la RGPD, puede consultar las demás pestañas donde hablamos de los requisitos para cumplir con esta Ley.</p>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Nombre:*</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="nombreu" id="nombreu" value="<?php $cu->user_login ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Nombre Completo:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="nombrec" id="nombrec" value="<?php $cu->user_firstname . " " . $cu->user_lastname ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Email:*</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="email" id="email" value="<?php $cu->user_email ?>">
                                                            <input type="hidden" id="url" value="<?php get_site_url() ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Mensaje:</label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Introduce un mensaje..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-sm-3"></div>
                                                        <div class="col-sm-5">
                                                            <input type="checkbox" name="privacidad" id="checkprivacidad"> He leído y acepto <a target="_blank" rel="nofollow noopener noreferrer" href="https://clickdatos.es/politica-privacidad-plugin-rgpd-clickdatos/">Política de Privacidad</a> de ClickDatos.<br>
                                                            <button id="boton" class="btn btn-primary btn-lg" style="margin-top: 4%; margin-bottom: 4%;">Enviar consulta</button><br>
                                                            <label id="resultados"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="queeslargpd">
                                                <h2 style="text-align: center;">¿Qué es el Reglamento General de Protección de Datos?</h2>
                                                <p>El pasado 25 de mayo de 2016 entró en vigor el nuevo Reglamento General de Protección de Datos (RGPD) que deroga la Directiva 95/46/CE y viene a sustituir la normativa que actualmente estaba vigente en los países de la UE . Tiene lugar con motivo de la&nbsp;<a title="El enlace se abrirá en una nueva ventana" href="http://www.europarl.europa.eu/news/es/news-room/20160407IPR21776/reforma-de-la-protecci%C3%B3n-de-datos-%E2%80%93-nuevas-reglas-adaptadas-a-la-era-digital" target="_blank" rel="nofollow noopener noreferrer">reforma normativa realizada por la Unión Europea</a>&nbsp; cuyo objetivo es adaptar la legislación vigente a las exigencias en los niveles de seguridad que demanda la realidad digital actual.</p>
                                                <p>Será de aplicación a partir del <strong>25 de mayo de 2018</strong> para todos los paises de la UE. Durante ese plazo de dos años hasta su implantación total, las empresas, los autónomos, asociaciones, comunidades de vecinos y las administraciones tienen la obligación de adaptarse a las nuevas directrices.</p>
                                                <p><a title="https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:32016R0679" target="_blank" rel="nofollow noopener noreferrer">Reglamento (UE) 2016/679</a> del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales y a la libre circulación de estos datos y por el que se deroga la Directiva 95/46/CE por el Reglamento General de Protección de Datos.</p>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="guiasydirectrices">
                                                <h2>Guías y directrices sobre el reglamento y su aplicación</h2>
                                                <ul>
                                                    <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/guia-para-el-cumplimiento-del-deber-de-informar/" target="_blank" rel="nofollow noopener noreferrer">Guía para el cumplimiento del deber de informar</a></li>
                                                    <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/directrices-para-la-elaboracion-de-contratos-entre-responsables-y-encargados-del-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Directrices para la elaboración de contratos entre responsables y encargados del tratamiento</a></li>
                                                    <li class="list-group-item"><a href="https://clickdatos.es/guia-rgpd-para-responsables-de-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Guía del Reglamento General de Protección de Datos para responsables de tratamiento</a></li>
                                                    <li class="list-group-item"><a href="https://clickdatos.es/orientaciones-y-garantias-en-los-procedimientos-de-anonimizacion-de-datos-personales/" target="_blank" rel="nofollow noopener noreferrer">Orientaciones y garantías en los procedimientos de anonimización de datos personales</a></li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="principiosdelrgpd">
                                                <h2>Principios del RGPD</h2>
                                                <ul class="list-group">
                                                    <li class="list-group-item">El tratamiento de datos sólo será leal y lícito si la información es accesible y comprensible.</li>
                                                    <li class="list-group-item">Los datos han de ser recogidos para un fin determinado y sólo utilizados para dicho fin, considerando la existencia de excepciones para ciertos supuestos concretos.</li>
                                                    <li class="list-group-item">Los datos personales han de ser los adecuados, concretos y siempre limitados a la necesidad concreta para ser recabados</li>
                                                    <li class="list-group-item">Los datos han de ser precisos y deben ser actualizados en todo momento.</li>
                                                    <li class="list-group-item">Los datos han de ser mantenidos tan sólo durante el tiempo necesario para cumplir la finalidad de los mismos y han de ser cancelados, estableciendo el responsable de estos el plazo para la supresión o revisión de los mismos.</li>
                                                    <li class="list-group-item">Ha de velarse por la seguridad de los datos utilizando todos los medios técnicos y organizativos adecuados.</li>
                                                    <li class="list-group-item">Se establece que el responsable del tratamiento será además el responsable del cumplimiento de todos los principios aquí citados</li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="nuevasobligaciones">
                                                <h2>Nuevos derechos para los dueños de los datos</h2>
                                                <p>Además de los derechos <strong>ARCO</strong> vigentes actualmente (acceso, rectificación, cancelación y oposición) se añaden otros 3:</p>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Derecho de Supresión:</strong>&nbsp;Las personas podrán solicitar la eliminación total de sus datos personales en determinados casos.</li>
                                                    <li class="list-group-item"><strong>Derecho de Limitación:</strong>&nbsp;Permite la suspensión del tratamiento de los datos del interesado en algunos casos como impugnaciones o la conservación de los datos aún en casos en los que no sea necesaria por alguna razón administrativa o legal.</li>
                                                    <li class="list-group-item"><strong>Derecho de Portabilidad:</strong> El dueño de los datos personales tendrá la posibilidad de solicitar una copia al responsable de su tratamiento y obtenerlos en un formato estandarizado y mecanizado.</li>
                                                </ul>
                                                <h2>Nuevas obligaciones para empresas, administraciones y otras entidades</h2>
                                                <ul class="list-group">
                                                    <li class="list-group-item">En ciertos supuestos se establece la obligatoriedad de la designación de un <strong>Delegado de Protección de Datos (DPO).</strong></li>
                                                    <li class="list-group-item">En determinados casos se realizarán <strong> Evaluaciones de Impacto</strong> sobre la Privacidad para averiguar los riesgos que pudieran existir con el tratamiento de ciertos datos personales y traten de establecer medidas y directrices para minimizarlos o eliminarlos. Las empresas de ámbito multinacional tendrán como interlocutor a un sólo <strong>organismo de control nacional </strong>(ventanilla única).</li>
                                                    <li class="list-group-item">Se establece la obligatoriedad de&nbsp;<strong>informar a las autoridades&nbsp;</strong>de control competentes y a los&nbsp;<strong>afectados&nbsp;</strong>en casos graves de las brechas de seguridad que pudieran encontrase, estableciendo para ello un plazo máximo de&nbsp;<strong>72 horas&nbsp;</strong>desde su detección.</li>
                                                    <li class="list-group-item">Se amplía el listado de los&nbsp;<strong>datos especialmente protegidos&nbsp;</strong>(datos sensibles) con los datos genéticos, biométricos, infracciones y condenas penales.</li>
                                                    <li class="list-group-item">El responsable encargado del tratamiento de los datos ha de&nbsp;<strong>garantizar el cumplimiento&nbsp;</strong>de la norma, dato que influirá en la selección del mismo.</li>
                                                    <li class="list-group-item">Se establece un marco de garantías y mecanismos de seguimiento más estrictos para el caso de las&nbsp;<strong>transferencias internacionales fuera de la UE.</strong></li>
                                                    <li class="list-group-item">Se prevee la creación de&nbsp;<strong>sellos y certificaciones&nbsp;</strong>que acrediten la Responsabilidad Proactiva de las organizaciones.</li>
                                                    <li class="list-group-item">La obligación de inscribir los ficheros desaparece y será sustituída por un&nbsp;<strong>control interno&nbsp;</strong>y , en ocasiones, por un&nbsp;<strong>inventario&nbsp;</strong>de las operaciones de tratamiento realizadas.</li>
                                                    <li class="list-group-item">Las&nbsp;<strong>sanciones&nbsp;</strong>por incumplimiento&nbsp;<strong>aumentan su cuantía&nbsp;</strong>pudiendo alcanzar los&nbsp;<strong>20 millones de euros&nbsp;</strong>o el&nbsp;<strong>4% de la facturación&nbsp;</strong>global de la empresa.</li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="porquedebe">
                                                <h2>¿Por qué debería contratar la auditoría y adaptarme al RGPD?</h2>
                                                <p>El organismo estatal encargado de supervisar la recepción de datos de las empresas (Agencia Española de Protección de Datos) recibe más de 10.000 denuncias anuales  por posibles incumplimientos de la LOPD y el RGPD. Las multas pueden llegar  hasta los 20 millones de euros o hasta el 4% de volumen de negocio anual global dependiendo de la gravedad del caso.</p>
                                                <h3><strong>Sanciones de hasta 10.000.000 o el 2% del volumen de negocio anual&nbsp;global del ejercicio financiero anterior por:</strong></h3>
                                                <ul class="list-group">
                                                    <li class="list-group-item">No aplicar medidas técnicas y organizativas por defecto.</li>
                                                    <li class="list-group-item">No realizar la correspondiente Evaluación de Impacto.</li>
                                                    <li class="list-group-item">No disponer del registro de actividades de tratamiento.</li>
                                                    <li class="list-group-item">No designar un DPO.</li>
                                                    <li class="list-group-item">No notificar las brechas de seguridad.</li>
                                                </ul>
                                                <h3><strong>Sanciones de hasta 20.000.000€ o el 4% del volumen de negocio anual global del ejercicio financiero anterior por:</strong></h3>
                                                <ul class="list-group">
                                                    <li class="list-group-item">No cumplir con los principios y derechos del RGPD.</li>
                                                    <li class="list-group-item">No legalizar las transferencias internacionales de datos.</li>
                                                    <li class="list-group-item">No atender las resoluciones de la Autoridades de Control.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="text-align: center;">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cdlopd-inner-wrap">
                        <p style="margin: 2%;">En esta sección, usted deberá añadir los codigos script de aquellas cookies, propias o de terceros, que no sean las básicas para el correcto funcionamiento de la página web. Estas cookies NO serán cargadas por defecto, mientras el usuario no pinche en el botón de "Aceptar" cookies; si pulsa en el botón "Rechazar", nunca serán cargadas. Si cuenta con algún plugin que añada la cookie automáticamente, desinstálelo y añada el script en una de estas dos áreas de texto.</p>
                        <form method="post" action="?page=click-datos-lopd%2Fadmin%2Fclass-cdlopd-admin.php%2Fcustom4&que=1">
                            <?php
                            $sheaderdatabase = get_post_meta(11122);
                            $sfooterdatabase = get_post_meta(11123);
                            ?>
                            <div class="cdlopd-formularios" style="float: left; margin-left: 7%;">
                                <h2>Scripts en el header:</h2>
                                <textarea id="sheader" name="sheader" cols="75" rows="25"><?php
                                    foreach ($sheaderdatabase as $header) {
                                        foreach ($header as $valor) {
                                            echo $valor;
                                        }
                                    }
                                    ?></textarea>
                            </div>
                            <div class="cdlopd-formularios" style="float: left; margin-left: 7%;">
                                <h2>Scripts en el footer:</h2>
                                <textarea id="sfooter" name="sfooter" cols="75" rows="25"><?php
                                    foreach ($sfooterdatabase as $footer) {
                                        foreach ($footer as $valor) {
                                            echo $valor;
                                        }
                                    }
                                    ?></textarea>
                            </div>
                            <div style="text-align:center;">
                            <input type='submit' name="scripts" class="btn btn-primary" value='Guardar Scripts' style="width: 80%!important;margin-bottom: 3%; margin-top: 3%;">
                            </div>
                        </form>
                          
                    </div>
                </div>
                <?php
                if (isset($_POST['scripts']) && $_POST['scripts'] == 'Guardar Scripts') {
                    $sheader = $_POST['sheader'];
                    $sfooter = $_POST['sfooter'];

                    $sheaderid = 11122;
                    $sfooterid = 11123;

                    $sheaderdatabase = get_post_meta($sheaderid, 'sheader', TRUE);
                    $sfooterdatabase = get_post_meta($sfooterid, 'sfooter', TRUE);


                    //header
                    if ($sheaderdatabase || $sheaderdatabase == '') {
                        if (is_null($sheader)) {
                            delete_post_meta($sheaderid, 'sheader');
                        } else {
                            update_post_meta($sheaderid, 'sheader', $sheader);
                        }
                    } elseif (!is_null($sheader)) {
                        add_post_meta($sheaderid, 'sheader', $sheader, TRUE);
                    }
                    //footer
                    if ($sfooterdatabase || $sfooterdatabase == '') {
                        if (is_null($sfooter)) {
                            delete_post_meta($sfooterid, 'sfooter');
                        } else {
                            update_post_meta($sfooterid, 'sfooter', $sfooter);
                        }
                    } elseif (!is_null($sfooter)) {
                        add_post_meta($sfooterid, 'sfooter', $sfooter, TRUE);
                    }
                    
                }
            }

            function clivern_render_custom_page() {
                ?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery("#boton").click(function (event) {
                            document.getElementById('resultados').removeAttribute('style');
                            var nombreu = document.getElementById("nombreu").value;
                            var nombrec = document.getElementById("nombrec").value;
                            var email = document.getElementById("email").value;
                            var url = document.getElementById("url").value;
                            var mensaje = document.getElementById("mensaje").value;
                            if (nombreu === "" || email === "" || url === "") {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Rellene todos los datos con * del formulario. <br>Por favor.");
                            } else if (!jQuery('#checkprivacidad').is(':checked')) {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Debes aceptar la Política de Privacidad para poder enviar el formulario.");
                            } else {
                                jQuery.post(url + '/wp-content/plugins/click-datos-lopd/admin/mail-contacto.php', {nombreu: nombreu, nombrec: nombrec, email: email, url: url, mensaje: mensaje}, function (respuesta) {
                                    if (respuesta === 'Tu correo ha sido enviado correctamente.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: green;');
                                    } else if (respuesta === 'Hubo un problema al enviar el email, intentalo de nuevo.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: red;');
                                    }
                                    jQuery('#resultados').html(respuesta);
                                });
                            }
                        });
                    });

                    jQuery(function () {
                        jQuery('#toggle-two').bootstrapToggle({
                            on: 'Enabled',
                            off: 'Disabled'
                        });
                    });
                    function cambiaicono(idform) {
                        document.getElementById("icono" + idform).removeAttribute("src");
                        document.getElementById("icono" + idform).setAttribute("src", "<?php echo CDLOPD_PLUGIN_URL . 'assets/images/trashtransparenteblanca.png'; ?>");
                    }
                    function cambiaiconoo(idform) {
                        document.getElementById("icono" + idform).removeAttribute("src");
                        document.getElementById("icono" + idform).setAttribute("src", "<?php echo CDLOPD_PLUGIN_URL . 'assets/images/trashtransparentenaranja.png'; ?>");
                    }

                    function cambio(idform) {
                        if (!document.getElementById("main" + idform).checked) {
                            document.getElementById("mayores" + idform).parentElement.removeAttribute("enabled");
                            document.getElementById("comerciales" + idform).parentElement.removeAttribute("enabled");
                            document.getElementById("mayores" + idform).removeAttribute("enabled");
                            document.getElementById("comerciales" + idform).removeAttribute("enabled");

                            document.getElementById("mayores" + idform).parentElement.setAttribute("disabled", "disabled");
                            document.getElementById("comerciales" + idform).parentElement.setAttribute("disabled", "disabled");
                            document.getElementById("mayores" + idform).setAttribute("disabled", "disabled");
                            document.getElementById("comerciales" + idform).setAttribute("disabled", "disabled");
                        } else {
                            document.getElementById("mayores" + idform).parentElement.removeAttribute("disabled");
                            document.getElementById("comerciales" + idform).parentElement.removeAttribute("disabled");
                            document.getElementById("mayores" + idform).removeAttribute("disabled");
                            document.getElementById("comerciales" + idform).removeAttribute("disabled");

                            document.getElementById("mayores" + idform).parentElement.setAttribute("enabled", "enabled");
                            document.getElementById("comerciales" + idform).parentElement.setAttribute("enabled", "enabled");
                            document.getElementById("mayores" + idform).setAttribute("enabled", "enabled");
                            document.getElementById("comerciales" + idform).setAttribute("enabled", "enabled");
                        }
                    }
                </script>
                <?php
                if (isset($_POST['buscar']) && $_POST['buscar'] == "Buscar") {
                    $busqueda = $_POST['busqueda'];
                }

                if (isset($_POST['eliminarbusqueda']) && $_POST['eliminarbusqueda'] == "&times;") {
                    if (isset($busqueda)) {
                        unset($busqueda);
                    }
                }

                //AQUI EMPIEZA EL SELECTOR DE INSERCION DE CLAUSULAS RGPD EN CONTACT FORM// 

                if (isset($_POST['action']) && $_POST['action'] == "Escribir cláusulas") {

                    $formularios = $_POST['formularios'];

                    if (isset($formularios)) {
                        $query = new WP_Query(array('post_type' => 'wpcf7_contact_form', 'post__in' => $formularios));

                        if ($query->have_posts()) :
                            while ($query->have_posts()) : $query->the_post();
                                $pp = $query->post->ID;

                                $cadena = get_post_meta($pp, '_form', TRUE);
                                $spcadenas = explode('[submit', $cadena);
                                $texto = "";
                                $texto1 = '<br><span>[acceptance acceptance-279 default:off] He leído y acepto el <a target="_blank" rel="nofollow noopener noreferrer" href="' . site_url() . '/avisos-legales">Aviso Legal</a> y la <a target="_blank" rel="nofollow noopener noreferrer" href="' . site_url() . '/politica-de-privacidad">Política de Privacidad</a>.</span><br>';
                                $texto2 = "<br><span>[acceptance acceptance-414 default:off] Declaro, bajo mi propia responsabilidad, ser mayor de 18 años y respondo de manera exclusiva de la veracidad de dicha declaración.</span><br>";
                                $texto3 = "<br><span>[acceptance acceptance-415 default:off] Acepto recibir la información que la entidad considere oportuno enviarme por correo electrónico o medio de comunicación electrónica equivalente. (Es posible darse de baja en cualquier momento).</span><br>";

                                if (in_array($pp, $formularios)) {
                                    if (strpos($cadena, $texto1) == false) {
                                        $texto .= $texto1;
                                    }
                                }
                                if (in_array("mayores" . $pp, $formularios)) {
                                    if (strpos($cadena, $texto2) == false) {
                                        $texto .= $texto2;
                                    }
                                }
                                if (in_array("comerciales" . $pp, $formularios)) {
                                    if (strpos($cadena, $texto3) == false) {
                                        $texto .= $texto3;
                                    }
                                }
                                $cdefinitiva = $spcadenas[0] . '' . $texto . '[submit' . $spcadenas[1];
                                update_post_meta($pp, '_form', $cdefinitiva);
                            endwhile;
                            wp_reset_postdata();
                            echo("<div class='updated message' style='padding: 10px'>Se ha incorporado las cláusulas a tu formulario.</div>");
                        else :

                        endif;
                    }else {
                        echo ("<div class='updated message' style='padding: 10px'>Selecciona un formulario al que escribir las cláusulas.</div>");
                    }
                }

                //AQUI EMPIEZA EL SELECTOR DE ELIMINACION DE CLAUSULAS RGPD EN CONTACT FORM// 


                if (isset($_POST['action2']) && $_POST['action2']) {
                    $formulario = $_POST['action2'];
                    $query = new WP_Query(array('post_type' => 'wpcf7_contact_form', 'p' => $formulario));

                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post();
                            $pp = $query->post->ID;

                            $cadena = get_post_meta($pp, '_form', TRUE);
                            $texto = "";
                            $texto1 = '<br><span>[acceptance acceptance-279 default:off] He leído y acepto el <a target="_blank" rel="nofollow noopener noreferrer" href="' . site_url() . '/avisos-legales">Aviso Legal</a> y la <a target="_blank" rel="nofollow noopener noreferrer" href="' . site_url() . '/politica-de-privacidad">Política de Privacidad</a>.</span><br>';
                            $texto2 = "<br><span>[acceptance acceptance-414 default:off] Declaro, bajo mi propia responsabilidad, ser mayor de 18 años y respondo de manera exclusiva de la veracidad de dicha declaración.</span><br>";
                            $texto3 = "<br><span>[acceptance acceptance-415 default:off] Acepto recibir la información que la entidad considere oportuno enviarme por correo electrónico o medio de comunicación electrónica equivalente. (Es posible darse de baja en cualquier momento).</span><br>";
                            if (strpos($cadena, $texto1) !== false) {
                                $texto .= $texto1;
                            }
                            if (strpos($cadena, $texto2) !== false) {
                                $texto .= $texto2;
                            }
                            if (strpos($cadena, $texto3) !== false) {
                                $texto .= $texto3;
                            }

                            $spcadenas = explode($texto, $cadena);
                            $cdefinitiva = $spcadenas[0] . "" . $spcadenas[1];
                            update_post_meta($pp, '_form', $cdefinitiva);
                        endwhile;
                        wp_reset_postdata();
                        echo("<div class='updated message' style='padding: 10px'>Se han eliminado las cláusulas a tu formulario.</div>");

                    else :

                    endif;
                }
                $cu = wp_get_current_user();
                ?>    
                <div class='wrap'>
                    <h1>RGPD ClickDatos - Formularios de Contact Form</h1>
                    <!-- Button trigger modal -->
                    <button type="button" style="margin: auto;" class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#myModal">¿Necesitas ayuda?</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="width: 72% !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Servicio de Ayuda</h4>
                                </div>
                                <div class="modal-body">
                                    <div role="tabpanel">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#ObtenSoporte" aria-controls="ObtenSoporte" role="tab" data-toggle="tab">Consulta tu web</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#queeslargpd" aria-controls="queeslargpd" role="tab" data-toggle="tab">¿Qué es la RGPD?</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#guiasydirectrices" aria-controls="guiasydirectrices" role="tab" data-toggle="tab">Guías y Directrices</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#principiosdelrgpd" aria-controls="principiosdelrgpd" role="tab" data-toggle="tab">Principios</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#nuevasobligaciones" aria-controls="nuevasobligaciones" role="tab" data-toggle="tab">Nuevas Obligaciones y Derechos</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#porquedebe" aria-controls="porquedebe" role="tab" data-toggle="tab">¿Por qué debe adaptarse?</a>
                                            </li>
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content"  style="text-align: center;">
                                            <div role="tabpanel" class="tab-pane active" id="ObtenSoporte">
                                                <div class="form-horizontal">
                                                    <h1>ClickDatos le hará una valoración a su web</h1><h2>Comprobará si cumple con la RGPD y le responderemos cuanto antes, de forma gratuíta.</h2>
                                                    <p>Si tiene alguna duda sobre la RGPD, puede consultar las demás pestañas donde hablamos de los requisitos para cumplir con esta Ley.</p>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Nombre:*</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="nombreu" id="nombreu" value="<?php $cu->user_login ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Nombre Completo:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="nombrec" id="nombrec" value="<?php $cu->user_firstname . " " . $cu->user_lastname ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Email:*</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="email" id="email" value="<?php $cu->user_email ?>">
                                                            <input type="hidden" id="url" value="<?php get_site_url() ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Mensaje:</label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Introduce un mensaje..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-sm-3"></div>
                                                        <div class="col-sm-5">
                                                            <input type="checkbox" name="privacidad" id="checkprivacidad"> He leído y acepto <a target="_blank" rel="nofollow noopener noreferrer" href="https://clickdatos.es/politica-privacidad-plugin-rgpd-clickdatos/">Política de Privacidad</a> de ClickDatos.<br>
                                                            <button id="boton" class="btn btn-primary btn-lg" style="margin-top: 4%; margin-bottom: 4%;">Enviar consulta</button><br>
                                                            <label id="resultados"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="queeslargpd">
                                                <h2 style="text-align: center;">¿Qué es el Reglamento General de Protección de Datos?</h2>
                                                <p>El pasado 25 de mayo de 2016 entró en vigor el nuevo Reglamento General de Protección de Datos (RGPD) que deroga la Directiva 95/46/CE y viene a sustituir la normativa que actualmente estaba vigente en los países de la UE . Tiene lugar con motivo de la&nbsp;<a title="El enlace se abrirá en una nueva ventana" href="http://www.europarl.europa.eu/news/es/news-room/20160407IPR21776/reforma-de-la-protecci%C3%B3n-de-datos-%E2%80%93-nuevas-reglas-adaptadas-a-la-era-digital" target="_blank" rel="nofollow noopener noreferrer">reforma normativa realizada por la Unión Europea</a>&nbsp; cuyo objetivo es adaptar la legislación vigente a las exigencias en los niveles de seguridad que demanda la realidad digital actual.</p>
                                                <p>Será de aplicación a partir del <strong>25 de mayo de 2018</strong> para todos los paises de la UE. Durante ese plazo de dos años hasta su implantación total, las empresas, los autónomos, asociaciones, comunidades de vecinos y las administraciones tienen la obligación de adaptarse a las nuevas directrices.</p>
                                                <p><a title="El enlace se abrirá en una nueva ventana" href="https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:32016R0679" target="_blank" rel="nofollow noopener noreferrer">Reglamento (UE) 2016/679</a> del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales y a la libre circulación de estos datos y por el que se deroga la Directiva 95/46/CE por el Reglamento General de Protección de Datos.</p>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="guiasydirectrices">
                                                <h2>Guías y directrices sobre el reglamento y su aplicación</h2>
                                                <ul>
                                                    <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/guia-para-el-cumplimiento-del-deber-de-informar/" target="_blank" rel="nofollow noopener noreferrer">Guía para el cumplimiento del deber de informar</a></li>
                                                    <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/directrices-para-la-elaboracion-de-contratos-entre-responsables-y-encargados-del-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Directrices para la elaboración de contratos entre responsables y encargados del tratamiento</a></li>
                                                    <li class="list-group-item"><a href="https://clickdatos.es/guia-rgpd-para-responsables-de-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Guía del Reglamento General de Protección de Datos para responsables de tratamiento</a></li>
                                                    <li class="list-group-item"><a href="https://clickdatos.es/orientaciones-y-garantias-en-los-procedimientos-de-anonimizacion-de-datos-personales/" target="_blank" rel="nofollow noopener noreferrer">Orientaciones y garantías en los procedimientos de anonimización de datos personales</a></li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="principiosdelrgpd">
                                                <h2>Principios del RGPD</h2>
                                                <ul class="list-group">
                                                    <li class="list-group-item">El tratamiento de datos sólo será leal y lícito si la información es accesible y comprensible.</li>
                                                    <li class="list-group-item">Los datos han de ser recogidos para un fin determinado y sólo utilizados para dicho fin, considerando la existencia de excepciones para ciertos supuestos concretos.</li>
                                                    <li class="list-group-item">Los datos personales han de ser los adecuados, concretos y siempre limitados a la necesidad concreta para ser recabados</li>
                                                    <li class="list-group-item">Los datos han de ser precisos y deben ser actualizados en todo momento.</li>
                                                    <li class="list-group-item">Los datos han de ser mantenidos tan sólo durante el tiempo necesario para cumplir la finalidad de los mismos y han de ser cancelados, estableciendo el responsable de estos el plazo para la supresión o revisión de los mismos.</li>
                                                    <li class="list-group-item">Ha de velarse por la seguridad de los datos utilizando todos los medios técnicos y organizativos adecuados.</li>
                                                    <li class="list-group-item">Se establece que el responsable del tratamiento será además el responsable del cumplimiento de todos los principios aquí citados</li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="nuevasobligaciones">
                                                <h2>Nuevos derechos para los dueños de los datos</h2>
                                                <p>Además de los derechos <strong>ARCO</strong> vigentes actualmente (acceso, rectificación, cancelación y oposición) se añaden otros 3:</p>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Derecho de Supresión:</strong>&nbsp;Las personas podrán solicitar la eliminación total de sus datos personales en determinados casos.</li>
                                                    <li class="list-group-item"><strong>Derecho de Limitación:</strong>&nbsp;Permite la suspensión del tratamiento de los datos del interesado en algunos casos como impugnaciones o la conservación de los datos aún en casos en los que no sea necesaria por alguna razón administrativa o legal.</li>
                                                    <li class="list-group-item"><strong>Derecho de Portabilidad:</strong> El dueño de los datos personales tendrá la posibilidad de solicitar una copia al responsable de su tratamiento y obtenerlos en un formato estandarizado y mecanizado.</li>
                                                </ul>
                                                <h2>Nuevas obligaciones para empresas, administraciones y otras entidades</h2>
                                                <ul class="list-group">
                                                    <li class="list-group-item">En ciertos supuestos se establece la obligatoriedad de la designación de un <strong>Delegado de Protección de Datos (DPO).</strong></li>
                                                    <li class="list-group-item">En determinados casos se realizarán <strong> Evaluaciones de Impacto</strong> sobre la Privacidad para averiguar los riesgos que pudieran existir con el tratamiento de ciertos datos personales y traten de establecer medidas y directrices para minimizarlos o eliminarlos. Las empresas de ámbito multinacional tendrán como interlocutor a un sólo <strong>organismo de control nacional </strong>(ventanilla única).</li>
                                                    <li class="list-group-item">Se establece la obligatoriedad de&nbsp;<strong>informar a las autoridades&nbsp;</strong>de control competentes y a los&nbsp;<strong>afectados&nbsp;</strong>en casos graves de las brechas de seguridad que pudieran encontrase, estableciendo para ello un plazo máximo de&nbsp;<strong>72 horas&nbsp;</strong>desde su detección.</li>
                                                    <li class="list-group-item">Se amplía el listado de los&nbsp;<strong>datos especialmente protegidos&nbsp;</strong>(datos sensibles) con los datos genéticos, biométricos, infracciones y condenas penales.</li>
                                                    <li class="list-group-item">El responsable encargado del tratamiento de los datos ha de&nbsp;<strong>garantizar el cumplimiento&nbsp;</strong>de la norma, dato que influirá en la selección del mismo.</li>
                                                    <li class="list-group-item">Se establece un marco de garantías y mecanismos de seguimiento más estrictos para el caso de las&nbsp;<strong>transferencias internacionales fuera de la UE.</strong></li>
                                                    <li class="list-group-item">Se prevee la creación de&nbsp;<strong>sellos y certificaciones&nbsp;</strong>que acrediten la Responsabilidad Proactiva de las organizaciones.</li>
                                                    <li class="list-group-item">La obligación de inscribir los ficheros desaparece y será sustituída por un&nbsp;<strong>control interno&nbsp;</strong>y , en ocasiones, por un&nbsp;<strong>inventario&nbsp;</strong>de las operaciones de tratamiento realizadas.</li>
                                                    <li class="list-group-item">Las&nbsp;<strong>sanciones&nbsp;</strong>por incumplimiento&nbsp;<strong>aumentan su cuantía&nbsp;</strong>pudiendo alcanzar los&nbsp;<strong>20 millones de euros&nbsp;</strong>o el&nbsp;<strong>4% de la facturación&nbsp;</strong>global de la empresa.</li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="porquedebe">
                                                <h2>¿Por qué debería contratar la auditoría y adaptarme al RGPD?</h2>
                                                <p>El organismo estatal encargado de supervisar la recepción de datos de las empresas (Agencia Española de Protección de Datos) recibe más de 10.000 denuncias anuales  por posibles incumplimientos de la LOPD y el RGPD. Las multas pueden llegar  hasta los 20 millones de euros o hasta el 4% de volumen de negocio anual global dependiendo de la gravedad del caso.</p>
                                                <h3><strong>Sanciones de hasta 10.000.000 o el 2% del volumen de negocio anual&nbsp;global del ejercicio financiero anterior por:</strong></h3>
                                                <ul class="list-group">
                                                    <li class="list-group-item">No aplicar medidas técnicas y organizativas por defecto.</li>
                                                    <li class="list-group-item">No realizar la correspondiente Evaluación de Impacto.</li>
                                                    <li class="list-group-item">No disponer del registro de actividades de tratamiento.</li>
                                                    <li class="list-group-item">No designar un DPO.</li>
                                                    <li class="list-group-item">No notificar las brechas de seguridad.</li>
                                                </ul>
                                                <h3><strong>Sanciones de hasta 20.000.000€ o el 4% del volumen de negocio anual global del ejercicio financiero anterior por:</strong></h3>
                                                <ul class="list-group">
                                                    <li class="list-group-item">No cumplir con los principios y derechos del RGPD.</li>
                                                    <li class="list-group-item">No legalizar las transferencias internacionales de datos.</li>
                                                    <li class="list-group-item">No atender las resoluciones de la Autoridades de Control.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="text-align: center;">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='cdlopd-outer-wrap'>
                        <div class='cdlopd-inner-wrap'>
                            <div class='cdlopd-inner-wrap-content'>
                                <h2>Seleccione los formularios donde desea añadir las cláusulas RGPD</h2>
                                <div class='container' style='width: 100%!important; margin: 1%;'>
                                    <form method='post'>
                                        <label style='float: left; margin-right: 1%; padding-top: 10px;' for='busqueda'><strong>Filtro: </strong></label>
                                        <input style='width: 76%; padding: 7px;' name='busqueda' id='busqueda' type='search' placeholder='Nombre del formulario.' value='<?php
                                        if (isset($busqueda)) {
                                            echo $busqueda;
                                        }
                                        ?>'>
                                        <input type='submit' style='float: right; margin-right: 5%;' name='eliminarbusqueda' class='btn btn-danger' value='&times;'>
                                        <input type='submit' style='float: right;' name='buscar' class='btn btn-primary' value='Buscar'>
                                    </form>
                                </div>
                                <form method='post'>
                                    <?php
                                    //AQUI EMPIEZA QUERY PARA MOSTRAR LOS CONTACT FORMS//
                                    $args = array(
                                        'post_type' => 'wpcf7_contact_form',
                                        'orderby' => 'title'
                                    );

                                    if (isset($busqueda)) {
                                        global $wpdb;
                                        $mypostids = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE '" . $busqueda . "%' ");
                                        if ($mypostids == !0) {
                                            $args = array(
                                                'post_type' => 'wpcf7_contact_form',
                                                'post__in' => $mypostids,
                                                'orderby' => 'title'
                                            );
                                        } else {
                                            $args = array('post_type' => 'wpcf7_contact_form', 'post__in' => array(9876548458892321));
                                        }
                                    }

                                    $the_query = new WP_Query($args);
                                    if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();

                                            $cadena = get_post_meta($the_query->post->ID, '_form', TRUE);
                                            $clausula1 = '<span>[acceptance acceptance-279 default:off] He leído y acepto el <a target="_blank" rel="nofollow noopener noreferrer" href="' . site_url() . '/avisos-legales">Aviso Legal</a> y la <a target="_blank" rel="nofollow noopener noreferrer" href="' . site_url() . '/politica-de-privacidad">Política de Privacidad</a>.</span>';
                                            $clausula2 = "<span>[acceptance acceptance-414 default:off] Declaro, bajo mi propia responsabilidad, ser mayor de 18 años y respondo de manera exclusiva de la veracidad de dicha declaración.</span>";
                                            $clausula3 = "<span>[acceptance acceptance-415 default:off] Acepto recibir la información que la entidad considere oportuno enviarme por correo electrónico o medio de comunicación electrónica equivalente. (Es posible darse de baja en cualquier momento).</span>";
                                            ?>

                                            <div class="cdlopd-formularios"> 
                                                <p style="font-size: 24px; margin-left: 3%;"> <?php the_title(); ?> 
                                                    <?php if (strpos($cadena, $clausula1) !== false || strpos($cadena, $clausula2) !== false || strpos($cadena, $clausula3) !== false) { ?>
                                                        <button onmouseout="cambiaiconoo(<?php the_ID(); ?>)" onmouseover="cambiaicono(<?php the_ID(); ?>)" type="submit" name="action2" class="btn btn-outline-danger" value="<?php the_ID(); ?>" style="float: right; margin-right: 5%; height: 36px;"><img id="icono<?php the_ID(); ?>" rel="icon" height="23px" width="25px" src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/trashtransparentenaranja.png'; ?>"></button>
                                                    <?php } ?>
                                                </p>
                                                <p <?php
                                                if (strpos($cadena, $clausula1) !== false) {
                                                    echo " class='alert alert-success' ";
                                                    $seleccionado = " ";
                                                } else {
                                                    echo " class='alert alert-danger' ";
                                                }
                                                ?>>   
                                                    <input id="main<?php the_ID(); ?>" type="checkbox" <?php
                                                    if (strpos($cadena, $clausula1) !== false) {
                                                        echo " checked ";
                                                    }
                                                    ?> name="formularios[]" value="<?php the_ID(); ?>" onchange="cambio(<?php the_ID(); ?>)" data-toggle="toggle" data-size="mini" id="toggle-two" data-on="Si" data-off="No" > Habilitar cláusula de tratamiento de la información.

                                                </p>
                                                <p <?php
                                                if (strpos($cadena, $clausula2) !== false) {
                                                    echo " class='alert alert-success' ";
                                                } else {
                                                    echo " class='alert alert-danger' ";
                                                }
                                                ?>>   
                                                    <input id="mayores<?php the_ID(); ?>" type="checkbox" <?php
                                                    if (strpos($cadena, $clausula2) !== false) {
                                                        echo " checked ";
                                                    }
                                                    if (!isset($seleccionado)) {
                                                        echo " disabled='disabled' ";
                                                    }
                                                    ?> name="formularios[]" value="mayores<?php the_ID(); ?>" data-toggle="toggle" data-size="mini" id="toggle-two" data-on="Si" data-off="No"> Habilitar cláusula de mayores de 18 años.
                                                </p><p <?php
                                                if (strpos($cadena, $clausula3) !== false) {
                                                    echo " class='alert alert-success' ";
                                                } else {
                                                    echo " class='alert alert-danger'";
                                                }
                                                ?>><input id="comerciales<?php the_ID(); ?>" type="checkbox" <?php
                                                    if (strpos($cadena, $clausula3) !== false) {
                                                        echo " checked ";
                                                    }
                                                    if (!isset($seleccionado)) {
                                                        echo " disabled='disabled' ";
                                                    }
                                                    ?> name="formularios[]" value="comerciales<?php the_ID(); ?>" data-toggle="toggle" data-size="mini" id="toggle-two" data-on="Si" data-off="No">  Habilitar cláusula para recogida de información con fines comerciales.
                                                </p>
                                                <?php unset($seleccionado) ?>
                                            </div>
                                            <?php
                                        endwhile;
                                    else :
                                        ?>
                                        <p> No se ha encontrado formularios de Contact Forms :( </p>
                                    <?php
                                    endif;
                                    wp_reset_postdata();
                                    ?>
                                    <!-- AQUI FINALIZA-->
                                    <div style="text-align: center;">
                                    <input type='submit' name="action" class="btn btn-primary" value='Escribir cláusulas' style="width: 80%!important; margin-bottom: 1%;">
                                    </div>
                                 </form>              
                            </div>
                            <!--FINALIZA PARTE DE CONSTRUCCION DE PAGINAS LOPD -->             
                        </div>                 
                        <div class="cdlopd-banners">
                            <div class=" postbox cdlopd-banner vc_single_image-wrapper vc_box_shadow_3d  vc_box_border_grey">
                                <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img class="vc_single_image-img attachment-medium" src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/300X600.jpg'; ?>" alt="RGPD clikDatos" ></a>
                            </div>
                            <div class=" postbox cdlopd-banner hide-dbpro">
                                <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/clickdatos-lopd-web-cookie-RGPD.jpg'; ?>" alt="RGPD clikDatos" ></a>
                            </div>
                        </div>   
                    </div>
                </div>
                    <?php
                }

                function paginaslodp() {
                    /* AQUI EMPIEZA LA CREACIÓN DE PAGINAS RGPD */
                    if (isset($_POST['crearpagina']) && $_POST['crearpagina'] == "Si") {

                        global $reg_errors;
                        $reg_errors = new WP_Error;

                        $arraycookies = array();
                        $arraycookiesstring = $_POST['arraycookiesstring'];

                        $arraycookiessub = explode('||', $arraycookiesstring);
                        foreach ($arraycookiessub as $cookie) {
                            $partescookie = explode('*', $cookie);
                            array_push($arraycookies, $partescookie);
                        }

                        // Recogemos los datos del formulario una vez validados.
                        $empresaclickdatos = sanitize_text_field($_POST['empresa']);
                        $razonclickdatos = sanitize_text_field($_POST['razon']);
                        $titularclickdatos = sanitize_text_field($_POST['titular']);
                        $domicilioclickdatos = sanitize_text_field($_POST['domicilio']);
                        $poblacionclickdatos = sanitize_text_field($_POST['poblacion']);
                        $provinciaclickdatos = sanitize_text_field($_POST['provincia']);
                        $cpclickdatos = sanitize_text_field($_POST['cp']);
                        $cifclickdatos = sanitize_text_field($_POST['cif']);
                        $telefonoclickdatos = sanitize_text_field($_POST['telefono']);
                        $emailclickdatos = sanitize_email($_POST['email']);
                        $datosclickdatos = sanitize_textarea_field($_POST['datos']);
                        //Comprobamos que los campos obligatorios no están vacios
                        //Comprobamos que código postal tenga una longitud de 5 caracteres
                        //Validacion campos
                        if (get_option("lopdwidget", false) == false) {
                            add_option("lopdwidget", "true", null, false);
                            add_option("lopdwidget_empresa", $empresaclickdatos, null, false);
                            add_option("lopdwidget_razon", $razonclickdatos, null, false);
                            add_option("lopdwidget_titular", $titularclickdatos, null, false);
                            add_option("lopdwidget_domicilio", $domicilioclickdatos, null, false);
                            add_option("lopdwidget_poblacion", $poblacionclickdatos, null, false);
                            add_option("lopdwidget_provincia", $provinciaclickdatos, null, false);
                            add_option("lopdwidget_cp", $cpclickdatos, null, false);
                            add_option("lopdwidget_cif", $cifclickdatos, null, false);
                            add_option("lopdwidget_telefono", $telefonoclickdatos, null, false);
                            add_option("lopdwidget_email", $emailclickdatos, null, false);
                            add_option("lopdwidget_datos", $datosclickdatos, null, false);
                        } else {
                            add_option("lopdwidget", "true", null, false);
                            add_option("lopdwidget_razon", $razonclickdatos, null, false);
                            update_option("lopdwidget_empresa", $empresaclickdatos, false);
                            update_option("lopdwidget_titular", $titularclickdatos, false);
                            update_option("lopdwidget_domicilio", $domicilioclickdatos, false);
                            update_option("lopdwidget_poblacion", $poblacionclickdatos, false);
                            update_option("lopdwidget_provincia", $provinciaclickdatos, false);
                            update_option("lopdwidget_cp", $cpclickdatos, false);
                            update_option("lopdwidget_cif", $cifclickdatos, false);
                            update_option("lopdwidget_telefono", $telefonoclickdatos, false);
                            update_option("lopdwidget_email", $emailclickdatos, false);
                            update_option("lopdwidget_datos", $datosclickdatos, false);
                        }

                        //Aqui comienza la construccion de la tabla de cookies.
                        $cookies = '';
                        $i = 0;
                        foreach ($arraycookies as $c) {
                            $cookieusuarionombre = sanitize_text_field($_POST['nombrecookie' . $i]);
                            $cookieusuariocategoria = sanitize_text_field($_POST['categoria' . $i]);
                            $cookieusuariodescripcion = sanitize_text_field($_POST['descripcion' . $i]);
                            $cookieusuariopropiedad = sanitize_text_field($_POST['propiedad' . $i]);
                            $cookieusuarioduracion = sanitize_text_field($_POST['duracion' . $i]);
                            $cookies .= '<tr align="center" id="' . $cookieusuarionombre . '">';
                            $cookies .= '<td style="border: 1px solid black;">' . $cookieusuarionombre . '</td>';
                            $cookies .= '<td style="border: 1px solid black;">' . $cookieusuariocategoria . '</td>';
                            $cookies .= '<td style="border: 1px solid black;"> ' . $cookieusuariodescripcion . '</td>';
                            $cookies .= '<td style="border: 1px solid black;">' . $cookieusuariopropiedad . '</td>';
                            $cookies .= '<td style="border: 1px solid black;">' . $cookieusuarioduracion . '</td>';
                            $cookies .= '</tr>';
                            $i++;
                            if (get_option("lopdwidget_cookie_" . $cookieusuarionombre, false) == false) {
                                add_option("lopdwidget_cookie_" . $cookieusuarionombre, $cookieusuarionombre, null, false);
                                add_option("lopdwidget_cookie_" . $cookieusuarionombre . "_categoria", $cookieusuariocategoria, null, false);
                                add_option("lopdwidget_cookie_" . $cookieusuarionombre . "_descripcion", $cookieusuariodescripcion, null, false);
                                add_option("lopdwidget_cookie_" . $cookieusuarionombre . "_propiedad", $cookieusuariopropiedad, null, false);
                                add_option("lopdwidget_cookie_" . $cookieusuarionombre . "_duracion", $cookieusuarioduracion, null, false);
                            } else {
                                update_option("lopdwidget_cookie_" . $cookieusuarionombre, $cookieusuarionombre, false);
                                update_option("lopdwidget_cookie_" . $cookieusuarionombre . "_categoria", $cookieusuariocategoria, false);
                                update_option("lopdwidget_cookie_" . $cookieusuarionombre . "_descripcion", $cookieusuariodescripcion, false);
                                update_option("lopdwidget_cookie_" . $cookieusuarionombre . "_propiedad", $cookieusuariopropiedad, false);
                                update_option("lopdwidget_cookie_" . $cookieusuarionombre . "_duracion", $cookieusuarioduracion, false);
                            }
                        }
                        if (get_option("lopdwidget_cookie_contador", false) === false) {
                            add_option("lopdwidget_cookie_contador", $i, null, false);
                        } else {
                            update_option("lopdwidget_cookie_contador", $i, false);
                        }

                        if ($empresaclickdatos == "") {
                            $reg_errors->add("invalid-nombreempresa", "<div class='error message' style='padding: 10px'>Rellene el Nombre de la empresa.</div>");
                        }

                        if ($razonclickdatos == "") {
                            $reg_errors->add("invalid-nombrerazon", "<div class='error message' style='padding: 10px'>Rellene la Razón Social de la empresa.</div>");
                        }

                        if ($titularclickdatos == "") {
                            $reg_errors->add("invalid-nombretitular", "<div class='error message' style='padding: 10px'>Rellene el Nombre del titular.</div>");
                        }

                        if ($domicilioclickdatos == "") {
                            $reg_errors->add("invalid-domicilio", "<div class='error message' style='padding: 10px'>Rellene la dirección de la empresa.</div>");
                        }

                        if ($poblacionclickdatos == "") {
                            $reg_errors->add("invalid-poblacion", "<div class='error message' style='padding: 10px'>Rellene la poblacion de la empresa.</div>");
                        }

                        if ($provinciaclickdatos == "") {
                            $reg_errors->add("invalid-provincia", "<div class='error message' style='padding: 10px'>Rellene la provincia de la empresa.</div>");
                        }

                        if (strlen($cifclickdatos) < 1) {
                            $reg_errors->add("invalid-provincia", "<div class='error message' style='padding: 10px'>El cif debe tener algún digito.</div>");
                        }

                        if (!is_email($_POST['email']) && (!isset($_POST['email']))) {
                            $reg_errors->add("invalid-email", "<div class='error message' style='padding: 10px'>El e-mail no tiene un formato válido</div>");
                        }

                        $telefono = intval($_POST['telefono']);
                        if (strlen($telefono) != 9) {
                            $reg_errors->add("invalid-telefono", "<div class='error message' style='padding: 10px'>El telefono debe tener 9 digitos</div>");
                        }

                        $cpostal = ctype_digit($_POST['cp']);

                        if (strlen($_POST['cp']) != 5 || $cpostal == FALSE) {
                            $reg_errors->add("invalid-postalcode", "<div class='error message' style='padding: 10px'>El código postal debe tener 5 digitos</div>");
                        }
                       
                        if (count($reg_errors->get_error_messages()) > 0) {
                            foreach ($reg_errors->get_error_messages() as $error) {
                                ?>
                                <p><?php echo $error; ?></p>
                                <?php
                            }
                        } else {
                            //Generamos el query a la pagina avisos legales para ver si ya hay una hecha;
                            $querya = new WP_Query(array('name' => 'avisos-legales', 'post_type' => 'page'));
                            //contenido de pagina
                            $contental = __('
                        <div id="clickdatos">
                        <p>En el presente Aviso Legal, el Usuario, podrá encontrar toda la información relativa a las condiciones legales que definen las relaciones entre los usuarios y el responsable de la página web accesible en la dirección URL ' . esc_url(site_url()) . '  (en adelante, el sitio web), que ' . esc_attr($empresaclickdatos) . ' pone a disposición de los usuarios de Internet.<br>
                        La utilización del sitio web implica la aceptación plena y sin reservas de todas y cada una de las disposiciones incluidas en este Aviso Legal. En consecuencia, el usuario del sitio web debe leer atentamente el presente Aviso Legal en cada una de las ocasiones en que se proponga utilizar la web, ya que el texto podría sufrir modificaciones a criterio del titular de la web, o a causa de un cambio legislativo, jurisprudencial o en la práctica empresarial.</p>
                        <h2>1.- DATOS DEL RESPONSABLE TITULAR DEL SITIO WEB.</h2><p>Nombre del titular: ' . esc_attr($titularclickdatos) . '<br>Domicilio social: ' . esc_attr($domicilioclickdatos) . '<br>C.I.F.:  ' . esc_attr($cifclickdatos) . '<br>Teléfono de contacto: ' . esc_attr($telefonoclickdatos) . '<br>
                        Correo electrónico: ' . esc_attr($emailclickdatos) . '<br></p><p>' . esc_attr($titularclickdatos) . ' es el responsable del Sitio Web y se compromete a cumplir con todos los requisitos nacionales y europeos que regulan el uso de los datos personales de los usuarios.</p><p>Este Sitio Web garantiza la protección y confidencialidad de los datos personales que nos proporcionen de acuerdo con lo dispuesto en el Reglamento General de Protección de Datos de Carácter Personal (UE) 2016/679 del Parlamento Europeo y del Consejo, de 27 de abril de 2016, en la Ley Orgánica 3/2018, de Protección de Datos Personales y garantía de los derechos digitales, así como en la Ley de Servicios de la Sociedad de la Información y Comercio Electrónico 34/2002 de 11 de Julio (LSSI-CE)</p><h2>2.- OBJETO.</h2><p>El sitio web facilita a los usuarios del mismo el acceso a información y servicios prestados por ' . esc_attr($empresaclickdatos) . ' a aquellas personas u organizaciones interesadas en los mismos.</p>
                        <p>El acceso y la utilización del Sitio Web atribuye la condición de usuario del Sitio Web (en adelante, el “Usuario”) e implica la aceptación de todas las condiciones incluidas en este Aviso Legal así como de sus modificaciones. La prestación del servicio del Sitio Web tiene una duración limitada al momento en el que el Usuario se encuentre conectado al Sitio Web o a alguno de los servicios que a través del mismo se facilitan. Por tanto, el Usuario debe leer atentamente el presente Aviso Legal en cada una de las ocasiones en que se proponga utilizar el Sitio Web, ya que éste y sus condiciones de uso recogidas en el presente Aviso Legal pueden sufrir modificaciones.</p>
                        <h2>3.- ACCESO Y UTILIZACIÓN DE LA WEB.</h2><p>3.1.- Carácter gratuito del acceso y utilización de la web. El acceso a la web tiene carácter gratuito para los usuarios de la misma, salvo en lo relativo al coste de la conexión a través de la red de telecomunicaciones suministrada por el proveedor de acceso contratado por los usuarios.<br>3.2.- Registro de usuarios. Con carácter general el acceso y utilización de la web no exige la previa suscripción o registro de los usuarios de la misma.<br>3.3.- Los usuarios garantizan y responden, en cualquier caso, de la exactitud, vigencia y autenticidad de los datos personales facilitados, y se comprometen a mantenerlos debidamente actualizados. El usuario acepta proporcionar información completa y correcta en el formulario de contacto o suscripción.</p><p>En ningún caso se recabarán del menor de edad datos relativos a la situación profesional, económica o a la intimidad de los otros miembros de la familia, sin el consentimiento de éstos. Si eres menor de trece años y has accedido a este sitio web sin avisar a tus padres no debes registrarte como usuario.</p><p>En esta web se respetan y cuidan los datos personales de los usuarios. Como usuario debes saber que tus derechos están garantizados.</p>
                        <h2>4.- CONTENIDOS DE LA WEB.</h2><p>El idioma utilizado por el titular en la web será el castellano. ' . esc_attr($empresaclickdatos) . ' no se responsabiliza de la no comprensión o entendimiento del idioma de la web por el usuario, ni de sus consecuencias.<br>' . esc_attr($empresaclickdatos) . ' podrá modificar los contenidos sin previo aviso, así como suprimir y cambiar éstos dentro de la web, como la forma en que se accede a éstos, sin justificación alguna y libremente, no responsabilizándose de las consecuencias que los mismos puedan ocasionar a los usuarios.<br>Se prohíbe el uso de los contenidos de la web para promocionar, contratar o divulgar publicidad o información propia o de terceras personas sin la autorización de ' . esc_attr($empresaclickdatos) . ', ni remitir publicidad o información valiéndose para ello de los servicios o información que se ponen a disposición de los usuarios, independientemente de si la utilización es gratuita o no.<br>Los enlaces o hiperenlaces que incorporen terceros en sus páginas web, dirigidos a esta web, serán para la apertura de la página web completa, no pudiendo manifestar, directa o indirectamente, indicaciones falsas, inexactas o confusas, ni incurrir en acciones desleales o ilícitas en contra de ' . esc_attr($empresaclickdatos) . '</p><h2>5.- MEDIDAS DE SEGURIDAD.</h2><p>Los datos personales comunicados por el usuario podrán ser almacenados en bases de datos automatizadas o no, cuya titularidad corresponde en exclusiva a ' . esc_attr($empresaclickdatos) . ', asumiendo todas las medidas de índole técnica, organizativa y de seguridad que garanticen la confidencialidad, integridad y calidad de la información contenida en las mismas de acuerdo con lo establecido en las normativas vigentes en materia de protección de datos de carácter personal.</p><h2>6.- LIMITACIÓN DE RESPONSABILIDAD.</h2><p>Tanto el acceso a la web como el uso no consentido que pueda efectuarse de la información contenida en la misma es de la exclusiva responsabilidad de quien lo realiza. ' . esc_attr($empresaclickdatos) . ' no responderá de ninguna consecuencia, daño o perjuicio que pudieran derivarse de dicho acceso o uso. ' . esc_attr($empresaclickdatos) . ' no se hace responsable de los errores de seguridad, que se puedan producir ni de los daños que puedan causarse al sistema informático del usuario (hardware y software), o a los ficheros o documentos almacenados en el mismo, como consecuencia de:<br>
                        – la presencia de un virus en el ordenador del usuario que sea utilizado para la conexión a los servicios y contenidos de la web.<br>– un mal funcionamiento del navegador.<br>– y/o del uso de versiones no actualizadas del mismo.<br>' . esc_attr($empresaclickdatos) . ' no se hace responsable de la fiabilidad y rapidez de los hiperenlaces que se incorporen en la web para la apertura de otras. ' . esc_attr($empresaclickdatos) . ' no garantiza la utilidad de estos enlaces, ni se responsabiliza de los contenidos o servicios a los que pueda acceder el usuario por medio de estos enlaces, ni del buen funcionamiento de estas webs.<br>' . esc_attr($empresaclickdatos) . ' no será responsable de los virus o demás programas informáticos que deterioren o puedan deteriorar los sistemas o equipos informáticos de los usuarios al acceder a su web u otras webs a las que se haya accedido mediante enlaces de esta web.</p><h2>7.- EMPLEO DE LA TECNOLOGÍA "COOKIE".</h2><p>El Sitio Web puede emplear cookies o tecnologías similares que se regirán por lo establecido en la Política de Cookies, accesible en todo momento y respetando la confidencialidad e intimidad del usuario, siendo parte integrante del presente Aviso Legal.</p><h2>8.- NAVEGACIÓN.</h2><p>Los servidores de Internet pueden recoger datos no identificables, que puedan incluir, direcciones IP, y otros datos que no pueden ser utilizados para identificar al usuario. Su dirección IP se almacenará en los logs de acceso de forma automática y con la única finalidad de permitir el tránsito por Internet, siendo necesario que su equipo facilite esta dirección IP cuando navega por Internet para que las comunicaciones puedan realizarse. Así mismo, la dirección IP podrá ser utilizada para realizar estadísticas, de manera anonimizada, sobre el número de visitantes de esta web y su procedencia, de forma totalmente transparente a su navegación.</p><h2>9.- PROPIEDAD INTELECTUAL E INDUSTRIAL</h2><p>El usuario conoce y acepta que todos los contenidos y/o cualesquiera otros elementos del sitio web son propiedad de ' . esc_attr($empresaclickdatos) . ', y se compromete a respetar los derechos de propiedad intelectual e industrial titularidad de ' . esc_attr($empresaclickdatos) . '. Cualquier uso de la web o sus contenidos deberá tener un carácter exclusivamente particular.<br>Está reservado exclusivamente a ' . esc_attr($empresaclickdatos) . ', cualquier otro uso que suponga la copia, reproducción, distribución, transformación, comunicación pública o cualquier otra acción similar, de todo o parte de los contenidos de la web, por lo que ningún usuario podrá llevar a cabo estas acciones sin la autorización previa y por escrito de ' . esc_attr($empresaclickdatos) . '</p>
                        <h2>10.- LEGISLACIÓN APLICABLE Y JURISDICCIÓN COMPETENTE</h2><p>El presente Aviso Legal se interpretará y regirá de conformidad con la legislación española. ' . esc_attr($empresaclickdatos) . ' y los usuarios, con renuncia expresa a cualquier otro fuero que pudiera corresponderles, se someten al de los juzgados y tribunales del domicilio del usuario para cualquier controversia que pudiera derivarse del acceso o uso de la web. En el caso de que el usuario tenga su domicilio fuera de España, ' . esc_attr($empresaclickdatos) . ' y el usuario, se someten, con renuncia expresa a cualquier otro fuero, a los juzgados y tribunales del domicilio de ' . esc_attr($poblacionclickdatos) . '</p></div>'
                                    , 'click-datos-lopd');
                            if ($querya->have_posts()) {
                                while ($querya->have_posts()) : $querya->the_post();
                                    $pp = $querya->post->ID;
                                    $contenido = get_the_content();
                                    if (strpos($contenido, 'clickdatos')) {
                                        $my_post = array(
                                            'ID' => $pp,
                                            'post_title' => 'Avisos Legales',
                                            'post_content' => $contental,
                                        );
                                        $pageup = wp_update_post($my_post);
                                        update_option('cdlopd_more_info_page', $pageup);
                                        echo("<div class='updated message' style='padding: 10px'>Se ha actualizado la pagina de Avisos Legales.</div>");
                                    } else {
                                        echo("<div class='error message' style='padding: 10px'>La página de Avisos Legales está creada de forma manual, eliminala para crear una con nuestro plugin.</div>");
                                    }
                                endwhile;
                                wp_reset_postdata();
                            } else {
                                // Creamos la pagina Avisos legales
                                $pagenameal = __('Avisos Legales', 'click-datos-lopd');
                                $cpageal = get_page_by_title($pagenameal); // Comprobamos si existe la pagina
                                if (!$cpageal) {
                                    //si no existe la creamos
                                    $pageal['post_type'] = 'page';
                                    $pageal['post_content'] = $contental;
                                    $pageal['post_parent'] = 0;
                                    $pageal['post_status'] = 'publish';
                                    $pageal['post_title'] = $pagenameal;
                                    $pageidal = wp_insert_post($pageal);
                                    update_option('cdlopd_more_info_page', $pageidal);
                                    echo("<div class='updated message' style='padding: 10px'>Se ha creado la página de Avisos Legales.</div>");
                                } else {
                                    echo("<div class='error message' style='padding: 10px'>La página de Avisos Legales esta en la papelera, deberá eliminarla para continuar.</div>");
                                }
                            }

                            //Generamos el query a la pagina pagina de cookies para ver si ya hay una hecha;
                            $queryc = new WP_Query(array('name' => 'politica-de-cookies', 'post_type' => 'page'));
                            //contenido de pagina
                            $contentpc = __('<h2>POLÍTICA DE COOKIES</h2><div id="clickdatos"><p>En la web <a target="_blank" rel="nofollow noopener noreferrer" href="' . esc_url(site_url()) . '">' . esc_url(site_url()) . '</a> (en adelante, el Sitio Web) utilizamos cookies para facilitar la relación de los visitantes con nuestro contenido y para permitir elaborar estadísticas sobre las visitantes que recibimos.</p><p>Le informamos que podemos utilizar cookies con la finalidad de facilitar su navegación a través del Sitio Web, distinguirle de otros usuarios, proporcionarle una mejor experiencia en el uso del mismo, e identificar problemas para mejorar nuestro Sitio Web.</p>
<p><strong>¿Qué son las cookies?</strong><br>Se denominan cookies a unos pequeños archivos que se graban en el navegador utilizado por cada visitante de nuestra web para que el servidor pueda recordar la visita de ese usuario con posterioridad cuando vuelva a acceder a nuestros contenidos. Esta información no revela su identidad, ni dato personal alguno, ni accede al contenido almacenado en su pc, pero sí que permite a nuestro sistema identificarle a usted como un usuario determinado que ya visitó la web con anterioridad, visualizó determinadas páginas, etc. y además permite guardar sus preferencias personales e información técnica.</p>
<p>En cumplimiento de la Directiva 2009/136/CE, desarrollada en nuestro ordenamiento por el apartado segundo del artículo 22 de la LSSI, siguiendo las directrices de la AEPD, procedemos a informarle detalladamente del uso que se realiza en nuestra web.</p>
<p>A continuación, se realiza una clasificación de las cookies en función de una serie de categorías. No obstante es necesario tener en cuenta que una misma cookie puede estar incluida en más de una categoría.</p>
<div><strong>Tipos de cookies según la entidad que las gestiona</strong>
<ul><li>Cookies propias: Son aquéllas que se envían al equipo terminal del usuario desde un equipo o dominio gestionado por el propio editor.</li><li>Cookies de terceros: Son aquéllas que se envían al equipo terminal del usuario desde un equipo o dominio que no es gestionado por el editor, sino por otra entidad que trata los datos.</li></ul>
<div><strong>Tipos de cookies según el plazo de tiempo que permanecen activas</strong>
<ul><li>Cookies de sesión: Son un tipo de cookies diseñadas para recabar y almacenar datos mientras el usuario accede a una página web.</li><li>Cookies persistentes: Son un tipo de cookies en el que los datos siguen almacenados en el terminal y pueden ser accedidos y tratados durante un periodo definido por el responsable de la cookie, y que puede ir de unos minutos a varios años.</li></ul></div></div>
<div><strong>Tipos de cookies según su finalidad</strong>
<ul><li>Cookies técnicas: Aquellas que permiten al usuario la navegación a través de una página web, plataforma o aplicación y la utilización de las diferentes opciones o servicios que en ella existan.</li><li>Cookies de personalización: Son aquéllas que permiten al usuario acceder al servicio con algunas características de carácter general predefinidas en función de una serie de criterios en el terminal del usuario.</li><li>Cookies publicitarias: Son aquellas que permiten la gestión, de la forma más eficaz posible, de los espacios publicitarios que, en su caso, el editor haya incluido en una página web.</li><li>Cookies de publicidad comportamental: Son aquéllas que permiten la gestión, de la forma más eficaz posible, de los espacios publicitarios que, en su caso, el editor haya incluido en una página web. Estas cookies almacenan información del comportamiento de los usuarios obtenida a través de la observación continuada de sus hábitos de navegación, lo que permite desarrollar un perfil específico para mostrar publicidad en función del mismo.</li><li>Cookies de análisis: Son aquellas que permiten al responsable de las mismas, el seguimiento y análisis del comportamiento de los usuarios de los sitios web a los que están vinculadas. La información recogida mediante este tipo de cookies se utiliza en la medición de la actividad de los sitios web, con el fin de introducir mejoras en función del análisis de los datos de uso que hacen los usuarios del servicio.</li></ul></div>
<p>Actualmente, la mayoría de los navegadores vienen configurados por defecto para bloquear la instalación de cookies de publicidad o terceros en su equipo. El usuario puede ampliar las restricciones de origen, impidiendo la entrada de cualquier tipo de cookie, o eliminar dichas restricciones, aceptando la entrada de cualquier tipo de cookies. Si está interesado en admitir cookies de publicidad o de terceros, podrá configurar su navegador a tal fin.</p>
<p>La aceptación realizada por el usuario, haciendo click en el botón de <strong>ACEPTAR</strong> mostrado en la información inicial sobre cookies, implica que está consintiendo expresamente al responsable para su utilización, pudiendo ejercer sus derechos y revocar su consentimiento en cualquier momento, a través de solicitud a ' . esc_attr($empresaclickdatos) . '.</p>
    
A continuación le informamos detalladamente todas las cookies que podrían llegar a instalarse desde nuestro sitio web. En función de su navegación, de la configuración de su navegador y de la aceptación o rechazo de las mismas, podrán instalarse todas o sólo algunas de ellas.<br>
&nbsp;
<div id="listadodecookies">
    <table style="border: 1px solid black;">
        <tr align="center">
            <td style="border: 1px solid black;">Nombre</td>
            <td style="border: 1px solid black;">Categoria</td>
            <td style="border: 1px solid black;">Descripción</td>
            <td style="border: 1px solid black;">Propiedad</td>
            <td style="border: 1px solid black;">Duración</td>
        </tr>
        ' .
        $cookies
    . '</table>
</div>

Es posible que actualicemos la Política de Cookies de nuestro Sitio Web, por ello le recomendamos revisar esta política cada vez que acceda a nuestro Sitio Web con el objetivo de estar adecuadamente informado sobre cómo y para qué usamos las cookies.<br>

Si usted no desea que se guarden cookies en su navegador o prefiere recibir una información cada vez que una cookie solicite instalarse, puede configurar sus opciones de navegación para que se haga de esa forma. La mayor parte de los navegadores permiten la gestión de las cookies de 3 formas diferentes:
<ul>
    <li>Las cookies son siempre rechazadas;</li>
    <li>El navegador pregunta si el usuario desea instalar cada cookie;</li>
    <li>Las cookies son siempre aceptadas;</li>
</ul>
Su navegador también puede incluir la posibilidad de seleccionar con detalle las cookies que desea que se instalen en su ordenador. En concreto, el usuario puede normalmente aceptar alguna de las siguientes opciones:
<ul> 	
    <li>Rechazar las cookies de determinados dominios;</li>
    <li>Rechazar las cookies de terceros;</li>
    <li>Aceptar cookies como no persistentes (se eliminan cuando el navegador se cierra);</li>
    <li>Permitir al servidor crear cookies para un dominio diferente.</li>
</ul>
Para permitir, conocer, bloquear o eliminar las cookies instaladas en su equipo puede hacerlo mediante la configuración de las opciones del navegador instalado en su ordenador.

Puede encontrar información sobre cómo configurar los navegadores más usados en las siguientes ubicaciones:
<ul>
    <li><strong>Internet Explorer</strong>: Herramientas -&gt; Opciones de Internet -&gt; Privacidad -&gt; Configuración. Para más información, puede consultar el <a target="_blank" rel="nofollow noopener noreferrer" href="http://windows.microsoft.com/es-ES/windows/support">soporte de Microsoft</a> o la Ayuda del navegador.</li>
    <li><strong>Firefox</strong>: Herramientas -&gt; Opciones -&gt; Privacidad -&gt; Historial -&gt; Configuración Personalizada. Para más información, puede consultar el <a  target="_blank" rel="nofollow noopener noreferrer"href="http://support.mozilla.org/es/home">soporte de Mozilla</a> o la Ayuda del navegador.</li>
    <li><strong>Chrome</strong>: Configuración -&gt; Mostrar opciones avanzadas -&gt; Privacidad -&gt; Configuración de contenido. Para más información, puede consultar el <a target="_blank" rel="nofollow noopener noreferrer"href="http://support.google.com/chrome/?hl=es">soporte de Google</a> o la Ayuda del navegador.</li>
    <li><strong>Safari</strong>: Preferencias -&gt; Seguridad. Para más información, puede consultar el <a  target="_blank" rel="nofollow noopener noreferrer"href="http://www.apple.com/es/support/safari/">soporte de Apple</a> o la Ayuda del navegador.</li>
</ul>
</div>', 'click-datos-lopd');

                            if ($queryc->have_posts()) {
                                while ($queryc->have_posts()) : $queryc->the_post();
                                    $pp = $queryc->post->ID;
                                    $contenido = get_the_content();
                                    if (strpos($contenido, 'clickdatos')) {
                                        $my_post = array(
                                            'ID' => $pp,
                                            'post_title' => 'Política de Cookies',
                                            'post_content' => $contentpc,
                                        );
                                        $pageup = wp_update_post($my_post);
                                        update_option('cdlopd_more_info_page', $pageup);
                                        echo("<div class='updated message' style='padding: 10px'>Se ha actualizado la pagina de Política de Cookies.</div>");
                                    } else {
                                        echo("<div class='error message' style='padding: 10px'>La página de Política de Cookies está creada de forma manual, eliminala para crear una con nuestro plugin.</div>");
                                    }
                                endwhile;
                                wp_reset_postdata();
                            } else {
                                // Creamos la pagina Politica de Cookies
                                $pagenamepc = __('Política de Cookies', 'click-datos-lopd');
                                $cpagepc = get_page_by_title($pagenamepc); // Comprobamos si existe la pagina
                                if (!$cpagepc) {
                                    //si no existe la creamos
                                    $pagepc['post_type'] = 'page';
                                    $pagepc['post_content'] = $contentpc;
                                    $pagepc['post_parent'] = 0;
                                    $pagepc['post_status'] = 'publish';
                                    $pagepc['post_title'] = $pagenamepc;
                                    $pageidpc = wp_insert_post($pagepc);
                                    update_option('cdlopd_more_info_page', $pageidpc);
                                    echo("<div class='updated message' style='padding: 10px'>Se ha creado la pagina de Política de Cookies.</div>");
                                } else {
                                    echo("<div class='error message' style='padding: 10px'>La página de Política de Cookies esta en la papelera, deberá eliminarla para continuar.</div>");
                                }
                            }

                            //Generamos el query a la pagina pagina de cookies para ver si ya hay una hecha;
                            $queryp = new WP_Query(array('name' => 'politica-de-privacidad', 'post_type' => 'page'));
                            //contenido de pagina
                            $contentpp = __('<div id="clickdatos"><h2>POLÍTICA DE PRIVACIDAD Y PROTECCIÓN DE DATOS</h2><p>En cumplimiento del Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales, por el que se deroga la directiva 95/46/CE (en adelante, RGPD), de la Ley 34/2002, de 11 de julio, de servicios de la sociedad de la información y comercio electrónico (en adelante, LSSI-CE) y de la Ley Orgánica 3/2018, de Protección de Datos Personales y garantía de los derechos digitales (en adelante, LOPDGDD), ' . esc_attr($empresaclickdatos) . ' garantiza la protección y confidencialidad de los datos personales, de cualquier tipo que nos proporcionen nuestros clientes, de acuerdo con lo dispuesto en el Reglamento General de Protección de Datos de Carácter Personal.</p>
<p>Los datos facilitados serán tratados en los términos establecidos en el RGPD, en ese sentido ' . esc_attr($empresaclickdatos) . ' ha adoptado los niveles de protección que legalmente se exigen, y ha instalado todas las medidas técnicas a su alcance para evitar la pérdida, mal uso, alteración, acceso no autorizado por terceros, expuestos a continuación. No obstante, el usuario debe ser consciente de que las medidas de seguridad en Internet no son inexpugnables.</p>
<p><strong>Responsable del tratamiento</strong><br>Denominación: ' . esc_attr($empresaclickdatos) . '<br>CIF: ' . esc_attr($cifclickdatos) . '<br>Dirección: ' . esc_attr($domicilioclickdatos) . '<br>Teléfono: ' . esc_attr($telefonoclickdatos) . '<br>Correo electrónico: ' . esc_attr($emailclickdatos) . '</p>
<p><strong>Finalidad del tratamiento</strong><br>Todos los datos facilitados por nuestros clientes y/o visitantes en la web de ' . esc_attr($empresaclickdatos) . ' o a su personal, serán incluidos en registro de actividades de tratamiento de datos de carácter personal, creado y mantenido bajo la resposabilidad de ' . esc_attr($empresaclickdatos) . ', imprescindibles para prestar los servicios solicitados por los usuarios, o para resolver las dudas o cuestiones planteadas por nuestros visitantes. Nuestra política es no elaborar perfiles sobre los usuarios de nuestros servicios.</p>
<p><strong>Legitimidad del tratamiento</strong>
<ol type="a">
    <li>Relación contractual: Es la que aplica cuando compra uno de nuestros productos o contrata alguno de nuestros servicios.</li>
    <li>Interés legítimo: Para atender a las consultas y reclamaciones que nos plantee y para gestionar el cobro de las cantidades adeudadas.</li>
    <li>Su consentimiento: Si es usuario de nuestra web, mediante la marcación de la casilla que figura en el formulario de contacto, nos autoriza a que le remitamos las comunicaciones necesarias para dar respuesta a la consulta o solicitud de información planteada.</li>
</ol>
</p>
<p><strong>Destinatarios</strong><br>No cedemos sus datos personales a nadie, a excepción de aquellas entidades públicas o privadas a las cuales estemos obligados a facilitar sus datos personales con motivo del cumplimiento de alguna ley. Por poner un ejemplo, la Ley Tributaria obliga a facilitar a la Agencia Tributaria determinada información sobre operaciones económicas que superen una determinada cantidad.</p>
<p>En el caso de que, al margen de los supuestos comentados, necesitemos dar a conocer  su información personal a otras entidades, le solicitaremos previamente su permiso a través de opciones claras que le permitirán decidir a este respecto.</p>
<p><strong>Comunicación</strong><br>No realizaremos transferencias internacionales de sus datos personales para ninguna de las finalidades indicadas.<br>(<strong>NOTA INTERNA</strong>: ESTE PÁRRAFO SE HA DE CAMBIAR POR EL RESPONSABLE SI REALMENTE SE PRODUCEN TRANSFERENCIAS INTERNACIONALES. TE RECOMENDAMOS SOLICITAR ASESORAMIENTO SI NO SABES CÓMO REFLEJARLO CORRECTAMENTE PARA CUMPLIR CON LA NORMATIVA)</p>
<p><strong>Conservación</strong><br>Solo conservaremos sus datos personales durante el tiempo que resulte necesario para lograr los fines para los que fueron recabados. A la hora de determinar el oportuno periodo de conservación, examinamos los riesgos que conlleva el tratamiento, así como nuestras obligaciones contractuales, legales y normativas, las políticas internas de conservación de datos y nuestros intereses de negocio legítimos descritos en el presente Aviso de Privacidad y Política de Cookies.</p>
<p>En este sentido, ' . esc_attr($empresaclickdatos) . ' conservará los datos personales una vez terminada su relación con Ud., debidamente bloqueados, durante el plazo de prescripción de las acciones que pudieran derivarse de la relación mantenida con el interesado.</p>
<p>Una vez bloqueados, sus datos resultarán inaccesibles para ' . esc_attr($empresaclickdatos) . ', y no serán tratados excepto para su puesta a disposición a las Administraciones públicas, Jueces y Tribunales, para la atención de las posibles responsabilidades nacidas de los tratamientos, así como para el ejercicio y defensa de reclamaciones ante la Agencia Española de Protección de Datos.</p>
<p><strong>Seguridad</strong><br>Empleamos todos los esfuerzos razonables para mantener la confidencialidad de la información personal que se trate en nuestros sistemas. Mantenemos estrictos niveles de seguridad para proteger los datos de carácter personal que procesamos frente a pérdidas fortuitas y a accesos, tratamientos o revelaciones no autorizados, habida cuenta del estado de la tecnología, la naturaleza y los riesgos a que están expuestos los datos. No obstante, no podemos responsabilizarnos del uso que Ud. haga de los datos (incluido usuario y contraseña) que utilice en nuestra web. Nuestro personal sigue estrictas normas de privacidad y en el caso de que contratemos a terceros para prestar servicios de soporte, les exigimos que acaten las mismas normas y nos permitan auditarles para verificar su cumplimiento.</p>
<p><strong>Sus derechos</strong><br>Le informamos que podrá ejercer los siguientes derechos:
<ol type="a">
    <li>Derecho de acceso a sus datos personales, para saber cuáles están siendo objeto de tratamiento y las operaciones de tratamiento llevadas a cabo con ellos;</li>
    <li>Derecho de rectificación de cualquier dato personal inexacto;</li>
    <li>Derecho de supresión de sus datos personales, cuando esto sea posible (por ejemplo, por imperativo legal);</li>
    <li>Derecho de limitación del tratamiento de sus datos personales cuando la exactitud, la legalidad o la necesidad del tratamiento de los datos resulte dudosa, en cuyo caso, podremos conservarlos para el ejercicio o la defensa de reclamaciones.</li>
    <li>Derecho de oposición al tratamiento de sus datos personales, cuando la base legal que nos habilite para su tratamiento de las indicadas sea nuestro interés legítimo. «Nombre_Empresa» dejará de tratar tus datos salvo que tenga un interés legítimo o sea necesario para la defensa de reclamaciones.</li>
    <li>Derecho a la portabilidad de sus datos, cuando la base legal que nos habilite para su tratamiento sea la existencia de una relación contractual o su consentimiento.</li>
    <li>Derecho a revocar el consentimiento otorgado a ' . esc_attr($empresaclickdatos) . '</li>
</ol>
</p>
Para ejercitar sus derechos, puede hacerlo de manera gratuita y en cualquier momento contactando con nosotros en la dirección ' . esc_attr($domicilioclickdatos) . ', adjuntando copia de su DNI.<br>
<p><strong>Tutela de derechos</strong><br>En caso de que entienda que sus derechos han sido desatendidos por nuestra entidad, puede formular una reclamación en la Agencia Española de Protección de Datos, a través de alguno de los medios siguientes:
<ul>
    <li>Sede electrónica: <a href="https://www.aepd.es">https://www.aepd.es</a></li>
    <li>Correo postal: Agencia Española de Protección de Datos, C/ Jorge Juan, 6, 28001, Madrid </li>
    <li>Teléfono: 901.100.099 y 912.663.517</li>
</ul>
Formular una reclamación en la Agencia Española de Protección de Datos no conlleva ningún coste y no es necesaria la asistencia de abogado ni procurador.</p>
<strong>Actualizaciones</strong><br>' . esc_attr($empresaclickdatos) .' se reserva el derecho a modificar la presente política para adaptarla a novedades legistlativas o jurisprudenciales que puedan afectar el cumplimiento de la misma.
</div>', 'click-datos-lopd');

                            if ($queryp->have_posts()) {
                                while ($queryp->have_posts()) : $queryp->the_post();
                                    $pp = $queryp->post->ID;
                                    $contenido = get_the_content();
                                    if (strpos($contenido, 'clickdatos')) {
                                        $my_post = array(
                                            'ID' => $pp,
                                            'post_title' => 'Política de Privacidad',
                                            'post_content' => $contentpp,
                                        );
                                        $pageup = wp_update_post($my_post);
                                        update_option('cdlopd_more_info_page', $pageup);
                                        echo("<div class='updated message' style='padding: 10px'>Se ha actualizado la pagina de Política de Privacidad.</div>");
                                    } else {
                                        echo("<div class='error message' style='padding: 10px'>La página de Política de Privacidad está creada de forma manual, eliminala para crear una con nuestro plugin.</div>");
                                    }
                                endwhile;
                                wp_reset_postdata();
                            } else {
                                // Creamos la pagina Politica de Privacidad
                                $pagenamepp = __('Política de Privacidad', 'click-datos-lopd');
                                $cpagepp = get_page_by_title($pagenamepp); // Comprobamos si existe la pagina
                                if (!$cpagepp) {
                                    //si no existe la creamos
                                    $pagepp['post_type'] = 'page';
                                    $pagepp['post_content'] = $contentpp;
                                    $pagepp['post_parent'] = 0;
                                    $pagepp['post_status'] = 'publish';
                                    $pagepp['post_title'] = $pagenamepp;
                                    $pageidpp = wp_insert_post($pagepp);
                                    update_option('cdlopd_more_info_page', $pageidpp);
                                    echo("<div class='updated message' style='padding: 10px'>Se ha creado la pagina de Política de Privacidad.</div>");
                                } else {
                                    echo("<div class='error message' style='padding: 10px'>La página de Política de Privacidad esta en la papelera, deberá eliminarla para continuar.</div>");
                                }
                            }
                        }
                    }
                    //AQUI EMPIEZA LA PARTE DE RECOGER LOS DATOS DE LA EMPRESA Y SI ESTAN LOS AÑADIMOS A LAS VARIABLES.
                    $empresa = "";
                    $titular = "";
                    $domicilio = "";
                    $poblacion = "";
                    $provincia = "";
                    $cp = "";
                    $cif = "";
                    $telefono = "";
                    $email = "";
                    $datos = "";
                    $razon = "";
                    if (get_option("lopdwidget", false)) {
                        $empresa = sanitize_text_field(get_option("lopdwidget_empresa", false));
                        $razon = sanitize_text_field(get_option("lopdwidget_razon", false));
                        $titular = sanitize_text_field(get_option("lopdwidget_titular", false));
                        $domicilio = sanitize_text_field(get_option("lopdwidget_domicilio", false));
                        $poblacion = sanitize_text_field(get_option("lopdwidget_poblacion", false));
                        $provincia = sanitize_text_field(get_option("lopdwidget_provincia", false));
                        $cp = sanitize_text_field(get_option("lopdwidget_cp", false));
                        $cif = sanitize_text_field(get_option("lopdwidget_cif", false));
                        $telefono = sanitize_text_field(get_option("lopdwidget_telefono", false));
                        $email = sanitize_text_field(get_option("lopdwidget_email", false));
                        $datos = sanitize_text_field(get_option("lopdwidget_datos", false));
                    }
                    //AQUI EMPEZAR�? LA PARTE DE RECOGER LOS DATOS DE LAS COOKIES INTRODUCIDOS ANTERIORMENTE
                    ?>
                    <script>
                        jQuery(document).ready(function () {
                            jQuery("#boton").click(function (event) {
                                document.getElementById('resultados').removeAttribute('style');
                                var nombreu = document.getElementById("nombreu").value;
                                var nombrec = document.getElementById("nombrec").value;
                                var email = document.getElementById("email").value;
                                var url = document.getElementById("url").value;
                                var mensaje = document.getElementById("mensaje").value;
                                if (nombreu === "" || email === "" || url === "") {
                                    document.getElementById('resultados').setAttribute('style', 'color: red;');
                                    jQuery('#resultados').html("Rellene todos los datos con * del formulario. <br>Por favor.");
                                } else if (!jQuery('#checkprivacidad').is(':checked')) {
                                    document.getElementById('resultados').setAttribute('style', 'color: red;');
                                    jQuery('#resultados').html("Debes aceptar la Política de Privacidad para poder enviar el formulario.");
                                } else {
                                    jQuery.post(url + '/wp-content/plugins/click-datos-lopd/admin/mail-contacto.php', {nombreu: nombreu, nombrec: nombrec, email: email, url: url, mensaje: mensaje}, function (respuesta) {
                                        if (respuesta === 'Tu correo ha sido enviado correctamente.') {
                                            document.getElementById('resultados').setAttribute('style', 'color: green;');
                                        } else if (respuesta === 'Hubo un problema al enviar el email, intentalo de nuevo.') {
                                            document.getElementById('resultados').setAttribute('style', 'color: red;');
                                        }
                                        jQuery('#resultados').html(respuesta);
                                    });
                                }
                            });
                        });
                    </script>
                    <div class='wrap'>
                        <h1>RGPD ClickDatos - Creación de páginas </h1>
                        <?php
                        $cu = wp_get_current_user();
                        ?>
                        <!-- Button trigger modal -->
                        <button type="button" style="margin: auto;" class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#myModal">¿Necesitas ayuda?</button>
                        <!-- Modal -->
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" style="width: 72% !important;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="myModalLabel">Servicio de Ayuda</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div role="tabpanel">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active">
                                                    <a href="#ObtenSoporte" aria-controls="ObtenSoporte" role="tab" data-toggle="tab">Consulta tu web</a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#queeslargpd" aria-controls="queeslargpd" role="tab" data-toggle="tab">¿Qué es la RGPD?</a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#guiasydirectrices" aria-controls="guiasydirectrices" role="tab" data-toggle="tab">Guías y Directrices</a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#principiosdelrgpd" aria-controls="principiosdelrgpd" role="tab" data-toggle="tab">Principios</a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#nuevasobligaciones" aria-controls="nuevasobligaciones" role="tab" data-toggle="tab">Nuevas Obligaciones y Derechos</a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#porquedebe" aria-controls="porquedebe" role="tab" data-toggle="tab">¿Por qué debe adaptarse?</a>
                                                </li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content"  style="text-align: center;">
                                                <div role="tabpanel" class="tab-pane active" id="ObtenSoporte">
                                                    <div class="form-horizontal">
                                                        <h1>ClickDatos le hará una valoración a su web</h1><h2>Comprobará si cumple con la RGPD y le responderemos cuanto antes, de forma gratuíta.</h2>
                                                        <p>Si tiene alguna duda sobre la RGPD, puede consultar las demás pestañas donde hablamos de los requisitos para cumplir con esta Ley.</p>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-2">Nombre:*</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" name="nombreu" id="nombreu" value="<?php echo $cu->user_login; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-2">Nombre Completo:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" name="nombrec" id="nombrec" value="<?php echo $cu->user_firstname . ' ' . $cu->user_lastname; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-2">Email:*</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" name="email" id="email" value="<?php echo $cu->user_email; ?>">
                                                                <input type="hidden" id="url" value="<?php echo get_site_url(); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-2">Mensaje:</label>
                                                            <div class="col-sm-8">
                                                                <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Introduce un mensaje..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-3"></div>
                                                            <div class="col-sm-5">
                                                                <input type="checkbox" name="privacidad" id="checkprivacidad"> He leído y acepto <a target="_blank" rel="nofollow noopener noreferrer" href="https://clickdatos.es/politica-privacidad-plugin-rgpd-clickdatos/">Política de Privacidad</a> de ClickDatos.<br>
                                                                <button id="boton" class="btn btn-primary btn-lg" style="margin-top: 4%; margin-bottom: 4%;">Enviar consulta</button><br>
                                                                <label id="resultados"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="queeslargpd">
                                                    <h2 style="text-align: center;">¿Qué es el Reglamento General de Protección de Datos?</h2>
                                                    <p>El pasado 25 de mayo de 2016 entró en vigor el nuevo Reglamento General de Protección de Datos (RGPD) que deroga la Directiva 95/46/CE y viene a sustituir la normativa que actualmente estaba vigente en los países de la UE . Tiene lugar con motivo de la&nbsp;<a title="El enlace se abrirá en una nueva ventana" href="http://www.europarl.europa.eu/news/es/news-room/20160407IPR21776/reforma-de-la-protecci%C3%B3n-de-datos-%E2%80%93-nuevas-reglas-adaptadas-a-la-era-digital" target="_blank" rel="nofollow noopener noreferrer">reforma normativa realizada por la Unión Europea</a>&nbsp; cuyo objetivo es adaptar la legislación vigente a las exigencias en los niveles de seguridad que demanda la realidad digital actual.</p>
                                                    <p>Será de aplicación a partir del <strong>25 de mayo de 2018</strong> para todos los paises de la UE. Durante ese plazo de dos años hasta su implantación total, las empresas, los autónomos, asociaciones, comunidades de vecinos y las administraciones tienen la obligación de adaptarse a las nuevas directrices.</p>
                                                    <p><a title="El enlace se abrirá en una nueva ventana" href="https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:32016R0679" target="_blank" rel="nofollow noopener noreferrer">Reglamento (UE) 2016/679</a> del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales y a la libre circulación de estos datos y por el que se deroga la Directiva 95/46/CE por el Reglamento General de Protección de Datos.</p>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="guiasydirectrices">
                                                    <h2>Guías y directrices sobre el reglamento y su aplicación</h2>
                                                    <ul>
                                                        <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/guia-para-el-cumplimiento-del-deber-de-informar/" target="_blank" rel="nofollow noopener noreferrer">Guía para el cumplimiento del deber de informar</a></li>
                                                        <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/directrices-para-la-elaboracion-de-contratos-entre-responsables-y-encargados-del-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Directrices para la elaboración de contratos entre responsables y encargados del tratamiento</a></li>
                                                        <li class="list-group-item"><a href="https://clickdatos.es/guia-rgpd-para-responsables-de-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Guía del Reglamento General de Protección de Datos para responsables de tratamiento</a></li>
                                                        <li class="list-group-item"><a href="https://clickdatos.es/orientaciones-y-garantias-en-los-procedimientos-de-anonimizacion-de-datos-personales/" target="_blank" rel="nofollow noopener noreferrer">Orientaciones y garantías en los procedimientos de anonimización de datos personales</a></li>
                                                    </ul>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="principiosdelrgpd">
                                                    <h2>Principios del RGPD</h2>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">El tratamiento de datos sólo será leal y lícito si la información es accesible y comprensible.</li>
                                                        <li class="list-group-item">Los datos han de ser recogidos para un fin determinado y sólo utilizados para dicho fin, considerando la existencia de excepciones para ciertos supuestos concretos.</li>
                                                        <li class="list-group-item">Los datos personales han de ser los adecuados, concretos y siempre limitados a la necesidad concreta para ser recabados</li>
                                                        <li class="list-group-item">Los datos han de ser precisos y deben ser actualizados en todo momento.</li>
                                                        <li class="list-group-item">Los datos han de ser mantenidos tan sólo durante el tiempo necesario para cumplir la finalidad de los mismos y han de ser cancelados, estableciendo el responsable de estos el plazo para la supresión o revisión de los mismos.</li>
                                                        <li class="list-group-item">Ha de velarse por la seguridad de los datos utilizando todos los medios técnicos y organizativos adecuados.</li>
                                                        <li class="list-group-item">Se establece que el responsable del tratamiento será además el responsable del cumplimiento de todos los principios aquí citados</li>
                                                    </ul>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="nuevasobligaciones">
                                                    <h2>Nuevos derechos para los dueños de los datos</h2>
                                                    <p>Además de los derechos <strong>ARCO</strong> vigentes actualmente (acceso, rectificación, cancelación y oposición) se añaden otros 3:</p>
                                                    <ul class="list-group">
                                                        <li class="list-group-item"><strong>Derecho de Supresión:</strong>&nbsp;Las personas podrán solicitar la eliminación total de sus datos personales en determinados casos.</li>
                                                        <li class="list-group-item"><strong>Derecho de Limitación:</strong>&nbsp;Permite la suspensión del tratamiento de los datos del interesado en algunos casos como impugnaciones o la conservación de los datos aún en casos en los que no sea necesaria por alguna razón administrativa o legal.</li>
                                                        <li class="list-group-item"><strong>Derecho de Portabilidad:</strong> El dueño de los datos personales tendrá la posibilidad de solicitar una copia al responsable de su tratamiento y obtenerlos en un formato estandarizado y mecanizado.</li>
                                                    </ul>
                                                    <h2>Nuevas obligaciones para empresas, administraciones y otras entidades</h2>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">En ciertos supuestos se establece la obligatoriedad de la designación de un <strong>Delegado de Protección de Datos (DPO).</strong></li>
                                                        <li class="list-group-item">En determinados casos se realizarán <strong> Evaluaciones de Impacto</strong> sobre la Privacidad para averiguar los riesgos que pudieran existir con el tratamiento de ciertos datos personales y traten de establecer medidas y directrices para minimizarlos o eliminarlos. Las empresas de ámbito multinacional tendrán como interlocutor a un sólo <strong>organismo de control nacional </strong>(ventanilla única).</li>
                                                        <li class="list-group-item">Se establece la obligatoriedad de&nbsp;<strong>informar a las autoridades&nbsp;</strong>de control competentes y a los&nbsp;<strong>afectados&nbsp;</strong>en casos graves de las brechas de seguridad que pudieran encontrase, estableciendo para ello un plazo máximo de&nbsp;<strong>72 horas&nbsp;</strong>desde su detección.</li>
                                                        <li class="list-group-item">Se amplía el listado de los&nbsp;<strong>datos especialmente protegidos&nbsp;</strong>(datos sensibles) con los datos genéticos, biométricos, infracciones y condenas penales.</li>
                                                        <li class="list-group-item">El responsable encargado del tratamiento de los datos ha de&nbsp;<strong>garantizar el cumplimiento&nbsp;</strong>de la norma, dato que influirá en la selección del mismo.</li>
                                                        <li class="list-group-item">Se establece un marco de garantías y mecanismos de seguimiento más estrictos para el caso de las&nbsp;<strong>transferencias internacionales fuera de la UE.</strong></li>
                                                        <li class="list-group-item">Se prevee la creación de&nbsp;<strong>sellos y certificaciones&nbsp;</strong>que acrediten la Responsabilidad Proactiva de las organizaciones.</li>
                                                        <li class="list-group-item">La obligación de inscribir los ficheros desaparece y será sustituída por un&nbsp;<strong>control interno&nbsp;</strong>y , en ocasiones, por un&nbsp;<strong>inventario&nbsp;</strong>de las operaciones de tratamiento realizadas.</li>
                                                        <li class="list-group-item">Las&nbsp;<strong>sanciones&nbsp;</strong>por incumplimiento&nbsp;<strong>aumentan su cuantía&nbsp;</strong>pudiendo alcanzar los&nbsp;<strong>20 millones de euros&nbsp;</strong>o el&nbsp;<strong>4% de la facturación&nbsp;</strong>global de la empresa.</li>
                                                    </ul>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="porquedebe">
                                                    <h2>¿Por qué debería contratar la auditoría y adaptarme al RGPD?</h2>
                                                    <p>El organismo estatal encargado de supervisar la recepción de datos de las empresas (Agencia Española de Protección de Datos) recibe más de 10.000 denuncias anuales  por posibles incumplimientos de la LOPD y el RGPD. Las multas pueden llegar  hasta los 20 millones de euros o hasta el 4% de volumen de negocio anual global dependiendo de la gravedad del caso.</p>
                                                    <h3><strong>Sanciones de hasta 10.000.000 o el 2% del volumen de negocio anual&nbsp;global del ejercicio financiero anterior por:</strong></h3>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">No aplicar medidas técnicas y organizativas por defecto.</li>
                                                        <li class="list-group-item">No realizar la correspondiente Evaluación de Impacto.</li>
                                                        <li class="list-group-item">No disponer del registro de actividades de tratamiento.</li>
                                                        <li class="list-group-item">No designar un DPO.</li>
                                                        <li class="list-group-item">No notificar las brechas de seguridad.</li>
                                                    </ul>
                                                    <h3><strong>Sanciones de hasta 20.000.000€ o el 4% del volumen de negocio anual global del ejercicio financiero anterior por:</strong></h3>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">No cumplir con los principios y derechos del RGPD.</li>
                                                        <li class="list-group-item">No legalizar las transferencias internacionales de datos.</li>
                                                        <li class="list-group-item">No atender las resoluciones de la Autoridades de Control.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="text-align: center;">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='cdlopd-outer-wrap' style="max-width: 100%!important; width: 100%!important;">
                            <div class='cdlopd-inner-wrap' style="max-width: 100%!important;">
                                <div class='cdlopd-inner-wrap-content'>
                                    <h2>Aquí podrá crear o actualizar las páginas de Política de Privacidad, Política de Cookies y Avisos Legales.</h2>
                                    <div class='cdlopd-inner-wrap-content container' style="width: 100% !important">
                                        <form method="post" class="table-responsive">   
                                            <div class="col-md-8 cdlopd-formularios">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td><label for="empresa">Nombre Comercial:</label></td>
                                                            <td colspan="3"><input name="empresa" style="width: 100%;" type="text" id="empresa" value="<?php echo $empresa; ?>" class="cdlopd-regular-text" placeholder="Nombre de la empresa"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="razon">Razón Social:</label></td>
                                                            <td colspan="3"><input name="razon" style="width: 100%;" type="text" id="razon" value="<?php echo $razon; ?>" class="cdlopd-regular-text" placeholder="Razón Social de la empresa"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="titular">Nombre del Titular:</label></td>
                                                            <td><input name="titular" type="text" id="titular" value="<?php echo $titular; ?>" class="cdlopd-regular-text"  style="width: 100%;" placeholder="Nombre del titular"></td>
                                                            <td><label for="domicilio">Domicilio Social:</label></td>
                                                            <td><input name="domicilio" type="text" id="domicilio" aria-describedby="tagline-description" value="<?php echo $domicilio; ?>"  style="width: 100%;" class="cdlopd-regular-text" placeholder="Domicilio social"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="poblacion">Población:</label></td>
                                                            <td><input name="poblacion" type="text" id="poblacion" value="<?php echo $poblacion; ?>" class="cdlopd-regular-text"  style="width: 100%;" placeholder="Población de la empresa"></td>
                                                            <td><label for="provincia">Provincia:</label></td>
                                                            <td><input name="provincia" type="text" id="provincia" aria-describedby="home-description" value="<?php echo $provincia; ?>"  style="width: 100%;" class="cdlopd-regular-text" placeholder="Provincia"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="cp">Código Postal:</label></td>
                                                            <td><input name="cp" type="text" id="cp" minlength="5" maxlength="5" aria-describedby="new-admin-email-description" value="<?php echo $cp; ?>"  style="width: 100%;" class="cdlopd-regular-text" placeholder="Codigo Postal"></td>
                                                            <td><label for="cif">C.I.F./D.N.I.:</label></td>
                                                            <td><input name="cif" type="text" id="cif" aria-describedby="new-admin-email-description" value="<?php echo $cif; ?>" style="width: 100%;" class="cdlopd-regular-text" placeholder="C.I.F."></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="telefono">Teléfono:</label></td>
                                                            <td><input name="telefono" type="tel" id="telefono" minlength="9" maxlength="9"  style="width: 100%;" value="<?php echo $telefono; ?>" class="input-medium bfh-phone"  aria-describedby="new-admin-telefono-description" placeholder="Número de teléfono" ></td>
                                                            <td><label for="email">Email:</label></td>
                                                            <td><input name="email" type="email" id="email" aria-describedby="new-admin-email-description" value="<?php echo $email; ?>" style="width: 100%;" class="cdlopd-regular-text" placeholder="E-mail"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="datos">Datos registrales:</label></td>
                                                            <td colspan="4"><textarea class="form-control" rows="5" name="datos" id="datos" style="width: 100%;" placeholder="Datos registrales"><?php echo $datos; ?></textarea></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-3">
                                                <div class=" postbox cdlopd-banner vc_single_image-wrapper vc_box_shadow_3d  vc_box_border_grey" style="margin: 0;">
                                                    <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img style="width: 100%; object-fit: contain" class="vc_single_image-img attachment-medium" src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/300x250.jpg'; ?>" alt="RGPD clikDatos" ></a>
                                                </div>
                                                <div class=" postbox cdlopd-banner vc_single_image-wrapper vc_box_shadow_3d  vc_box_border_grey" style="margin: 0;">
                                                    <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img style="width: 100%; object-fit: contain" class="vc_single_image-img attachment-medium" src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/clickdatos-lopd-web-cookie-RGPD.jpg'; ?>" alt="RGPD clikDatos" ></a>
                                                </div>
                                            </div>
                                            <!-- Listado de cookies de la web -->
                                            <div class="col-md-12" style="max-width: 97% !important;">
                                                <div id="listadodecookies">
                                                    <p>Esta tabla aparecerá en su página de Política de Cookies. Tendrá que indicar para que son utilizadas las cookies de su web y así tener informado al usuario. </p>
                                                    <?php
                                                    $xml = simplexml_load_file(plugin_dir_path(__FILE__) . "cookies.xml");
                                                    $arraycookies = array();
                                                    //Guardamos las cookies de la pagina encontradas en nuestro xml dentro de arraycookies.
                                                    foreach ($xml->cookie as $cookie) {
                                                        $i = 0;
                                                        foreach ($_COOKIE as $key => $value) {
                                                            $resultado = strpos((string) $key, (string) $cookie->nombre);
                                                            if ($resultado !== false && $i == 0) {
                                                                if (get_option("lopdwidget_cookie_" . $cookie->nombre, false)) {
                                                                    $cookiea = array(
                                                                        'nombre' => sanitize_text_field(get_option("lopdwidget_cookie_" . $cookie->nombre, false)),
                                                                        'categoria' => sanitize_text_field(get_option("lopdwidget_cookie_" . $cookie->nombre . "_categoria", false)),
                                                                        'descripcion' => sanitize_text_field(get_option("lopdwidget_cookie_" . $cookie->nombre . "_descripcion", false)),
                                                                        'propiedad' => sanitize_text_field(get_option("lopdwidget_cookie_" . $cookie->nombre . "_propiedad", false)),
                                                                        'duracion' => sanitize_text_field(get_option("lopdwidget_cookie_" . $cookie->nombre . "_duracion", false))
                                                                    );
                                                                } else {
                                                                    $cookiea = array(
                                                                        'nombre' => $cookie->nombre,
                                                                        'categoria' => $cookie->categoria,
                                                                        'descripcion' => $cookie->descripcion,
                                                                        'propiedad' => $cookie->propiedad,
                                                                        'duracion' => $cookie->duracion
                                                                    );
                                                                }
                                                                array_push($arraycookies, $cookiea);
                                                                $i++;
                                                            }
                                                        }
                                                    }
                                                    //Convertimos el array de cookies a string
                                                    $arraycookiesstring = implode(" ", array_map(function($a) {
                                                                return implode(" ", $a);
                                                            }, $arraycookies));
                                                    //Las cookies de la web que no hayan sido encontradas en el xml tambien deberan ser aÃƒÂ±adidas al array de cookies
                                                    foreach ($_COOKIE as $key => $value) {
                                                        $keys = substr($key, 0, 10);
                                                        $resultado = strpos((string) $arraycookiesstring, (string) $keys);
                                                        if ($resultado === false) {
                                                            if (get_option("lopdwidget_cookie_" . $key, false)) {
                                                                $cookiea = array(
                                                                    'nombre' => sanitize_text_field(get_option("lopdwidget_cookie_" . $key, false)),
                                                                    'categoria' => sanitize_text_field(get_option("lopdwidget_cookie_" . $key . "_categoria", false)),
                                                                    'descripcion' => sanitize_text_field(get_option("lopdwidget_cookie_" . $key . "_descripcion", false)),
                                                                    'propiedad' => sanitize_text_field(get_option("lopdwidget_cookie_" . $key . "_propiedad", false)),
                                                                    'duracion' => sanitize_text_field(get_option("lopdwidget_cookie_" . $key . "_duracion", false))
                                                                );
                                                            } else {
                                                                $cookiea = array(
                                                                    'nombre' => $key,
                                                                    'categoria' => 'Desconocemos el origen de la cookie',
                                                                    'descripcion' => 'Desconocemos el origen de la cookie',
                                                                    'propiedad' => 'Desconocemos el origen de la cookie',
                                                                    'duracion' => 'Desconocemos el origen de la cookie'
                                                                );
                                                            }
                                                            array_push($arraycookies, $cookiea);
                                                        }
                                                    }
                                                    //Convertimos el array de cookies a string para enviarlo al controlador
                                                    $arraycookiesstring = implode("||", array_map(function($a) {
                                                                return implode("*", $a);
                                                            }, $arraycookies));
                                                    //Recorremos el array multidimensional de cookies para mostrarlas
                                                    ?>
                                                    <input type="hidden" name="arraycookiesstring" id="arraycookiesstring" value="<?php echo $arraycookiesstring; ?>" >
                                                    <table id="datatable" class="table">
                                                        <tr align="center">
                                                            <td class="titulotablacookies" style="border-radius: 50px 0px 0px 0px;">Nombre</td>
                                                            <td class="titulotablacookies">Categoria</td>
                                                            <td class="titulotablacookies">Descripción</td>
                                                            <td class="titulotablacookies">Propiedad</td>
                                                            <td class="titulotablacookies" style="border-radius: 0px 50px 0px 0px;">Duración</td>
                                                        </tr>
                                                        <?php
                                                        $i = 0;
                                                        foreach ($arraycookies as $cookie) {
                                                            //$pos almacena la posición donde se encuentren las cookies que empiecen por wordpress_ 
                                                            //a continuación comprueba las que empiezan por wordpress_ para que no aparezcan en la tabla
                                                            $pos = strpos($cookie['nombre'], 'wordpress_');
                                                            
                                                            if($pos !== 0){?>
                                                                <tr align="center">
                                                                    <td style="border: 1px solid #ff6f00!important;">
                                                                        <label style="width: 100%!important;"><?php
                                                                            echo $cookie['nombre'] . "<br>";
                                                                            if ($cookie['descripcion'] === "Desconocemos el origen de la cookie") {
                                                                                ?>
                                                                                <input type="button" value="¿Saber más?" class="btn btn-warning" onclick="window.open('https://clickdatos.es/contacto-cookies-plugin/', '_blank')" />
                                                                                <?php
                                                                            }
                                                                            ?></label>
                                                                        <input id="nombrecookie<?php echo $i; ?>" name="nombrecookie<?php echo $i; ?>" type="hidden" value="<?php echo $cookie['nombre']; ?>">
                                                                    </td>
                                                                    <td style="border: 1px solid #ff6f00!important;">
                                                                        <select style="width: 100%!important;" id="categoria<?php echo $i; ?>" name="categoria<?php echo $i; ?>">
                                                                            <option value="Funcionalidad" <?php
                                                                            if ($cookie['categoria'] == 'Funcionalidad') {
                                                                                echo 'selected="selected"';
                                                                            }
                                                                            ?>>Funcionalidad</option>
                                                                            <option value="Análisis" <?php
                                                                            if ($cookie['categoria'] == 'Análisis') {
                                                                                echo 'selected="selected"';
                                                                            }
                                                                            ?>>Análisis</option>
                                                                            <option value="Publicidad"  <?php
                                                                            if ($cookie['categoria'] == 'Publicidad') {
                                                                                echo 'selected="selected"';
                                                                            }
                                                                            ?>>Publicidad</option>
                                                                        </select>
                                                                    </td>
                                                                    <td style="border: 1px solid #ff6f00!important; ">
                                                                        <textarea style="width: 100%!important;" rows="3" cols="40" id="descripcion<?php echo $i; ?>" name="descripcion<?php echo $i; ?>" placeholder="Descripción"><?php echo $cookie['descripcion']; ?></textarea>
                                                                    </td>
                                                                    <td style="border: 1px solid #ff6f00!important;">
                                                                        <select style="width: 100%!important;" id="propiedad<?php echo $i; ?>" name="propiedad<?php echo $i; ?>">
                                                                            <option value="Terceros" <?php
                                                                            if ($cookie['propiedad'] == 'Terceros') {
                                                                                echo 'selected="selected"';
                                                                            }
                                                                            ?>>Terceros</option>
                                                                            <option value="Propia" <?php
                                                                            if ($cookie['propiedad'] == 'Propia') {
                                                                                echo 'selected="selected"';
                                                                            }
                                                                            ?>>Propia</option>
                                                                        </select>
                                                                    </td>
                                                                    <td style="border: 1px solid #ff6f00!important;">
                                                                        <input style="width: 100%!important;" type="text" id="duracion<?php echo $i; ?>" name="duracion<?php echo $i; ?>" placeholder="Duración" value="<?php echo $cookie['duracion']; ?>">
                                                                    </td>
                                                                </tr>
                                                            <?php }
                                                            ?>
                                                            
                                                            <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="max-width: 97% !important; text-align: center;">
                                                <!-- Trigger the modal with a button -->
                                                <button type="button" style="margin-bottom: 2%; width: 80%!important" class="btn btn-primary" data-toggle="modal" data-target="#seguro">Crear o Actualizar Páginas</button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="seguro" role="dialog" style="margin-top: 10%;">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-body" style="height: 100px;">
                                                                <p>Si sigue adelante sobreescribirá las páginas de Política de Privacidad, Avisos Legales y Política de Cookies, creadas con nuestro plugin.¿Desea continuar?</p>
                                                                <input style="float: left; margin-left: 10%; width: 30%;" type='submit' class="btn btn-primary" name="crearpagina" value='Si'>
                                                                <button style="float: right; margin-right: 10%; width: 30%;" type="button" class="btn btn-warning" data-dismiss="modal">No</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>      
                                    <!--FINALIZA PARTE DE CONSTRUCCION DE PAGINAS RGPD -->             
                                </div>                 
                            </div>
                        </div>
                    </div>
                    <?php
                }

            }

            public function sanitize_content($input) {
                $output = array();
                foreach ($input as $key => $value) {
                    if (isset($input[$key])) {
                        if ($key == 'notification_text') {
                            $output[$key] = esc_attr($input[$key]);
                        } else if ($key == 'more_info_url') {
                            $output[$key] = esc_url($input[$key]);
                        } else {
                            $output[$key] = sanitize_text_field($input[$key]);
                        }
                    }
                }
                return $output;
            }

            public function register_content_init() {

                register_setting('cdlopd_content', 'cdlopd_content_settings', array($this, 'sanitize_content'));

                add_settings_section(
                        'cdlopd_content_section', __('Parámetro de contenidos de tus Cookies', 'click-datos-lopd'), array($this, 'content_settings_section_callback'), 'cdlopd_content'
                );

                add_settings_field(
                        'position', __('Posición', 'click-datos-lopd'), array($this, 'position_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                add_settings_field(
                        'text_color', __('Color del texto', 'click-datos-lopd'), array($this, 'text_color_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'bg_color', __('Color de fondo', 'click-datos-lopd'), array($this, 'bg_color_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                add_settings_field(
                        'link_color', __('Color del enlace a más información ', 'click-datos-lopd'), array($this, 'link_color_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'grosor_border', __('Grosor del borde', 'click-datos-lopd'), array($this, 'grosor_border_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'color_border', __('Color del borde', 'click-datos-lopd'), array($this, 'color_border_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                //Color de texto y fondo del boton Aceptar
                add_settings_field(
                        'button_color_aceptar', __('Color texto del botón Aceptar', 'click-datos-lopd'), array($this, 'button_color_render_aceptar'), 'cdlopd_content', 'cdlopd_content_section'
                );
                add_settings_field(
                        'button_bg_color_aceptar', __('Color fondo del botón Aceptar', 'click-datos-lopd'), array($this, 'button_bg_color_render_aceptar'), 'cdlopd_content', 'cdlopd_content_section'
                );
                //Color de texto y fondo del boton Rechazar
                add_settings_field(
                        'button_color_rechazar', __('Color texto del botón Rechazar', 'click-datos-lopd'), array($this, 'button_color_render_rechazar'), 'cdlopd_content', 'cdlopd_content_section'
                );
                add_settings_field(
                        'button_bg_color_rechazar', __('Color fondo del botón Rechazar', 'click-datos-lopd'), array($this, 'button_bg_color_render_rechazar'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'bg_color', __('Color de fondo', 'click-datos-lopd'), array($this, 'bg_color_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'heading_text', __('Texto de cabecera', 'click-datos-lopd'), array($this, 'heading_text_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'notification_text', __('Texto principal', 'click-datos-lopd'), array($this, 'notification_text_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'more_info_text', __('Texto de enlace a más información"', 'click-datos-lopd'), array($this, 'more_info_text_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'more_info_page', __('Página de más información', 'click-datos-lopd'), array($this, 'more_info_page_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'more_info_url', __('URL externa para más información', 'click-datos-lopd'), array($this, 'more_info_url_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'more_info_target', __('Abrir url externa en:', 'click-datos-lopd'), array($this, 'more_info_target_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'accept_text', __('Texto botón aceptación', 'click-datos-lopd'), array($this, 'accept_text_render'), 'cdlopd_content', 'cdlopd_content_section'
                );

                add_settings_field(
                        'deny_text', __('Texto botón rechazo', 'click-datos-lopd'), array($this, 'deny_text_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'opcion_cookie', __('Opción para cargar las cookies', 'click-datos-lopd'), array($this, 'opcion_cookie_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'scroll_text', __('Texto cuadro de Cookies al seleccionar Scroll', 'click-datos-lopd'), array($this, 'scroll_text_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'duracion_cookie_aceptar', __('Tiempo duración cookie Aceptar (en días)', 'click-datos-lopd'), array($this, 'duracion_cookie_aceptar_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'duracion_cookie_rechazar', __('Tiempo duración cookie Rechazar (en días)', 'click-datos-lopd'), array($this, 'duracion_cookie_rechazar_render'), 'cdlopd_content', 'cdlopd_content_section'
                );
                
                add_settings_field(
                        'porcentaje_scroll', __('Porcentaje de Scroll para aceptar cookies', 'click-datos-lopd'), array($this, 'porcentaje_scroll_render'), 'cdlopd_content', 'cdlopd_content_section'
                );


                // Set default options
                $options = get_option('cdlopd_content_settings');
                if (false === $options) {
                    // Get defaults
                    $defaults = $this->get_default_content_settings();
                    update_option('cdlopd_content_settings', $defaults);
                }
            }

            public function get_default_content_settings() {

                $previous_settings = get_option('catapult_cookie_options');
                // Check for settings from previous version
                if (!empty($previous_settings)) {
                    $defaults = array(
                        'position' => 'bottom-bar',
                        'text_color' => '#ddd',
                        'bg_color' => '#fff',
                        'link_color' => '#ccc',
                        'button_color_aceptar' => '#000',
                        'button_bg_color_aceptar' => '#fff',
                        'button_color_rechazar' => '#000',
                        'button_bg_color_rechazar' => '#fff',
                        'heading_text' => __('Cookies', 'click-datos-lopd'),
                        'notification_text' => __('Utilizamos cookies propias y de terceros para analizar nuestros servicios y mostrarte publicidad relacionada con tus preferencias en base a un perfil elaborado a partir de tus hábitos de navegación (por ejemplo páginas visitadas). Puedes obtener más información ', 'click-datos-lopd'),
                        'accept_text' => $previous_settings['catapult_cookie_accept_settings'],
                        'deny_text' => 'Rechazo',
                        'more_info_text' => __('AQUÍ', 'click-datos-lopd'),
                        'more_info_page' => '',
                        'more_info_url' => site_url($previous_settings['catapult_cookie_link_settings']),
                        'more_info_target' => '_blank',
                        'opcion_cookie' => 'botones',
                        'scroll_text' => __('Al hacer scroll en esta página, usted acepta nuestra política de cookies', 'click-datos-lopd'),
                    );
                } else {
                    $defaults = array(
                        'heading_text' => __('Política de Cookies', 'click-datos-lopd'),
                        'text_color' => '#ddd',
                        'bg_color' => '#fff',
                        'link_color' => '#ccc',
                        'button_color_aceptar' => '#000',
                        'button_bg_color_aceptar' => '#fff',
                        'button_color_rechazar' => '#000',
                        'button_bg_color_rechazar' => '#fff',
                        'notification_text' => __('Utilizamos cookies propias y de terceros para analizar nuestros servicios y mostrarte publicidad relacionada con tus preferencias en base a un perfil elaborado a partir de tus hábitos de navegación (por ejemplo páginas visitadas). Puedes obtener más información ', 'click-datos-lopd'),
                        'accept_text' => __('Acepto', 'click-datos-lopd'),
                        'deny_text' => __('Rechazar', 'click-datos-lopd'),
                        'more_info_text' => __('AQUÍ', 'click-datos-lopd'),
                        'more_info_page' => get_option('cdlopd_more_info_page', ''),
                        'more_info_url' => '',
                        'more_info_target' => '_blank',
                        'scroll_text' => __('Al hacer scroll en esta página, usted acepta nuestra política de cookies', 'click-datos-lopd'),
                        'opcion_cookie' => get_option('botones'),
                        'duracion_cookie_aceptar' => __(1, 'click-datos-lopd'),
                        'duracion_cookie_rechazar' => __(1, 'click-datos-lopd'),
                    );
                }if (!empty($previous_settings['catapult_cookie_bar_position_settings'])) {
                    $defaults['position'] = 'bottom-bar';
                }

                if (!empty($previous_settings['catapult_cookie_text_colour_settings'])) {
                    $defaults['text_color'] = $previous_settings['catapult_cookie_text_colour_settings'];
                }

                if (!empty($previous_settings['catapult_cookie_bg_colour_settings'])) {
                    $defaults['bg_color'] = $previous_settings['catapult_cookie_bg_colour_settings'];
                }

                if (!empty($previous_settings['catapult_cookie_link_colour_settings'])) {
                    $defaults['link_color'] = $previous_settings['catapult_cookie_link_colour_settings'];
                }

                if (!empty($previous_settings['catapult_cookie_button_colour_settings'])) {
                    $defaults['button_bg_color'] = $previous_settings['catapult_cookie_button_colour_settings'];
                }

                if (!empty($previous_settings['catapult_cookie_accept_text_settings'])) {
                    $defaults['accept_text'] = $previous_settings['catapult_cookie_accept_text_settings'];
                }

                if (!empty($previous_settings['catapult_cookie_deny_text_settings'])) {
                    $defaults['deny_text'] = $previous_settings['catapult_cookie_deny_text_settings'];
                }

                return $defaults;
            }

            /*
             * Content renders
             */

            public function heading_text_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['heading_text'])) {
                    $cdlopd_content_settings['heading_text'] = "";
                }
                ?>
                <div class='cdlopd-formularios'>
                    <input type="text" name="cdlopd_content_settings[heading_text]" value="<?php echo esc_attr($cdlopd_content_settings['heading_text']); ?>">
                    <p class="description"><?php _e('Texto de cabecera del bannner(no se mostrara si se posiciona en la parte superior o inferior)', 'click-datos-lopd'); ?></p>
                </div>
                <?php
            }

            public function notification_text_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['notification_text'])) {
                    $cdlopd_content_settings['notification_text'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input class="widefat" type="text" name="cdlopd_content_settings[notification_text]" value="<?php echo esc_attr($cdlopd_content_settings['notification_text']); ?>">
                    <p class="description"><?php _e('Texto principal del banner de política de cookies', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function accept_text_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['accept_text'])) {
                    $cdlopd_content_settings['accept_text'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" name="cdlopd_content_settings[accept_text]" value="<?php echo esc_attr($cdlopd_content_settings['accept_text']); ?>">
                    <p class="description"><?php _e('Texto de botón para aceptar política de cookies y cerrar banner', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function more_info_text_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['more_info_text'])) {
                    $cdlopd_content_settings['more_info_text'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" name="cdlopd_content_settings[more_info_text]" value="<?php echo esc_attr($cdlopd_content_settings['more_info_text']); ?>">
                    <p class="description"><?php _e('Texto para enlace a página de más información', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function more_info_page_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                // Get all pages
                $pages = get_pages();
                if (!isset($cdlopd_content_settings['more_info_page'])) {
                    $cdlopd_content_settings['more_info_page'] = "";
                }
                ?>
                    <?php if ($pages) { ?><div class='cdlopd-formularios'>
                        <select name='cdlopd_content_settings[more_info_page]'>
                            <option></option>
                            <?php foreach ($pages as $page) { ?>
                                <option value='<?php echo $page->ID; ?>' <?php selected($cdlopd_content_settings['more_info_page'], $page->ID); ?>><?php echo $page->post_title; ?></option>
                            <?php } ?>
                        </select>
                        <p class="description"><?php _e('Seleccionar la página que muestre la información', 'click-datos-lopd'); ?></p></div>
                    <?php
                }
            }

            public function more_info_url_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['more_info_url'])) {
                    $cdlopd_content_settings['more_info_url'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="url" name="cdlopd_content_settings[more_info_url]" value="<?php echo esc_url($cdlopd_content_settings['more_info_url']); ?>">
                    <p class="description"><?php _e('Si no dispone de página de política de cookies puede escribir una url externa.', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function more_info_target_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['more_info_target'])) {
                    $cdlopd_content_settings['more_info_target'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <select name='cdlopd_content_settings[more_info_target]'>
                        <option value='_blank' <?php selected($cdlopd_content_settings['more_info_target'], '_blank'); ?>><?php _e('Nueva Página', 'click-datos-lopd'); ?></option>
                        <option value='_self' <?php selected($cdlopd_content_settings['more_info_target'], '_self'); ?>><?php _e('Misma Página', 'click-datos-lopd'); ?></option>
                    </select>
                    <p class="description"><?php _e('Abrir url externa en nueva página o en la misma página', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            /*
             * Styles functions
             */

            public function position_render() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['position']) || empty($options['position'])) {
                    $options['position'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <select name='cdlopd_content_settings[position]'>
                        <option value='top-bar' <?php selected($options['position'], 'top-bar'); ?>><?php _e('Superior', 'click-datos-lopd'); ?></option>
                        <option value='bottom-bar' <?php selected($options['position'], 'bottom-bar'); ?>><?php _e('Inferior', 'click-datos-lopd'); ?></option>
                        <option value='top-left-block' <?php selected($options['position'], 'top-left-block'); ?>><?php _e('Superior Izquierda', 'click-datos-lopd'); ?></option>
                        <option value='top-right-block' <?php selected($options['position'], 'top-right-block'); ?>><?php _e('Superior Derecha', 'click-datos-lopd'); ?></option>
                        <option value='bottom-left-block' <?php selected($options['position'], 'bottom-left-block'); ?>><?php _e('Inferior Izquierda', 'click-datos-lopd'); ?></option>
                        <option value='bottom-right-block' <?php selected($options['position'], 'bottom-right-block'); ?>><?php _e('Inferior Derecha', 'click-datos-lopd'); ?></option>
                        <option value='bottom-center-block' <?php selected($options['position'], 'bottom-center-block'); ?>><?php _e('Intrusivo', 'click-datos-lopd'); ?></option>
                    
                    </select>
                    <p class="description"><?php _e('Posición en la que aparece el banner de Política de cookies', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function text_color_render() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['text_color'])) {
                    $options['text_color'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[text_color]" value="<?php echo esc_attr($options['text_color']); ?>">
                    <p class="description"><?php _e('Color del texto principal de banner de política de cookies', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function bg_color_render() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['bg_color'])) {
                    $options['bg_color'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[bg_color]" value="<?php echo esc_attr($options['bg_color']); ?>">
                    <p class="description"><?php _e('Color de fondo del banner', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function link_color_render() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['link_color'])) {
                    $options['link_color'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[link_color]" value="<?php echo esc_attr($options['link_color']); ?>">
                    <p class="description"><?php _e('Color de texto que enlaza a más información', 'click-datos-lopd'); ?></p></div>
                <?php
            }
            
            public function grosor_border_render() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['grosor_border'])) {
                    $options['grosor_border'] = "";
                }
                ?>
                    <div class="cdlopd-formularios">
                        <input type="text" name="cdlopd_content_settings[grosor_border]" value="<?php echo esc_attr($options['grosor_border']); ?>">
                        <p class="description"><?php _e('Establece el grosor en píxeles que tendrá el banner de política de cookies', 'click-datos-lopd'); ?></p>
                    </div>
                <?php
            }
            
            public function color_border_render() {
                $options = get_option('cdlopd_content_settings');
                if(!isset($options['color_border'])){
                    $options['color_border'] = "";
                }
                ?>
                    <div class="cdlopd-formularios">
                        <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[color_border]" value="<?php echo esc_attr($options['color_border']); ?>">
                        <p class="description"><?php _e('Color del borde del banner de política de cookies', 'click-datos-lopd'); ?></p>
                    </div>
                <?php
            }
            //Funciones para cargar la eleccion de color del boton Aceptar
            public function button_color_render_aceptar() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['button_color_aceptar'])) {
                    $options['button_color_aceptar'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[button_color_aceptar]" value="<?php echo esc_attr($options['button_color_aceptar']); ?>">
                    <p class="description"><?php _e('Color de texto del botón de aceptar política', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function button_bg_color_render_aceptar() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['button_bg_color_aceptar'])) {
                    $options['button_bg_color_aceptar'] = "Acepto";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[button_bg_color_aceptar]" value="<?php echo esc_attr($options['button_bg_color_aceptar']); ?>">
                    <p class="description"><?php _e('Color de fondo del boton de aceptar política', 'click-datos-lopd'); ?></p></div>
                <?php
            }
            //Funciones para cargar la eleccion de color del boton Aceptar
            public function button_color_render_rechazar() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['button_color_rechazar'])) {
                    $options['button_color_rechazar'] = "";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[button_color_rechazar]" value="<?php echo esc_attr($options['button_color_rechazar']); ?>">
                    <p class="description"><?php _e('Color de texto del botón de rechazar política', 'click-datos-lopd'); ?></p></div>
                <?php
            }
            
            public function button_bg_color_render_rechazar() {
                $options = get_option('cdlopd_content_settings');
                if (!isset($options['button_bg_color_rechazar'])) {
                    $options['button_bg_color_rechazar'] = "Rechazo";
                }
                ?><div class='cdlopd-formularios'>
                    <input type="text" class="cdlopd-color-field" name="cdlopd_content_settings[button_bg_color_rechazar]" value="<?php echo esc_attr($options['button_bg_color_rechazar']); ?>">
                    <p class="description"><?php _e('Color de fondo del boton de rechazar política', 'click-datos-lopd'); ?></p></div>
                <?php
            }

            public function deny_text_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['deny_text'])) {
                    $cdlopd_content_settings['deny_text'] = "Rechazo";
                }
                ?>
                <div class='cdlopd-formularios'>
                    <input type="text" name="cdlopd_content_settings[deny_text]" value="<?php echo esc_attr($cdlopd_content_settings['deny_text']); ?>">
                    <p class="description"><?php _e('Texto de botón para rechazar la política de cookies y cerrar banner', 'click-datos-lopd'); ?></p>
                </div>
                <?php
            }
            
            public function opcion_cookie_render(){
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if(!isset($cdlopd_content_settings['opcion_cookie'])){
                    $cdlopd_content_settings['opcion_cookie'] = "";
                }
                ?>
                    <div class="cdlopd-formularios">
                        <input type='radio' checked="checked" name='cdlopd_content_settings[opcion_cookie]' value='botones' <?php if (esc_attr($cdlopd_content_settings['opcion_cookie']) === 'botones'){ ?> checked="checked" <?php } ?>><?php _e('Botones', 'click-datos-lopd'); ?>
                        &nbsp;
                        <input type='radio' name='cdlopd_content_settings[opcion_cookie]' value='scroll' <?php if (esc_attr($cdlopd_content_settings['opcion_cookie']) === 'scroll'){ ?> checked="checked" <?php } ?>><?php _e('Scroll Página', 'click-datos-lopd'); ?>
                        <p class="description"><?php _e('Elige el método por el que se cargarán las cookies<br>-Botones: El usuario aceptará o rechazará las cookies por medio de botones<br>-Scroll Página: El usuario aceptará las cookies al hacer scroll en la página', 'click-datos-lopd'); ?></p>
                    </div>
                <?php
            }
            
            public function scroll_text_render(){
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if(!isset($cdlopd_content_settings['scroll_text'])){
                    $cdlopd_content_settings['scroll_text'] = "";
                }
                ?>
                    <div class="cdlopd-formularios">
                        <input class="widefat" type="text" name='cdlopd_content_settings[scroll_text]' value="<?php echo esc_attr($cdlopd_content_settings['scroll_text']); ?>">
                        <p class="description"><?php _e('Texto para el scroll del banner de política de cookies', 'click-datos-lopd'); ?></p>
                    </div>
                <?php
            }
            
            public function duracion_cookie_aceptar_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['duracion_cookie_aceptar'])) {
                    $cdlopd_content_settings['duracion_cookie_aceptar'] = "";
                }
                ?>
                <div class='cdlopd-formularios'>
                    <input type="text" name="cdlopd_content_settings[duracion_cookie_aceptar]" value="<?php echo esc_attr($cdlopd_content_settings['duracion_cookie_aceptar']); ?>">
                    <p class="description"><?php _e('Establece el número de días que estará activa la cookie aceptar', 'click-datos-lopd'); ?></p>
                </div>
                <?php
            }
            
            public function duracion_cookie_rechazar_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['duracion_cookie_rechazar'])) {
                    $cdlopd_content_settings['duracion_cookie_rechazar'] = "";
                }
                ?>
                <div class='cdlopd-formularios'>
                    <input type="text" name="cdlopd_content_settings[duracion_cookie_rechazar]" value="<?php echo esc_attr($cdlopd_content_settings['duracion_cookie_rechazar']); ?>">
                    <p class="description"><?php _e('Establece el número de días que estará activa la cookie rechazar', 'click-datos-lopd'); ?></p>
                </div>
                <?php
            }
            
            public function porcentaje_scroll_render() {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if(!isset($cdlopd_content_settings['porcentaje_scroll'])){
                    $cdlopd_content_settings['porcentaje_scroll'] = "";
                }
                ?>
                    <div class="cdlopd-formularios">
                        <input type="text" id="porcent_scroll" name="cdlopd_content_settings[porcentaje_scroll]" value="25" readonly="readonly">
                        <p class="description"><?php _e('Establece el porcentaje por el cuál la página cargará la cookie de aceptar la política de cookies', 'click-datos-lopd'); ?></p>
                    </div>
                <?php
            }

            public function content_settings_section_callback() {
                //posible eliminacion	
            }

            public function pages_settings_section_callback() {

                //posible eliminacion
            }

            public function options_page() {
                $reset = isset($_GET['reset']) ? $_GET['reset'] : '';
                if (isset($_POST['reset'])) {
                    $defaults = $this->get_default_content_settings();
                    update_option('cdlopd_content_settings', $defaults);
                }
                $current = isset($_GET['tab']) ? $_GET['tab'] : 'content';
                $title = __('RGPD ClickDatos - Cookies', 'click-datos-lopd');
                ?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery("#boton").click(function (event) {
                            document.getElementById('resultados').removeAttribute('style');
                            var nombreu = document.getElementById("nombreu").value;
                            var nombrec = document.getElementById("nombrec").value;
                            var email = document.getElementById("email").value;
                            var url = document.getElementById("url").value;
                            var mensaje = document.getElementById("mensaje").value;
                            if (nombreu === "" || email === "" || url === "") {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Rellene todos los datos con * del formulario. <br>Por favor.");
                            } else if (!jQuery('#checkprivacidad').is(':checked')) {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Debes aceptar la Política de Privacidad para poder enviar el formulario.");
                            } else {
                                jQuery.post(url + '/wp-content/plugins/click-datos-lopd/admin/mail-contacto.php', {nombreu: nombreu, nombrec: nombrec, email: email, url: url, mensaje: mensaje}, function (respuesta) {
                                    if (respuesta === 'Tu correo ha sido enviado correctamente.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: green;');
                                    } else if (respuesta === 'Hubo un problema al enviar el email, intentalo de nuevo.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: red;');
                                    }
                                    jQuery('#resultados').html(respuesta);
                                });
                            }
                        });
                    });
                </script>
                <div class="wrap">
                    <h1><?php
                        echo $title;
                        $cu = wp_get_current_user();
                        ?></h1>
                    <!-- Button trigger modal -->
                    <button type="button" style="margin: auto;" class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#myModal">¿Necesitas ayuda?</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="width: 72% !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Servicio de Ayuda</h4>
                                </div>
                                <div class="modal-body">
                                    <div role="tabpanel">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#ObtenSoporte" aria-controls="ObtenSoporte" role="tab" data-toggle="tab">Consulta tu web</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#queeslargpd" aria-controls="queeslargpd" role="tab" data-toggle="tab">¿Qué es la RGPD?</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#guiasydirectrices" aria-controls="guiasydirectrices" role="tab" data-toggle="tab">Guías y Directrices</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#principiosdelrgpd" aria-controls="principiosdelrgpd" role="tab" data-toggle="tab">Principios</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#nuevasobligaciones" aria-controls="nuevasobligaciones" role="tab" data-toggle="tab">Nuevas Obligaciones y Derechos</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#porquedebe" aria-controls="porquedebe" role="tab" data-toggle="tab">¿Por qué debe adaptarse?</a>
                                            </li>
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content"  style="text-align: center;">
                                            <div role="tabpanel" class="tab-pane active" id="ObtenSoporte">
                                                <div class="form-horizontal">
                                                    <h1>ClickDatos le hará una valoración a su web</h1><h2>Comprobará si cumple con la RGPD y le responderemos cuanto antes, de forma gratuíta.</h2>
                                                    <p>Si tiene alguna duda sobre la RGPD, puede consultar las demás pestañas donde hablamos de los requisitos para cumplir con esta Ley.</p>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Nombre:*</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="nombreu" id="nombreu" value="<?php echo $cu->user_login; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Nombre Completo:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="nombrec" id="nombrec" value="<?php echo $cu->user_firstname . ' ' . $cu->user_lastname; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Email:*</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="email" id="email" value="<?php echo $cu->user_email; ?>">
                                                            <input type="hidden" id="url" value="<?php echo get_site_url(); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2">Mensaje:</label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Introduce un mensaje..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-sm-3"></div>
                                                        <div class="col-sm-5">
                                                            <input type="checkbox" name="privacidad" id="checkprivacidad"> He leído y acepto <a target="_blank" rel="nofollow noopener noreferrer" href="https://clickdatos.es/politica-privacidad-plugin-rgpd-clickdatos/">Política de Privacidad</a> de ClickDatos.<br>
                                                            <button id="boton" class="btn btn-primary btn-lg" style="margin-top: 4%; margin-bottom: 4%;">Enviar consulta</button><br>
                                                            <label id="resultados"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="queeslargpd">
                                                <h2 style="text-align: center;">¿Qué es el Reglamento General de Protección de Datos?</h2>
                                                <p>El pasado 25 de mayo de 2016 entró en vigor el nuevo Reglamento General de Protección de Datos (RGPD) que deroga la Directiva 95/46/CE y viene a sustituir la normativa que actualmente estaba vigente en los países de la UE . Tiene lugar con motivo de la&nbsp;<a title="El enlace se abrirá en una nueva ventana" href="http://www.europarl.europa.eu/news/es/news-room/20160407IPR21776/reforma-de-la-protecci%C3%B3n-de-datos-%E2%80%93-nuevas-reglas-adaptadas-a-la-era-digital" target="_blank" rel="nofollow noopener noreferrer">reforma normativa realizada por la Unión Europea</a>&nbsp; cuyo objetivo es adaptar la legislación vigente a las exigencias en los niveles de seguridad que demanda la realidad digital actual.</p>
                                                <p>Será de aplicación a partir del <strong>25 de mayo de 2018</strong> para todos los paises de la UE. Durante ese plazo de dos años hasta su implantación total, las empresas, los autónomos, asociaciones, comunidades de vecinos y las administraciones tienen la obligación de adaptarse a las nuevas directrices.</p>
                                                <p><a title="El enlace se abrirá en una nueva ventana" href="https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:32016R0679" target="_blank" rel="nofollow noopener noreferrer">Reglamento (UE) 2016/679</a> del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales y a la libre circulación de estos datos y por el que se deroga la Directiva 95/46/CE por el Reglamento General de Protección de Datos.</p>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="guiasydirectrices">
                                                <h2>Guías y directrices sobre el reglamento y su aplicación</h2>
                                                <ul>
                                                    <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/guia-para-el-cumplimiento-del-deber-de-informar/" target="_blank" rel="nofollow noopener noreferrer">Guía para el cumplimiento del deber de informar</a></li>
                                                    <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/directrices-para-la-elaboracion-de-contratos-entre-responsables-y-encargados-del-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Directrices para la elaboración de contratos entre responsables y encargados del tratamiento</a></li>
                                                    <li class="list-group-item"><a href="https://clickdatos.es/guia-rgpd-para-responsables-de-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Guía del Reglamento General de Protección de Datos para responsables de tratamiento</a></li>
                                                    <li class="list-group-item"><a href="https://clickdatos.es/orientaciones-y-garantias-en-los-procedimientos-de-anonimizacion-de-datos-personales/" target="_blank" rel="nofollow noopener noreferrer">Orientaciones y garantías en los procedimientos de anonimización de datos personales</a></li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="principiosdelrgpd">
                                                <h2>Principios del RGPD</h2>
                                                <ul class="list-group">
                                                    <li class="list-group-item">El tratamiento de datos sólo será leal y lícito si la información es accesible y comprensible.</li>
                                                    <li class="list-group-item">Los datos han de ser recogidos para un fin determinado y sólo utilizados para dicho fin, considerando la existencia de excepciones para ciertos supuestos concretos.</li>
                                                    <li class="list-group-item">Los datos personales han de ser los adecuados, concretos y siempre limitados a la necesidad concreta para ser recabados</li>
                                                    <li class="list-group-item">Los datos han de ser precisos y deben ser actualizados en todo momento.</li>
                                                    <li class="list-group-item">Los datos han de ser mantenidos tan sólo durante el tiempo necesario para cumplir la finalidad de los mismos y han de ser cancelados, estableciendo el responsable de estos el plazo para la supresión o revisión de los mismos.</li>
                                                    <li class="list-group-item">Ha de velarse por la seguridad de los datos utilizando todos los medios técnicos y organizativos adecuados.</li>
                                                    <li class="list-group-item">Se establece que el responsable del tratamiento será además el responsable del cumplimiento de todos los principios aquí citados</li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="nuevasobligaciones">
                                                <h2>Nuevos derechos para los dueños de los datos</h2>
                                                <p>Además de los derechos <strong>ARCO</strong> vigentes actualmente (acceso, rectificación, cancelación y oposición) se añaden otros 3:</p>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Derecho de Supresión:</strong>&nbsp;Las personas podrán solicitar la eliminación total de sus datos personales en determinados casos.</li>
                                                    <li class="list-group-item"><strong>Derecho de Limitación:</strong>&nbsp;Permite la suspensión del tratamiento de los datos del interesado en algunos casos como impugnaciones o la conservación de los datos aún en casos en los que no sea necesaria por alguna razón administrativa o legal.</li>
                                                    <li class="list-group-item"><strong>Derecho de Portabilidad:</strong> El dueño de los datos personales tendrá la posibilidad de solicitar una copia al responsable de su tratamiento y obtenerlos en un formato estandarizado y mecanizado.</li>
                                                </ul>
                                                <h2>Nuevas obligaciones para empresas, administraciones y otras entidades</h2>
                                                <ul class="list-group">
                                                    <li class="list-group-item">En ciertos supuestos se establece la obligatoriedad de la designación de un <strong>Delegado de Protección de Datos (DPO).</strong></li>
                                                    <li class="list-group-item">En determinados casos se realizarán <strong> Evaluaciones de Impacto</strong> sobre la Privacidad para averiguar los riesgos que pudieran existir con el tratamiento de ciertos datos personales y traten de establecer medidas y directrices para minimizarlos o eliminarlos. Las empresas de ámbito multinacional tendrán como interlocutor a un sólo <strong>organismo de control nacional </strong>(ventanilla única).</li>
                                                    <li class="list-group-item">Se establece la obligatoriedad de&nbsp;<strong>informar a las autoridades&nbsp;</strong>de control competentes y a los&nbsp;<strong>afectados&nbsp;</strong>en casos graves de las brechas de seguridad que pudieran encontrase, estableciendo para ello un plazo máximo de&nbsp;<strong>72 horas&nbsp;</strong>desde su detección.</li>
                                                    <li class="list-group-item">Se amplía el listado de los&nbsp;<strong>datos especialmente protegidos&nbsp;</strong>(datos sensibles) con los datos genéticos, biométricos, infracciones y condenas penales.</li>
                                                    <li class="list-group-item">El responsable encargado del tratamiento de los datos ha de&nbsp;<strong>garantizar el cumplimiento&nbsp;</strong>de la norma, dato que influirá en la selección del mismo.</li>
                                                    <li class="list-group-item">Se establece un marco de garantías y mecanismos de seguimiento más estrictos para el caso de las&nbsp;<strong>transferencias internacionales fuera de la UE.</strong></li>
                                                    <li class="list-group-item">Se prevee la creación de&nbsp;<strong>sellos y certificaciones&nbsp;</strong>que acrediten la Responsabilidad Proactiva de las organizaciones.</li>
                                                    <li class="list-group-item">La obligación de inscribir los ficheros desaparece y será sustituída por un&nbsp;<strong>control interno&nbsp;</strong>y , en ocasiones, por un&nbsp;<strong>inventario&nbsp;</strong>de las operaciones de tratamiento realizadas.</li>
                                                    <li class="list-group-item">Las&nbsp;<strong>sanciones&nbsp;</strong>por incumplimiento&nbsp;<strong>aumentan su cuantía&nbsp;</strong>pudiendo alcanzar los&nbsp;<strong>20 millones de euros&nbsp;</strong>o el&nbsp;<strong>4% de la facturación&nbsp;</strong>global de la empresa.</li>
                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="porquedebe">
                                                <h2>¿Por qué debería contratar la auditoría y adaptarme al RGPD?</h2>
                                                <p>El organismo estatal encargado de supervisar la recepción de datos de las empresas (Agencia Española de Protección de Datos) recibe más de 10.000 denuncias anuales  por posibles incumplimientos de la LOPD y el RGPD. Las multas pueden llegar  hasta los 20 millones de euros o hasta el 4% de volumen de negocio anual global dependiendo de la gravedad del caso.</p>
                                                <h3><strong>Sanciones de hasta 10.000.000 o el 2% del volumen de negocio anual&nbsp;global del ejercicio financiero anterior por:</strong></h3>
                                                <ul class="list-group">
                                                    <li class="list-group-item">No aplicar medidas técnicas y organizativas por defecto.</li>
                                                    <li class="list-group-item">No realizar la correspondiente Evaluación de Impacto.</li>
                                                    <li class="list-group-item">No disponer del registro de actividades de tratamiento.</li>
                                                    <li class="list-group-item">No designar un DPO.</li>
                                                    <li class="list-group-item">No notificar las brechas de seguridad.</li>
                                                </ul>
                                                <h3><strong>Sanciones de hasta 20.000.000€ o el 4% del volumen de negocio anual global del ejercicio financiero anterior por:</strong></h3>
                                                <ul class="list-group">
                                                    <li class="list-group-item">No cumplir con los principios y derechos del RGPD.</li>
                                                    <li class="list-group-item">No legalizar las transferencias internacionales de datos.</li>
                                                    <li class="list-group-item">No atender las resoluciones de la Autoridades de Control.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="text-align: center;">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cdlopd-outer-wrap">
                        <div class="cdlopd-inner-wrap">
                            <form action='options.php' method='post' style="margin: 2%;">
                                <?php
                                settings_fields('cdlopd_' . strtolower($current));
                                do_settings_sections('cdlopd_' . strtolower($current));
                                submit_button(null, "btn btn-block primary");
                                ?>
                            </form>
                            <form method="post" action="">
                                <p class="submit" style="text-align: center">
                                    <input name="reset" style="width: 80%;" class="btn btn-default" type="submit" value="<?php _e('Valores por defecto', 'click-datos-lopd'); ?>" >
                                    <input type="hidden" name="action" value="reset" />
                                </p>
                            </form>
                        </div>
                        <div class="cdlopd-banners">
                            <div class="cdlopd-banner vc_single_image-wrapper vc_box_shadow_3d  vc_box_border_grey">
                                <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img class="vc_single_image-img attachment-medium" src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/300X600.jpg'; ?>" alt="RGPD clikDatos" ></a>
                            </div>
                            <div class="cdlopd-banner hide-dbpro">
                                <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/clickdatos-lopd-web-cookie-RGPD.jpg'; ?>" alt="RGPD clikDatos" ></a>
                            </div>
                        </div>
                    </div><!-- .cdlopd-outer-wrap -->
                </div><!-- .wrap -->
                <?php
            }

            public function inicio() {
                $cu = wp_get_current_user();
                ?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery("#boton").click(function (event) {
                            document.getElementById('resultados').removeAttribute('style');
                            var nombreu = document.getElementById("nombreu").value;
                            var nombrec = document.getElementById("nombrec").value;
                            var email = document.getElementById("email").value;
                            var url = document.getElementById("url").value;
                            var mensaje = document.getElementById("mensaje").value;
                            if (nombreu === "" || email === "" || url === "") {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Rellene todos los datos con * del formulario. <br>Por favor.");
                            } else if (!jQuery('#checkprivacidad').is(':checked')) {
                                document.getElementById('resultados').setAttribute('style', 'color: red;');
                                jQuery('#resultados').html("Debes aceptar la Política de Privacidad para poder enviar el formulario.");
                            } else {
                                jQuery.post(url + '/wp-content/plugins/click-datos-lopd/admin/mail-contacto.php', {nombreu: nombreu, nombrec: nombrec, email: email, url: url, mensaje: mensaje}, function (respuesta) {
                                    if (respuesta === 'Tu correo ha sido enviado correctamente.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: green;');
                                    } else if (respuesta === 'Hubo un problema al enviar el email, intentalo de nuevo.') {
                                        document.getElementById('resultados').setAttribute('style', 'color: red;');
                                    }
                                    jQuery('#resultados').html(respuesta);
                                });
                            }
                        });
                    });
                </script>
                <div class="wrap">
                    <h1>RGPD ClickDatos</h1>
                    <div class="cdlopd-banner vc_single_image-wrapper vc_box_shadow_3d  vc_box_border_grey" style="text-align: center">
                        <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/728X90.jpg'; ?>" height="105" alt="RGPD clikDatos" ></a>
                        <a href="https://clickdatos.es/?utm_source=plugin&utm_medium=url_link&utm_campaign=%5BLD%5D%20Plugin%20RGPD" target="_blank" rel="nofollow noopener noreferrer"><img src="<?php echo CDLOPD_PLUGIN_URL . 'assets/images/clickdatos-lopd-web-cookie-RGPD.jpg'; ?>" height="105" alt="RGPD clikDatos" ></a>
                    </div>
                    <div class="form-horizontal sectionayuda" style="text-align: center;">
                        <h2>Comprobaremos si cumple con la RGPD y le responderemos cuanto antes, de forma gratuíta.</h2>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="nombreu">Nombre:*</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nombreu" id="nombreu" value="<?php echo $cu->user_login; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="nombrec">Nombre Completo:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nombrec" id="nombrec" value="<?php echo $cu->user_firstname . ' ' . $cu->user_lastname; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Email:*</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email" id="email" value="<?php echo $cu->user_email; ?>">
                                <input type="hidden" id="url" value="<?php echo get_site_url(); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="mensaje">Mensaje:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Introduce un mensaje..."></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="privacidad" id="checkprivacidad"> He leído y acepto <a target="_blank" rel="nofollow noopener noreferrer" href="https://clickdatos.es/politica-privacidad-plugin-rgpd-clickdatos/">Política de Privacidad</a> de ClickDatos.<br>
                            <button id="boton" class="btn btn-primary btn-lg" style="margin-top: 2%;">Enviar consulta</button><br><br>                        
                            <label id="resultados"></label>
                        </div>
                    </div>
                    <hr>
                    <h2 style="text-align: center;">¿Qué es el Reglamento General de Protección de Datos?</h2>
                    <p>El pasado 25 de mayo de 2016 entró en vigor el nuevo Reglamento General de Protección de Datos (RGPD) que deroga la Directiva 95/46/CE y viene a sustituir la normativa que actualmente estaba vigente en los países de la UE . Tiene lugar con motivo de la&nbsp;<a title="El enlace se abrirá en una nueva ventana" href="http://www.europarl.europa.eu/news/es/news-room/20160407IPR21776/reforma-de-la-protecci%C3%B3n-de-datos-%E2%80%93-nuevas-reglas-adaptadas-a-la-era-digital" target="_blank" rel="nofollow noopener noreferrer">reforma normativa realizada por la Unión Europea</a>&nbsp; cuyo objetivo es adaptar la legislación vigente a las exigencias en los niveles de seguridad que demanda la realidad digital actual.</p>
                    <p>Será de aplicación a partir del <strong>25 de mayo de 2018</strong> para todos los paises de la UE. Durante ese plazo de dos años hasta su implantación total, las empresas, los autónomos, asociaciones, comunidades de vecinos y las administraciones tienen la obligación de adaptarse a las nuevas directrices.</p>
                    <p><a title="El enlace se abrirá en una nueva ventana" href="https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:32016R0679" target="_blank" rel="nofollow noopener noreferrer">Reglamento (UE) 2016/679</a> del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales y a la libre circulación de estos datos y por el que se deroga la Directiva 95/46/CE por el Reglamento General de Protección de Datos.</p>

                    <h2 style="text-align: center;">Guías y directrices sobre el reglamento y su aplicación</h2>
                    <ul style="text-align: center;">
                        <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/guia-para-el-cumplimiento-del-deber-de-informar/" target="_blank" rel="nofollow noopener noreferrer">Guía para el cumplimiento del deber de informar</a></li>
                        <li class="list-group-item"><a title="El enlace se abrirá en una nueva ventana" href="https://clickdatos.es/directrices-para-la-elaboracion-de-contratos-entre-responsables-y-encargados-del-tratamiento/" target="_blank" rel="nofollow noopener noreferrer">Directrices para la elaboración de contratos entre responsables y encargados del tratamiento</a></li>
                        <li class="list-group-item"><a href="https://clickdatos.es/guia-rgpd-para-responsables-de-tratamiento/" target="_blank" rel="noopener">Guía del Reglamento General de Protección de Datos para responsables de tratamiento</a></li>
                        <li class="list-group-item"><a href="https://clickdatos.es/orientaciones-y-garantias-en-los-procedimientos-de-anonimizacion-de-datos-personales/" target="_blank" rel="nofollow noopener noreferrer">Orientaciones y garantías en los procedimientos de anonimización de datos personales</a></li>
                    </ul>

                    <h2 style="text-align: center;">Principios del RGPD</h2>
                    <ul class="list-group">
                        <li class="list-group-item">&#8226; El tratamiento de datos sólo será leal y lícito si la información es accesible y comprensible.</li>
                        <li class="list-group-item">&#8226; Los datos han de ser recogidos para un fin determinado y sólo utilizados para dicho fin, considerando la existencia de excepciones para ciertos supuestos concretos.</li>
                        <li class="list-group-item">&#8226; Los datos personales han de ser los adecuados, concretos y siempre limitados a la necesidad concreta para ser recabados</li>
                        <li class="list-group-item">&#8226; Los datos han de ser precisos y deben ser actualizados en todo momento.</li>
                        <li class="list-group-item">&#8226; Los datos han de ser mantenidos tan sólo durante el tiempo necesario para cumplir la finalidad de los mismos y han de ser cancelados, estableciendo el responsable de estos el plazo para la supresión o revisión de los mismos.</li>
                        <li class="list-group-item">&#8226; Ha de velarse por la seguridad de los datos utilizando todos los medios técnicos y organizativos adecuados.</li>
                        <li class="list-group-item">&#8226; Se establece que el responsable del tratamiento será además el responsable del cumplimiento de todos los principios aquí citados</li>
                    </ul>

                    <h2 style="text-align: center;">Nuevos derechos para los dueños de los datos</h2>
                    <p>Además de los derechos <strong>ARCO</strong> vigentes actualmente (acceso, rectificación, cancelación y oposición) se añaden otros 3:</p>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>&#8226; Derecho de Supresión:</strong>&nbsp;Las personas podrán solicitar la eliminación total de sus datos personales en determinados casos.</li>
                        <li class="list-group-item"><strong>&#8226; Derecho de Limitación:</strong>&nbsp;Permite la suspensión del tratamiento de los datos del interesado en algunos casos como impugnaciones o la conservación de los datos aún en casos en los que no sea necesaria por alguna razón administrativa o legal.</li>
                        <li class="list-group-item"><strong>&#8226; Derecho de Portabilidad:</strong> El dueño de los datos personales tendrá la posibilidad de solicitar una copia al responsable de su tratamiento y obtenerlos en un formato estandarizado y mecanizado.</li>
                    </ul>
                    <h2 style="text-align: center;">Nuevas obligaciones para empresas, administraciones y otras entidades</h2>
                    <ul class="list-group">
                        <li class="list-group-item">&#8226; En ciertos supuestos se establece la obligatoriedad de la designación de un <strong>Delegado de Protección de Datos (DPO).</strong></li>
                        <li class="list-group-item">&#8226; En determinados casos se realizarán <strong> Evaluaciones de Impacto</strong> sobre la Privacidad para averiguar los riesgos que pudieran existir con el tratamiento de ciertos datos personales y traten de establecer medidas y directrices para minimizarlos o eliminarlos. Las empresas de ámbito multinacional tendrán como interlocutor a un sólo <strong>organismo de control nacional </strong>(ventanilla única).</li>
                        <li class="list-group-item">&#8226; Se establece la obligatoriedad de&nbsp;<strong>informar a las autoridades&nbsp;</strong>de control competentes y a los&nbsp;<strong>afectados&nbsp;</strong>en casos graves de las brechas de seguridad que pudieran encontrase, estableciendo para ello un plazo máximo de&nbsp;<strong>72 horas&nbsp;</strong>desde su detección.</li>
                        <li class="list-group-item">&#8226; Se amplía el listado de los&nbsp;<strong>datos especialmente protegidos&nbsp;</strong>(datos sensibles) con los datos genéticos, biométricos, infracciones y condenas penales.</li>
                        <li class="list-group-item">&#8226; El responsable encargado del tratamiento de los datos ha de&nbsp;<strong>garantizar el cumplimiento&nbsp;</strong>de la norma, dato que influirá en la selección del mismo.</li>
                        <li class="list-group-item">&#8226; Se establece un marco de garantías y mecanismos de seguimiento más estrictos para el caso de las&nbsp;<strong>transferencias internacionales fuera de la UE.</strong></li>
                        <li class="list-group-item">&#8226; Se prevee la creación de&nbsp;<strong>sellos y certificaciones&nbsp;</strong>que acrediten la Responsabilidad Proactiva de las organizaciones.</li>
                        <li class="list-group-item">&#8226; La obligación de inscribir los ficheros desaparece y será sustituída por un&nbsp;<strong>control interno&nbsp;</strong>y , en ocasiones, por un&nbsp;<strong>inventario&nbsp;</strong>de las operaciones de tratamiento realizadas.</li>
                        <li class="list-group-item">&#8226; Las&nbsp;<strong>sanciones&nbsp;</strong>por incumplimiento&nbsp;<strong>aumentan su cuantía&nbsp;</strong>pudiendo alcanzar los&nbsp;<strong>20 millones de euros&nbsp;</strong>o el&nbsp;<strong>4% de la facturación&nbsp;</strong>global de la empresa.</li>
                    </ul>

                    <h2 style="text-align: center;">¿Por qué debería contratar la auditoría y adaptarme al RGPD?</h2>
                    <p>El organismo estatal encargado de supervisar la recepción de datos de las empresas (Agencia Española de Protección de Datos) recibe más de 10.000 denuncias anuales  por posibles incumplimientos de la LOPD y el RGPD. Las multas pueden llegar  hasta los 20 millones de euros o hasta el 4% de volumen de negocio anual global dependiendo de la gravedad del caso.</p>
                    <h3 style="text-align: center;"><strong>Sanciones de hasta 10.000.000 o el 2% del volumen de negocio anual&nbsp;global del ejercicio financiero anterior por:</strong></h3>
                    <ul class="list-group" style="text-align: center;">
                        <li class="list-group-item">&#8226; No aplicar medidas técnicas y organizativas por defecto.</li>
                        <li class="list-group-item">&#8226; No realizar la correspondiente Evaluación de Impacto.</li>
                        <li class="list-group-item">&#8226; No disponer del registro de actividades de tratamiento.</li>
                        <li class="list-group-item">&#8226; No designar un DPO.</li>
                        <li class="list-group-item">&#8226; No notificar las brechas de seguridad.</li>
                    </ul>
                    <h3 style="text-align: center;"><strong>Sanciones de hasta 20.000.000€ o el 4% del volumen de negocio anual global del ejercicio financiero anterior por:</strong></h3>
                    <ul class="list-group" style="text-align: center;">
                        <li class="list-group-item">&#8226; No cumplir con los principios y derechos del RGPD.</li>
                        <li class="list-group-item">&#8226; No legalizar las transferencias internacionales de datos.</li>
                        <li class="list-group-item">&#8226; No atender las resoluciones de la Autoridades de Control.</li>
                    </ul>
                </div><!-- .wrap -->
                <?php
            }

        }

    }

                   


