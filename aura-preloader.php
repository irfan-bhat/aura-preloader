<?php
/**
 * Plugin Name: Aura Preloader
 * Plugin URI:  https://wordpress.org/plugins/aura-preloader/
 * Description: A customisable full-screen backdrop-blur preloader with your logo, spinner, and progress bar.
 * Version:     1.3.1
 * Author:      Irfan Bhat
 * Author URI:  https://irfanbhat.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aura-preloader
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AURA_VERSION',     '1.3.0' );
define( 'AURA_DIR',         plugin_dir_path( __FILE__ ) );
define( 'AURA_URL',         plugin_dir_url( __FILE__ ) );
define( 'AURA_OPT',         'aura_preloader_options' );
define( 'AURA_GITHUB_REPO', 'irfanbhat/aura-preloader' );

/* ---------------------------------------------------------------
   Includes
--------------------------------------------------------------- */
require_once AURA_DIR . 'includes/presets.php';

/* ---------------------------------------------------------------
   Default options
--------------------------------------------------------------- */
function aura_defaults() {
    return [
        'overlay_color'      => '#ffffff',
        'overlay_opacity'    => 20,
        'blur_strength'      => 12,
        'accent_color'       => '#DC501E',
        'logo_url'           => '',
        'logo_width'         => 64,
        'show_bar'           => true,
        'show_ring'          => true,
        'fade_duration'      => 500,
        'min_display'        => 800,
        'enable_mobile'      => true,
        'spinner_type'       => 'spinner',
        'animation_speed'    => 'normal',
        'progress_bar_style' => 'linear',
        'show_progress'      => true,
    ];
}

function aura_options() {
    $saved = get_option( AURA_OPT );
    if ( false === $saved ) {
        // Migration fallback for previous option key
        $saved = get_option( 'logo_preloader_options', [] );
    }
    return wp_parse_args( $saved, aura_defaults() );
}

/* ---------------------------------------------------------------
   Activation — save defaults
--------------------------------------------------------------- */
register_activation_hook( __FILE__, function() {
    if ( ! get_option( AURA_OPT ) ) {
        update_option( AURA_OPT, aura_options() );
    }
});

/* ---------------------------------------------------------------
   Front-end: inject preloader HTML right after <body>
--------------------------------------------------------------- */
add_action( 'wp_body_open', 'aura_render_preloader' );
function aura_render_preloader() {
    $o        = aura_options();

    // Skip on mobile if disabled
    if ( ! $o['enable_mobile'] && wp_is_mobile() ) return;
    $logo_src = esc_url( $o['logo_url'] );
    $logo_w   = absint( $o['logo_width'] );
    $show_img = $logo_src ? "<img src=\"{$logo_src}\" alt=\"\" style=\"width:{$logo_w}px;\">" : '';
    ?>
    <div id="aura-preloader" role="status" aria-label="<?php esc_attr_e( 'Loading', 'aura-preloader' ); ?>">
        <div class="aura-inner">
            <?php if ( $o['show_ring'] ) : ?>
            <div class="aura-wrap">
                <div class="aura-ring"></div>
                <?php echo $show_img; ?>
            </div>
            <?php elseif ( $show_img ) : ?>
            <div class="aura-wrap"><?php echo $show_img; ?></div>
            <?php endif; ?>

            <?php if ( $o['show_bar'] ) : ?>
            <div class="aura-bar"><div class="aura-fill"></div></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* ---------------------------------------------------------------
   Front-end: enqueue CSS + JS (with inline config)
--------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'aura_enqueue_frontend' );
function aura_enqueue_frontend() {
    $o = aura_options();

    // Skip on mobile if disabled
    if ( ! $o['enable_mobile'] && wp_is_mobile() ) return;

    wp_enqueue_style(
        'aura-preloader',
        AURA_URL . 'css/preloader.css',
        [],
        AURA_VERSION
    );

    // Enqueue animation styles based on selected speed
    $animation_speed = sanitize_text_field( $o['animation_speed'] ?? 'normal' );
    wp_enqueue_style(
        'aura-preloader-animation-' . $animation_speed,
        AURA_URL . 'css/animations/' . $animation_speed . '.css',
        [ 'aura-preloader' ],
        AURA_VERSION
    );

    // Enqueue progress bar styles if enabled
    if ( ! empty( $o['show_progress'] ) ) {
        wp_enqueue_style(
            'aura-preloader-progress-bars',
            AURA_URL . 'css/progress-bars.css',
            [ 'aura-preloader' ],
            AURA_VERSION
        );
    }

    // Inline dynamic CSS vars
    $overlay_color   = sanitize_hex_color( $o['overlay_color'] ) ?: '#ffffff';
    $overlay_opacity = max( 0, min( 100, absint( $o['overlay_opacity'] ) ) );
    $blur            = max( 0, min( 40,  absint( $o['blur_strength'] ) ) );
    $accent          = sanitize_hex_color( $o['accent_color'] );
    $logo_w          = absint( $o['logo_width'] );

    // Convert hex + opacity to rgba
    list( $r, $g, $b ) = sscanf( $overlay_color, '#%02x%02x%02x' );
    $alpha   = round( $overlay_opacity / 100, 2 );
    $rgba    = "rgba({$r},{$g},{$b},{$alpha})";

    $inline  = "#aura-preloader{--aura-overlay:{$rgba};--aura-blur:{$blur}px;--aura-accent:{$accent};--aura-logo-w:{$logo_w}px;}";
    wp_add_inline_style( 'aura-preloader', $inline );

    wp_enqueue_script(
        'aura-preloader',
        AURA_URL . 'js/preloader.js',
        [],
        AURA_VERSION,
        true   // footer
    );

    // Pass config to JS
    wp_localize_script( 'aura-preloader', 'auraConfig', [
        'fade'              => absint( $o['fade_duration'] ),
        'minDisplay'        => absint( $o['min_display'] ),
        'spinnerType'       => sanitize_text_field( $o['spinner_type'] ?? 'spinner' ),
        'animationSpeed'    => $animation_speed,
        'progressBarStyle'  => sanitize_text_field( $o['progress_bar_style'] ?? 'linear' ),
        'showProgress'      => ! empty( $o['show_progress'] ),
        'svgUrl'            => AURA_URL . 'assets/svgs/',
    ]);
}

/* ---------------------------------------------------------------
   Admin: settings page
--------------------------------------------------------------- */
add_action( 'admin_menu', function() {
    add_options_page(
        __( 'Aura Preloader', 'aura-preloader' ),
        __( 'Aura Preloader', 'aura-preloader' ),
        'manage_options',
        'aura-preloader',
        'aura_settings_page'
    );
});

add_action( 'admin_init', 'aura_register_settings' );
function aura_register_settings() {
    register_setting( 'aura_preloader_group', AURA_OPT, [
        'sanitize_callback' => 'aura_sanitize_options',
    ]);
}

function aura_sanitize_options( $input ) {
    $d   = aura_defaults();
    $out = [];
    $out['overlay_color']       = sanitize_hex_color( $input['overlay_color']   ?? $d['overlay_color'] )   ?: $d['overlay_color'];
    $out['overlay_opacity']     = max( 0,  min( 100,  absint( $input['overlay_opacity'] ?? $d['overlay_opacity'] ) ) );
    $out['blur_strength']       = max( 0,  min( 40,   absint( $input['blur_strength']   ?? $d['blur_strength'] ) ) );
    $out['accent_color']        = sanitize_hex_color( $input['accent_color']  ?? $d['accent_color'] )  ?: $d['accent_color'];
    $out['logo_url']            = esc_url_raw( $input['logo_url']             ?? '' );
    $out['logo_width']          = max( 20, min( 300, absint( $input['logo_width']   ?? $d['logo_width'] ) ) );
    $out['fade_duration']       = max( 0,  min( 3000, absint( $input['fade_duration'] ?? $d['fade_duration'] ) ) );
    $out['min_display']         = max( 0,  min( 5000, absint( $input['min_display']   ?? $d['min_display'] ) ) );
    $out['show_bar']            = ! empty( $input['show_bar'] );
    $out['show_ring']           = ! empty( $input['show_ring'] );
    $out['enable_mobile']       = ! empty( $input['enable_mobile'] );
    
    // Validate preset options
    $spinners = array_keys( aura_get_presets() );
    $out['spinner_type']        = in_array( $input['spinner_type'] ?? '', $spinners, true ) ? sanitize_text_field( $input['spinner_type'] ) : $d['spinner_type'];
    
    $speeds = array_keys( aura_get_animation_speeds() );
    $out['animation_speed']     = in_array( $input['animation_speed'] ?? '', $speeds, true ) ? sanitize_text_field( $input['animation_speed'] ) : $d['animation_speed'];
    
    $bars = array_keys( aura_get_progress_bar_styles() );
    $out['progress_bar_style']  = in_array( $input['progress_bar_style'] ?? '', $bars, true ) ? sanitize_text_field( $input['progress_bar_style'] ) : $d['progress_bar_style'];
    
    $out['show_progress']       = ! empty( $input['show_progress'] );
    return $out;
}

/* ---------------------------------------------------------------
   Admin: enqueue media uploader
--------------------------------------------------------------- */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( $hook !== 'settings_page_aura-preloader' ) return;
    wp_enqueue_media();
    wp_enqueue_style( 'aura-admin', AURA_URL . 'admin/admin.css', [], AURA_VERSION );
    wp_enqueue_script( 'aura-admin', AURA_URL . 'admin/admin.js', [ 'jquery' ], AURA_VERSION, true );
});

/* ---------------------------------------------------------------
   Settings page HTML
--------------------------------------------------------------- */
function aura_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $o = aura_options();
    ?>
    <div class="wrap aura-wrap-admin">
        <h1><?php esc_html_e( 'Aura Preloader Settings', 'aura-preloader' ); ?></h1>

        <div class="aura-admin-layout">
            <!-- ── Form ── -->
            <div class="aura-form-col">
                <form method="post" action="options.php">
                    <?php settings_fields( 'aura_preloader_group' ); ?>

                    <div class="aura-card">
                        <h2><?php esc_html_e( 'Logo', 'aura-preloader' ); ?></h2>

                        <div class="aura-field">
                            <label><?php esc_html_e( 'Logo image', 'aura-preloader' ); ?></label>
                            <div class="aura-media-row">
                                <input type="text" id="aura-logo-url" name="<?php echo AURA_OPT; ?>[logo_url]"
                                    value="<?php echo esc_attr( $o['logo_url'] ); ?>" class="regular-text">
                                <button type="button" id="aura-upload-btn" class="button">
                                    <?php esc_html_e( 'Choose image', 'aura-preloader' ); ?>
                                </button>
                                <button type="button" id="aura-remove-btn" class="button aura-remove"
                                    <?php echo $o['logo_url'] ? '' : 'style="display:none"'; ?>>
                                    <?php esc_html_e( 'Remove', 'aura-preloader' ); ?>
                                </button>
                            </div>
                            <?php if ( $o['logo_url'] ) : ?>
                            <div id="aura-preview-wrap">
                                <img id="aura-img-preview" src="<?php echo esc_url( $o['logo_url'] ); ?>" alt="">
                            </div>
                            <?php else : ?>
                            <div id="aura-preview-wrap" style="display:none">
                                <img id="aura-img-preview" src="" alt="">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="aura-field aura-two-col">
                            <div>
                                <label for="aura-logo-width"><?php esc_html_e( 'Logo width (px)', 'aura-preloader' ); ?></label>
                                <input type="number" id="aura-logo-width" name="<?php echo AURA_OPT; ?>[logo_width]"
                                    value="<?php echo esc_attr( $o['logo_width'] ); ?>" min="20" max="300" class="small-text"> px
                            </div>
                        </div>
                    </div>

                    <div class="aura-card">
                        <h2><?php esc_html_e( 'Backdrop blur', 'aura-preloader' ); ?></h2>

                        <div class="aura-field aura-two-col">
                            <div>
                                <label for="aura-overlay-color"><?php esc_html_e( 'Overlay tint', 'aura-preloader' ); ?></label>
                                <input type="color" id="aura-overlay-color" name="<?php echo AURA_OPT; ?>[overlay_color]"
                                    value="<?php echo esc_attr( $o['overlay_color'] ); ?>">
                                <input type="text" class="aura-hex-text" data-for="aura-overlay-color"
                                    value="<?php echo esc_attr( $o['overlay_color'] ); ?>" maxlength="7">
                            </div>
                            <div>
                                <label for="aura-accent"><?php esc_html_e( 'Accent (ring & bar)', 'aura-preloader' ); ?></label>
                                <input type="color" id="aura-accent" name="<?php echo AURA_OPT; ?>[accent_color]"
                                    value="<?php echo esc_attr( $o['accent_color'] ); ?>">
                                <input type="text" class="aura-hex-text" data-for="aura-accent"
                                    value="<?php echo esc_attr( $o['accent_color'] ); ?>" maxlength="7">
                            </div>
                        </div>

                        <div class="aura-field">
                            <label for="aura-overlay-opacity">
                                <?php esc_html_e( 'Overlay opacity', 'aura-preloader' ); ?>
                                <span id="aura-opacity-val" class="aura-range-val"><?php echo esc_html( $o['overlay_opacity'] ); ?>%</span>
                            </label>
                            <input type="range" id="aura-overlay-opacity" name="<?php echo AURA_OPT; ?>[overlay_opacity]"
                                min="0" max="100" step="1" value="<?php echo esc_attr( $o['overlay_opacity'] ); ?>">
                        </div>

                        <div class="aura-field">
                            <label for="aura-blur">
                                <?php esc_html_e( 'Blur strength', 'aura-preloader' ); ?>
                                <span id="aura-blur-val" class="aura-range-val"><?php echo esc_html( $o['blur_strength'] ); ?>px</span>
                            </label>
                            <input type="range" id="aura-blur" name="<?php echo AURA_OPT; ?>[blur_strength]"
                                min="0" max="40" step="1" value="<?php echo esc_attr( $o['blur_strength'] ); ?>">
                        </div>
                    </div>

                    <div class="aura-card">
                        <h2><?php esc_html_e( 'Preloader Style Presets', 'aura-preloader' ); ?></h2>
                        
                        <div class="aura-field">
                            <label for="aura-spinner-type"><?php esc_html_e( 'Animation Style', 'aura-preloader' ); ?></label>
                            <select id="aura-spinner-type" name="<?php echo AURA_OPT; ?>[spinner_type]" class="regular-text">
                                <?php 
                                foreach ( aura_get_presets() as $key => $preset ) {
                                    echo sprintf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $key ),
                                        selected( $o['spinner_type'] ?? '', $key, false ),
                                        esc_html( $preset['label'] )
                                    );
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Choose your preferred preloader animation style', 'aura-preloader' ); ?></p>
                        </div>

                        <div class="aura-field">
                            <label for="aura-animation-speed"><?php esc_html_e( 'Animation Speed', 'aura-preloader' ); ?></label>
                            <select id="aura-animation-speed" name="<?php echo AURA_OPT; ?>[animation_speed]" class="regular-text">
                                <?php 
                                foreach ( aura_get_animation_speeds() as $key => $speed ) {
                                    echo sprintf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $key ),
                                        selected( $o['animation_speed'] ?? '', $key, false ),
                                        esc_html( $speed['label'] )
                                    );
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Control the animation speed of the preloader', 'aura-preloader' ); ?></p>
                        </div>

                        <div class="aura-field">
                            <label for="aura-progress-style"><?php esc_html_e( 'Progress Bar Style', 'aura-preloader' ); ?></label>
                            <select id="aura-progress-style" name="<?php echo AURA_OPT; ?>[progress_bar_style]" class="regular-text">
                                <?php 
                                foreach ( aura_get_progress_bar_styles() as $key => $style ) {
                                    echo sprintf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $key ),
                                        selected( $o['progress_bar_style'] ?? '', $key, false ),
                                        esc_html( $style['label'] )
                                    );
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Select how the progress bar should be displayed', 'aura-preloader' ); ?></p>
                        </div>

                        <div class="aura-field aura-checks">
                            <label>
                                <input type="checkbox" name="<?php echo AURA_OPT; ?>[show_progress]" value="1"
                                    <?php checked( $o['show_progress'] ?? true ); ?>>
                                <?php esc_html_e( 'Show progress animation', 'aura-preloader' ); ?>
                            </label>
                        </div>
                    </div>

                    <div class="aura-card">
                        <h2><?php esc_html_e( 'Elements', 'aura-preloader' ); ?></h2>
                        <div class="aura-field aura-checks">
                            <label>
                                <input type="checkbox" name="<?php echo AURA_OPT; ?>[show_ring]" value="1"
                                    <?php checked( $o['show_ring'] ); ?>>
                                <?php esc_html_e( 'Show spinner ring', 'aura-preloader' ); ?>
                            </label>
                            <label>
                                <input type="checkbox" name="<?php echo AURA_OPT; ?>[show_bar]" value="1"
                                    <?php checked( $o['show_bar'] ); ?>>
                                <?php esc_html_e( 'Show progress bar', 'aura-preloader' ); ?>
                            </label>
                            <label>
                                <input type="checkbox" name="<?php echo AURA_OPT; ?>[enable_mobile]" value="1"
                                    <?php checked( $o['enable_mobile'] ); ?>>
                                <?php esc_html_e( 'Show on mobile devices', 'aura-preloader' ); ?>
                            </label>
                        </div>
                    </div>

                    <div class="aura-card">
                        <h2><?php esc_html_e( 'Timing', 'aura-preloader' ); ?></h2>
                        <div class="aura-field aura-two-col">
                            <div>
                                <label for="aura-fade"><?php esc_html_e( 'Fade-out duration (ms)', 'aura-preloader' ); ?></label>
                                <input type="number" id="aura-fade" name="<?php echo AURA_OPT; ?>[fade_duration]"
                                    value="<?php echo esc_attr( $o['fade_duration'] ); ?>" min="0" max="3000" class="small-text"> ms
                            </div>
                            <div>
                                <label for="aura-min"><?php esc_html_e( 'Minimum display time (ms)', 'aura-preloader' ); ?></label>
                                <input type="number" id="aura-min" name="<?php echo AURA_OPT; ?>[min_display]"
                                    value="<?php echo esc_attr( $o['min_display'] ); ?>" min="0" max="5000" class="small-text"> ms
                                <p class="description"><?php esc_html_e( 'Preloader stays visible for at least this long even if the page loads faster.', 'aura-preloader' ); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php submit_button( __( 'Save settings', 'aura-preloader' ) ); ?>
                </form>
            </div>

            <!-- ── Live preview ── -->
            <div class="aura-preview-col">
                <div class="aura-card aura-sticky">
                    <h2><?php esc_html_e( 'Preview', 'aura-preloader' ); ?></h2>
                    <div id="aura-live-preview">
                        <div class="aura-prev-bg-sim"></div>
                        <div class="aura-prev-overlay" id="aurav-overlay"></div>
                        <div class="aura-prev-inner">
                            <div class="aura-prev-wrap" id="aurav-wrap">
                                <div class="aura-prev-ring" id="aurav-ring"></div>
                                <?php if ( $o['logo_url'] ) : ?>
                                <img id="aurav-img" src="<?php echo esc_url( $o['logo_url'] ); ?>"
                                    style="width:<?php echo esc_attr( $o['logo_width'] ); ?>px;">
                                <?php else : ?>
                                <div id="aurav-placeholder" class="aura-prev-placeholder">LOGO</div>
                                <img id="aurav-img" src="" style="display:none;width:<?php echo esc_attr( $o['logo_width'] ); ?>px;">
                                <?php endif; ?>
                            </div>
                            <span id="aurav-spinner-label" class="aurav-style-label">
                                <?php echo esc_html( aura_get_presets()[ $o['spinner_type'] ]['label'] ?? 'Spinner Ring' ); ?>
                            </span>
                            <div class="aura-prev-bar" id="aurav-bar">
                                <div class="aura-prev-fill" id="aurav-fill"
                                    style="background:<?php echo esc_attr( $o['accent_color'] ); ?>"></div>
                            </div>
                            <span id="aurav-bar-style-label" class="aurav-style-label">
                                <?php echo esc_html( aura_get_progress_bar_styles()[ $o['progress_bar_style'] ]['label'] ?? 'Linear Bar' ); ?>
                            </span>
                        </div>
                    </div>
                    <p class="description" style="text-align:center;margin-top:.5rem">
                        <?php esc_html_e( 'Updates as you change settings', 'aura-preloader' ); ?>
                    </p>
                </div>

                <div class="aura-card aura-author-card">
                    <p class="aura-author-name">Irfan Bhat</p>
                    <p class="aura-author-meta">
                        <a href="https://irfanbhat.com" target="_blank" rel="noopener">irfanbhat.com</a>
                        &nbsp;·&nbsp;
                        <a href="mailto:info@irfanbhat.com">info@irfanbhat.com</a>
                    </p>
                    <p class="aura-author-ver">Aura Preloader v1.3.1</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
