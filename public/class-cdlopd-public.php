<?php
/*
 * Public class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin public class
 * */
if (!class_exists('CDLOPD_Public')) { // Don't initialise if there's already a class activated

    class CDLOPD_Public {

        public function __construct() {
            //
        }

        /* 		
         * Initialize the class and start calling our hooks and filters		
         * @since 2.0.0		
         */

        public function init() {
            add_filter('body_class', array($this, 'body_class'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_head', array($this, 'add_css'));
            add_action('wp_footer', array($this, 'add_js'), 1000);
            add_action('wp_footer', array($this, 'add_notification_bar'), 1000);
            add_action('wp_head', array($this, 'check_cookies_header'));
            add_action('wp_footer', array($this, 'check_cookies_footer'));
        }

        /* 		
         * Initialize the class and start calling our hooks and filters		
         * @since 2.0.0		
         */

        public function body_class($classes) {
            return $classes;
        }

        /*
         * Enqueue styles and scripts
         * @since 2.0.0
         */

        public function enqueue_scripts() {
            $exclude = $this->show_bar();
            // Only do all this if post isn't excluded
            if (!empty($exclude)) {
                wp_enqueue_style('cookie-consent-style', CDLOPD_PLUGIN_URL . 'assets/css/style.css', '2.3.0');
                wp_enqueue_script('cookie-consent', CDLOPD_PLUGIN_URL . 'assets/js/click-datos-lopd-js.js', array('jquery'), '2.3.0', true);
                wp_localize_script(
                        'cookie-consent', 'cdlopd_vars', array(
                    'version' => 1,
                        )
                );
            }
        }

        /*
         * Check if post or page is excluded from displaying the bar
         * @since 2.2.0
         */

        public function show_bar() {
            global $post;
            $options = get_option('cdlopd_options_settings');
            if (isset($post->ID)) {
                $post_id = $post->ID;
                $excluded = get_post_meta($post_id, 'cdlopd_exclude', true);
                if ($excluded == 1 && !empty($options['enable_metafield'])) {
                    return false;
                }
            }
            return true;
        }

        /*
         * Add some CSS to the header
         * @since 2.0.0
         */

        public function add_css() {
            $exclude = $this->show_bar();
            // Only do all this if post isn't excluded
            if (!empty($exclude)) {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                $position_css = 'position: fixed;
					left: 0;
					top: 0;
					width: 100%;';
                // Figure out the bar position
                if (!isset($cdlopd_content_settings['position'])) {
                    $cdlopd_content_settings['position'] = "";
                }

                if ($cdlopd_content_settings['position'] == 'top-bar') {
                    $position_css = 'position: fixed;
					left: 0;
					top: 0;
					width: 100%;';
                } else if ($cdlopd_content_settings['position'] == 'bottom-bar') {
                    $position_css = 'position: fixed;
					left: 0;
					bottom: 0;
					width: 100%;';
                } else if ($cdlopd_content_settings['position'] == 'top-right-block') {
                    $position_css = 'position: fixed;
					right: 20px;
					top: 6%;
					width: 300px;';
                } else if ($cdlopd_content_settings['position'] == 'top-left-block') {
                    $position_css = 'position: fixed;
					left: 20px;
					top: 6%;
					width: 300px;';
                } else if ($cdlopd_content_settings['position'] == 'bottom-left-block') {
                    $position_css = 'position: fixed;
					left: 20px;
					bottom: 6%;
					width: 300px;';
                } else if ($cdlopd_content_settings['position'] == 'bottom-right-block') {
                    $position_css = 'position: fixed;
					right: 20px;
					bottom: 6%;
					width: 300px;';
                }
                 else if ($cdlopd_content_settings['position'] == 'bottom-center-block') {
                    $position_css = 'background: rgba(33,41,52,.85)!important;
    bottom: 0!important;
    display: flex;
    left: 0!important;
    overflow-y: scroll;
    position: fixed!important;
    right: 0!important;
    top: 0!important;
    transition: opacity .15s ease;
    z-index: 2147483647!important';
                }
                // Get our styles
                if (!isset($cdlopd_content_settings['text_color'])) {
                    $cdlopd_content_settings['text_color'] = '';
                }
                $text_color = $cdlopd_content_settings['text_color'];

                if (!isset($cdlopd_content_settings['bg_color'])) {
                    $cdlopd_content_settings['bg_color'] = '';
                }
                $bg_color = $cdlopd_content_settings['bg_color'];

                if (!isset($cdlopd_content_settings['link_color'])) {
                    $cdlopd_content_settings['link_color'] = '';
                }
                $link_color = $cdlopd_content_settings['link_color'];
                
                if(!isset($cdlopd_content_settings['grosor_border'])){
                    $cdlopd_content_settings['grosor_border'] = '';
                }
                $grosor_border = $cdlopd_content_settings['grosor_border'];
                
                if(!isset($cdlopd_content_settings['color_border'])){
                    $cdlopd_content_settings['color_border'] = '';
                }
                $color_border = $cdlopd_content_settings['color_border'];
                
                //Agrega los colores para el boton aceptar
                if (!isset($cdlopd_content_settings['button_bg_color_aceptar'])) {
                    $cdlopd_content_settings['button_bg_color_aceptar'] = '#90CD0D';
                }
                $button_bg_aceptar = $cdlopd_content_settings['button_bg_color_aceptar'];

                if (!isset($cdlopd_content_settings['button_color_aceptar'])) {
                    $cdlopd_content_settings['button_color_aceptar'] = '#fff';
                }
                $button_color_aceptar = $cdlopd_content_settings['button_color_aceptar'];
                //Agrega los colores para el boton rechazar
                if (!isset($cdlopd_content_settings['button_bg_color_rechazar'])) {
                    $cdlopd_content_settings['button_bg_color_rechazar'] = '#CD0D0D';
                }
                $button_bg_rechazar = $cdlopd_content_settings['button_bg_color_rechazar'];

                if (!isset($cdlopd_content_settings['button_color_rechazar'])) {
                    $cdlopd_content_settings['button_color_rechazar'] = '#fff';
                }
                $button_color_rechazar = $cdlopd_content_settings['button_color_rechazar'];
                
                if (!isset($cdlopd_content_settings['duracion_cookie_aceptar'])) {
                    $cdlopd_content_settings['duracion_cookie_aceptar'] = '';
                }
                
                if (!isset($cdlopd_content_settings['duracion_cookie_rechazar'])) {
                    $cdlopd_content_settings['duracion_cookie_rechazar'] = '';
                }

                $button_style = 'border: 0; padding: 6px 9px; border-radius: 3px;';
                
                $button_color = '';

                // Build our CSS
                $css = '<style id="cdlopd-css" type="text/css" media="screen">';
                $css .= '
				#catapult-cookie-bar {
					box-sizing: border-box;
					max-height: 0;
					opacity: 0;
					z-index: 99999;
					overflow: hidden;
					color: ' . $text_color . ';
					' . $position_css . '
					background-color: ' . $bg_color . ';
                                        border-style: solid;
                                        border-width: ' . $grosor_border . 'px;
                                        border-color: ' . $color_border . ';
				}
				#catapult-cookie-bar a {
					color: ' . $link_color . ';
				}
				#catapult-cookie-bar .x_close span {
					background-color: ' . $button_color . ';
				}
				button#catapultCookieAceptar {
					background:' . $button_bg_aceptar . ';
					color: ' . $button_color_aceptar . ';
					' . $button_style . '
				}
                                button#catapultCookieRechazar {
					background:' . $button_bg_rechazar . ';
					color: ' . $button_color_rechazar . ';
					' . $button_style . '
				}
				#catapult-cookie-bar h3 {
					color: ' . $text_color . ';
				}
				.has-cookie-bar #catapult-cookie-bar {
					opacity: 1;
					max-height: 999px;
					min-height: 30px;
				}';
                $css .= '</style>';
                echo $css;
                // Add it to the header
            }
        }

        /*
         * Add some JS to the footer
         * @since 2.0.0
         */

        public function add_js() {

            $exclude = $this->show_bar();
            // Only do all this if post isn't excluded
            if (!empty($exclude)) {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                if (!isset($cdlopd_content_settings['position'])) {
                    $cdlopd_content_settings['position'] = '';
                }
                if ($cdlopd_content_settings['position'] == 'top-bar' || $cdlopd_content_settings['position'] == 'bottom-bar') {
                    $type = 'bar';
                } else {
                    $type = 'block';
                }
                ?>

                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        if (catapultReadCookie('catAccCookies') || catapultReadCookie("catAccCookiesDeny") /*|| catapultReadCookie("catAccCookiesUnan")*/) {

                        } else {
                            $("body").addClass("has-cookie-bar");
                            $("body").addClass("cookie-bar-<?php echo $type; ?>");
                        }

//                        if (catapultReadCookie('catAccCookies') || catapultReadCookie("catAccCookiesDeny")) {
//
//                        } else {
//                            setTimeout("cookiesinaceptarnirechazar()", 30000);
//                        }
//
//                        if (catapultReadCookie("catAccCookiesUnan")) {
//                            setTimeout(function () {
//                                $("body").addClass("has-cookie-bar");
//                                $("body").addClass("cookie-bar-<?php //echo $type; ?>");
//                                setTimeout("cookiesinaceptarnirechazar()", 30000);
//                            }, 300000);
//                        }
                    });
                </script>


                <?php
            }
        }

        /*
         * Add the notification bar itself
         * @since 2.0.0
         */

        public function add_notification_bar() {

            $exclude = $this->show_bar();
            // Only do all this if post isn't excluded
            if (!empty($exclude)) {
                $cdlopd_content_settings = get_option('cdlopd_content_settings');
                // Check if it's a block or a bar
                $is_block = true;

                if (!isset($cdlopd_content_settings['position'])) {
                    $cdlopd_content_settings['position'] = '';
                }

                if ($cdlopd_content_settings['position'] == 'top-bar' || $cdlopd_content_settings['position'] == 'bottom-bar') {
                    $is_block = false; // It's a bar
                }

                // Add some classes to the block
                $classes = '';
                $classes .= ' rounded-corners';
                $classes .= ' drop-shadow';

                if (empty($cdlopd_content_settings['display_accept_with_text'])) {
                    $classes .= 'float-accept';
                }

                // Allowed tags
                $allowed = array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'br' => array(),
                    'em' => array(),
                    'strong' => array(),
                    'p' => array()
                );

                $content = '';
                $close_content = '';

                // Print the notification bar
                $content = '<div id="catapult-cookie-bar" class="' . $classes . '">';

                // Add a custom wrapper class if specified
                if ($cdlopd_content_settings['position'] == 'top-bar' || $cdlopd_content_settings['position'] == 'bottom-bar') {
                    $content .= '<div class="cdlopd-inner ">';
                    $close_content = '</div><!-- custom wrapper class -->';
                }
                
                
                if ($cdlopd_content_settings['position'] == 'bottom-center-block') {
                 $content .= '<div id="rgpd-intrusivo">';
                    $close_content = '</div><!-- cerramos rgpd intrusivo -->';
                
                }
                

                // Add a title if it's a block
                if ($cdlopd_content_settings['position'] != 'top-bar' && $cdlopd_content_settings['position'] != 'bottom-bar' && $cdlopd_content_settings['position'] != 'bottom-center-block') {
                    $heading_text = wp_kses($cdlopd_content_settings['heading_text'], $allowed);
                    $heading_text = apply_filters('cdlopd_heading_text', $heading_text);
                    $content .= sprintf('<span>%s</span>', $heading_text
                    );
                }
                
                
                 if ($cdlopd_content_settings['position'] == 'bottom-center-block') {
                    $heading_text = wp_kses($cdlopd_content_settings['heading_text'], $allowed);
                    $heading_text = apply_filters('cdlopd_heading_text', $heading_text);
                    
 
$custom_logo_id = get_theme_mod( 'custom_logo' );
 
$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
 

                    $content .= sprintf('<h3 class="rgpd-h3">%s</h3><p id="rgpd-p-imagen"><img src="'.$image[0].'" class="rgpd-logo-cookie"/></p>', $heading_text
                    );
                }
                
                
                
                

                // Make the Read More link
                $more_text = '';
                if ($cdlopd_content_settings['more_info_text']) {
                    // Find what page we're linking to
                    if (!empty($cdlopd_content_settings['more_info_url'])) {
                        // Check the absolute URL first
                        $link = $cdlopd_content_settings['more_info_url'];
                    } else {
                        // Make sure, we get the right page translation, using PolyLang, if pll_get_post function exists
                        $page_id = $cdlopd_content_settings['more_info_page'];
                        if (function_exists('pll_get_post')) {
                            $page_id = pll_get_post($page_id);
                        }
                        // Use the internal page
                        $link = get_permalink($page_id);
                    }
                    $more_info_text = wp_kses($cdlopd_content_settings['more_info_text'], $allowed);
                    $more_info_text = apply_filters('cdlopd_more_info_text', $more_info_text);
                    $more_text = sprintf(
                            '<a class="cdlopd-more-info-link" id="pag_informacion" tabindex=0 target="%s" href="%s">%s</a>', esc_attr($cdlopd_content_settings['more_info_target']), esc_url($link), $more_info_text
                    );
                }
                
                $button_text = '';
                $buttonr_text = '';
                if($cdlopd_content_settings['opcion_cookie']){
                    $opcion_cookie = wp_kses($cdlopd_content_settings['opcion_cookie'], $allowed);
                    $opcion_cookie = apply_filters('cdlopd_opcion_cookie', $opcion_cookie);
                    if($opcion_cookie === 'botones'){
                        $accept_text = wp_kses($cdlopd_content_settings['accept_text'], $allowed);
                        $accept_text = apply_filters('cdlopd_accept_text', $accept_text);
                        //recoge el valor de la duración de la cookie para asignárselo cuando se de click al botón de Aceptar
                        $duracion_cookie_aceptar = wp_kses($cdlopd_content_settings['duracion_cookie_aceptar'], $allowed);
                        $duracion_cookie_aceptar = apply_filters('cdlopd_duracion_cookie_aceptar', $duracion_cookie_aceptar);

                        $button_text = sprintf(
                                '<button id="catapultCookieAceptar" tabindex=0 onclick="catapultAcceptCookies(%s);">%s</button><input type="hidden" id="opcionCookie" value="%s">', $duracion_cookie_aceptar, $accept_text, $opcion_cookie
                        );
                        
                        $deny_text = wp_kses($cdlopd_content_settings['deny_text'], $allowed);
                        $deny_text = apply_filters('cdlopd_deny_text', $deny_text);
                        //recoge el valor de la duración de la cookie para asignárselo cuando se de click al botón de Rechazar
                        $duracion_cookie_rechazar = wp_kses($cdlopd_content_settings['duracion_cookie_rechazar'], $allowed);
                        $duracion_cookie_rechazar = apply_filters('cdlopd_duracion_cookie_rechazar', $duracion_cookie_rechazar);
                        $buttonr_text = sprintf(
                                '<button id="catapultCookieRechazar" tabindex=0 onclick="catapultDenyCookies(%s);">%s</button>', $duracion_cookie_rechazar, $deny_text
                        );
                    }
                    else if($opcion_cookie === 'scroll'){
                        $accept_text = wp_kses($cdlopd_content_settings['accept_text'], $allowed);
                        $accept_text = apply_filters('cdlopd_accept_text', $accept_text);
                        $duracion_cookie_aceptar = wp_kses($cdlopd_content_settings['duracion_cookie_aceptar'], $allowed);
                        $duracion_cookie_aceptar = apply_filters('cdlopd_duracion_cookie_aceptar', $duracion_cookie_aceptar);
                        $deny_text = wp_kses($cdlopd_content_settings['deny_text'], $allowed);
                        $deny_text = apply_filters('cdlopd_deny_text', $deny_text);
                        $duracion_cookie_rechazar = wp_kses($cdlopd_content_settings['duracion_cookie_rechazar'], $allowed);
                        $duracion_cookie_rechazar = apply_filters('cdlopd_duracion_cookie_rechazar', $duracion_cookie_rechazar);
                        $porcentaje_scroll = wp_kses($cdlopd_content_settings['porcentaje_scroll'], $allowed);
                        $porcentaje_scroll = apply_filters('cdlopd_porcentaje_scroll', $porcentaje_scroll);
                        $texto_scroll = wp_kses($cdlopd_content_settings['scroll_text'], $allowed);
                        $texto_scroll = apply_filters('cdlopd_scroll_text', $texto_scroll);
                        $input_porcentaje = "";
                        $input_porcentaje = sprintf(
                                '<p id="texto_scroll">%s</p><input type="hidden"  id="scrollCaducidad" value="%s">'
                                . '<input type="hidden" id="porcent_scroll" value="%s">'
                                . '<input type="hidden" id="opcionCookie" value="%s">'
                                . '<input type="hidden" id="textAceptar" value="%s">'
                                . '<input type="hidden" id="caducidadRechazar" value="%s">'
                                . '<input type="hidden" id="textRechazar" value="%s">'
                                . '<input type="hidden" id="textoScroll" value="%s">', 
                                $texto_scroll, $duracion_cookie_aceptar, $porcentaje_scroll, $opcion_cookie, $accept_text, $duracion_cookie_rechazar, $deny_text, $texto_scroll
                        );
                    }
                }

                // Boton de rechazar o no 

                if (get_post_meta(11122, 'sheader', TRUE) == true || get_post_meta(11123, 'sfooter', TRUE) == true) {
                    $notification_text = wp_kses_post(do_shortcode($cdlopd_content_settings['notification_text']));
                    $notification_text = apply_filters('cdlopd_notification_text', $notification_text);
                    if ($cdlopd_content_settings['position'] == 'bottom-center-block') {
                    $content .= sprintf(
                            '<span class="cdlopd-left-side rgpd-texto-centro ">%s %s</span><span class="cdlopd-right-side rgpd-texto-centro">%s %s %s</span>', $notification_text, $more_text, $button_text, $buttonr_text, $input_porcentaje
                    );
                    }else {
                      $content .= sprintf(
                            '<span class="cdlopd-left-side ">%s %s</span><span class="cdlopd-right-side">%s %s %s</span>', $notification_text, $more_text, $button_text, $buttonr_text, $input_porcentaje
                    );  
                    }
                } else {
                    $notification_text = wp_kses_post(do_shortcode($cdlopd_content_settings['notification_text']));
                    $notification_text = apply_filters('cdlopd_notification_text', $notification_text);
                    $content .= sprintf(
                            '<span class="cdlopd-left-side">%s %s</span><span class="cdlopd-right-side">%s %s %s</span>', $notification_text, $more_text, $button_text, $buttonr_text, $input_porcentaje
                    );
                }

                // Close custom wrapper class if used
                $content .= $close_content;

                $content .= '</div><!-- #catapult-cookie-bar -->';

                echo apply_filters('catapult_cookie_content', $content, $cdlopd_content_settings);
            }
        }

        public function check_cookies_header() {
            if (isset($_COOKIE['catAccCookies']) && $_COOKIE['catAccCookies'] == 1) {
                if (get_post_meta(11122, 'sheader', TRUE) == true) {
                    $scriptsheader = get_post_meta(11122, 'sheader', TRUE);
                    echo $scriptsheader;
                }
            }
        }

        public function check_cookies_footer() {
            if (isset($_COOKIE['catAccCookies']) && $_COOKIE['catAccCookies'] == 1) {
                if (get_post_meta(11123, 'sfooter', TRUE) == true) {
                    $scriptsfooter = get_post_meta(11123, 'sfooter', TRUE);
                    echo $scriptsfooter;
                }
            }
        }

    }

}