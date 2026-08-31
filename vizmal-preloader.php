<?php
/**
 * Plugin Name: Vizmal Preloader
 * Plugin URI:  https://wordpress.org/plugins/vizmal-preloader/
 * Description: A customisable full-screen backdrop-blur preloader with your logo, spinner, and progress bar.
 * Version:     1.3.2
 * Author:      Irfan Bhat
 * Author URI:  https://irfanbhat.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vizmal-preloader
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'VIZMAL_VERSION',     '1.3.0' );
define( 'VIZMAL_DIR',         plugin_dir_path( __FILE__ ) );
define( 'VIZMAL_URL',         plugin_dir_url( __FILE__ ) );
define( 'VIZMAL_OPT',         'vizmal_preloader_options' );
define( 'VIZMAL_GITHUB_REPO', 'irfanbhat/vizmal-preloader' );

/* ---------------------------------------------------------------
   Includes
--------------------------------------------------------------- */
require_once VIZMAL_DIR . 'includes/presets.php';

/* ---------------------------------------------------------------
   Default options
--------------------------------------------------------------- */
function vizmal_defaults() {
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

function vizmal_options() {
    $saved = get_option( VIZMAL_OPT );
    if ( false === $saved ) {
        // Migration fallback for previous option keys
        $saved = get_option( 'aura_preloader_options', get_option( 'logo_preloader_options', [] ) );
    }
    return wp_parse_args( $saved, vizmal_defaults() );
}

/* ---------------------------------------------------------------
   Activation — save defaults
--------------------------------------------------------------- */
register_activation_hook( __FILE__, function() {
    if ( ! get_option( VIZMAL_OPT ) ) {
        update_option( VIZMAL_OPT, vizmal_options() );
    }
});

/* ---------------------------------------------------------------
   Front-end: inject preloader HTML right after <body>
--------------------------------------------------------------- */
add_action( 'wp_body_open', 'vizmal_render_preloader' );
function vizmal_render_preloader() {
    $o        = vizmal_options();

    // Skip on mobile if disabled
    if ( ! $o['enable_mobile'] && wp_is_mobile() ) return;
    $logo_src = esc_url( $o['logo_url'] );
    $logo_w   = absint( $o['logo_width'] );
    $show_img = $logo_src ? "<img src=\"{$logo_src}\" alt=\"\" style=\"width:{$logo_w}px;\">" : '';
    ?>
    <div id="vizmal-preloader" role="status" aria-label="<?php esc_attr_e( 'Loading', 'vizmal-preloader' ); ?>">
        <div class="vizmal-inner">
            <?php if ( $o['show_ring'] ) : ?>
            <div class="vizmal-wrap">
                <div class="vizmal-ring"></div>
                <?php echo $show_img; ?>
            </div>
            <?php elseif ( $show_img ) : ?>
            <div class="vizmal-wrap"><?php echo $show_img; ?></div>
            <?php endif; ?>

            <?php if ( $o['show_bar'] ) : ?>
            <div class="vizmal-bar"><div class="vizmal-fill"></div></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* ---------------------------------------------------------------
   Front-end: enqueue CSS + JS (with inline config)
--------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'vizmal_enqueue_frontend' );
function vizmal_enqueue_frontend() {
    $o = vizmal_options();

    // Skip on mobile if disabled
    if ( ! $o['enable_mobile'] && wp_is_mobile() ) return;

    wp_enqueue_style(
        'vizmal-preloader',
        VIZMAL_URL . 'css/preloader.css',
        [],
        VIZMAL_VERSION
    );

    // Enqueue animation styles based on selected speed
    $animation_speed = sanitize_text_field( $o['animation_speed'] ?? 'normal' );
    wp_enqueue_style(
        'vizmal-preloader-animation-' . $animation_speed,
        VIZMAL_URL . 'css/animations/' . $animation_speed . '.css',
        [ 'vizmal-preloader' ],
        VIZMAL_VERSION
    );

    // Enqueue progress bar styles if enabled
    if ( ! empty( $o['show_progress'] ) ) {
        wp_enqueue_style(
            'vizmal-preloader-progress-bars',
            VIZMAL_URL . 'css/progress-bars.css',
            [ 'vizmal-preloader' ],
            VIZMAL_VERSION
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

    $inline  = "#vizmal-preloader{--vizmal-overlay:{$rgba};--vizmal-blur:{$blur}px;--vizmal-accent:{$accent};--vizmal-logo-w:{$logo_w}px;}";
    wp_add_inline_style( 'vizmal-preloader', $inline );

    wp_enqueue_script(
        'vizmal-preloader',
        VIZMAL_URL . 'js/preloader.js',
        [],
        VIZMAL_VERSION,
        true   // footer
    );

    // Pass config to JS
    wp_localize_script( 'vizmal-preloader', 'vizmalConfig', [
        'fade'              => absint( $o['fade_duration'] ),
        'minDisplay'        => absint( $o['min_display'] ),
        'spinnerType'       => sanitize_text_field( $o['spinner_type'] ?? 'spinner' ),
        'animationSpeed'    => $animation_speed,
        'progressBarStyle'  => sanitize_text_field( $o['progress_bar_style'] ?? 'linear' ),
        'showProgress'      => ! empty( $o['show_progress'] ),
        'svgUrl'            => VIZMAL_URL . 'assets/svgs/',
    ]);
}

/* ---------------------------------------------------------------
   Admin: settings page
--------------------------------------------------------------- */
add_action( 'admin_menu', function() {
    add_options_page(
        __( 'Vizmal Preloader', 'vizmal-preloader' ),
        __( 'Vizmal Preloader', 'vizmal-preloader' ),
        'manage_options',
        'vizmal-preloader',
        'vizmal_settings_page'
    );
});

add_action( 'admin_init', 'vizmal_register_settings' );
function vizmal_register_settings() {
    register_setting( 'vizmal_preloader_group', VIZMAL_OPT, [
        'sanitize_callback' => 'vizmal_sanitize_options',
    ]);
}

function vizmal_sanitize_options( $input ) {
    $d   = vizmal_defaults();
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
    $spinners = array_keys( vizmal_get_presets() );
    $out['spinner_type']        = in_array( $input['spinner_type'] ?? '', $spinners, true ) ? sanitize_text_field( $input['spinner_type'] ) : $d['spinner_type'];
    
    $speeds = array_keys( vizmal_get_animation_speeds() );
    $out['animation_speed']     = in_array( $input['animation_speed'] ?? '', $speeds, true ) ? sanitize_text_field( $input['animation_speed'] ) : $d['animation_speed'];
    
    $bars = array_keys( vizmal_get_progress_bar_styles() );
    $out['progress_bar_style']  = in_array( $input['progress_bar_style'] ?? '', $bars, true ) ? sanitize_text_field( $input['progress_bar_style'] ) : $d['progress_bar_style'];
    
    $out['show_progress']       = ! empty( $input['show_progress'] );
    return $out;
}

/* ---------------------------------------------------------------
   Admin: enqueue media uploader
--------------------------------------------------------------- */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( $hook !== 'settings_page_vizmal-preloader' ) return;
    wp_enqueue_media();
    wp_enqueue_style( 'vizmal-admin', VIZMAL_URL . 'admin/admin.css', [], VIZMAL_VERSION );
    wp_enqueue_script( 'vizmal-admin', VIZMAL_URL . 'admin/admin.js', [ 'jquery' ], VIZMAL_VERSION, true );
});

/* ---------------------------------------------------------------
   Settings page HTML
--------------------------------------------------------------- */
function vizmal_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $o = vizmal_options();
    ?>
    <div class="wrap vizmal-wrap-admin">
        <h1><?php esc_html_e( 'Vizmal Preloader Settings', 'vizmal-preloader' ); ?></h1>

        <div class="vizmal-admin-layout">
            <!-- ── Form ── -->
            <div class="vizmal-form-col">
                <form method="post" action="options.php">
                    <?php settings_fields( 'vizmal_preloader_group' ); ?>

                    <div class="vizmal-card">
                        <h2><?php esc_html_e( 'Logo', 'vizmal-preloader' ); ?></h2>

                        <div class="vizmal-field">
                            <label><?php esc_html_e( 'Logo image', 'vizmal-preloader' ); ?></label>
                            <div class="vizmal-media-row">
                                <input type="text" id="vizmal-logo-url" name="<?php echo VIZMAL_OPT; ?>[logo_url]"
                                    value="<?php echo esc_attr( $o['logo_url'] ); ?>" class="regular-text">
                                <button type="button" id="vizmal-upload-btn" class="button">
                                    <?php esc_html_e( 'Choose image', 'vizmal-preloader' ); ?>
                                </button>
                                <button type="button" id="vizmal-remove-btn" class="button vizmal-remove"
                                    <?php echo $o['logo_url'] ? '' : 'style="display:none"'; ?>>
                                    <?php esc_html_e( 'Remove', 'vizmal-preloader' ); ?>
                                </button>
                            </div>
                            <?php if ( $o['logo_url'] ) : ?>
                            <div id="vizmal-preview-wrap">
                                <img id="vizmal-img-preview" src="<?php echo esc_url( $o['logo_url'] ); ?>" alt="">
                            </div>
                            <?php else : ?>
                            <div id="vizmal-preview-wrap" style="display:none">
                                <img id="vizmal-img-preview" src="" alt="">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="vizmal-field vizmal-two-col">
                            <div>
                                <label for="vizmal-logo-width"><?php esc_html_e( 'Logo width (px)', 'vizmal-preloader' ); ?></label>
                                <input type="number" id="vizmal-logo-width" name="<?php echo VIZMAL_OPT; ?>[logo_width]"
                                    value="<?php echo esc_attr( $o['logo_width'] ); ?>" min="20" max="300" class="small-text"> px
                            </div>
                        </div>
                    </div>

                    <div class="vizmal-card">
                        <h2><?php esc_html_e( 'Backdrop blur', 'vizmal-preloader' ); ?></h2>

                        <div class="vizmal-field vizmal-two-col">
                            <div>
                                <label for="vizmal-overlay-color"><?php esc_html_e( 'Overlay tint', 'vizmal-preloader' ); ?></label>
                                <input type="color" id="vizmal-overlay-color" name="<?php echo VIZMAL_OPT; ?>[overlay_color]"
                                    value="<?php echo esc_attr( $o['overlay_color'] ); ?>">
                                <input type="text" class="vizmal-hex-text" data-for="vizmal-overlay-color"
                                    value="<?php echo esc_attr( $o['overlay_color'] ); ?>" maxlength="7">
                            </div>
                            <div>
                                <label for="vizmal-accent"><?php esc_html_e( 'Accent (ring & bar)', 'vizmal-preloader' ); ?></label>
                                <input type="color" id="vizmal-accent" name="<?php echo VIZMAL_OPT; ?>[accent_color]"
                                    value="<?php echo esc_attr( $o['accent_color'] ); ?>">
                                <input type="text" class="vizmal-hex-text" data-for="vizmal-accent"
                                    value="<?php echo esc_attr( $o['accent_color'] ); ?>" maxlength="7">
                            </div>
                        </div>

                        <div class="vizmal-field">
                            <label for="vizmal-overlay-opacity">
                                <?php esc_html_e( 'Overlay opacity', 'vizmal-preloader' ); ?>
                                <span id="vizmal-opacity-val" class="vizmal-range-val"><?php echo esc_html( $o['overlay_opacity'] ); ?>%</span>
                            </label>
                            <input type="range" id="vizmal-overlay-opacity" name="<?php echo VIZMAL_OPT; ?>[overlay_opacity]"
                                min="0" max="100" step="1" value="<?php echo esc_attr( $o['overlay_opacity'] ); ?>">
                        </div>

                        <div class="vizmal-field">
                            <label for="vizmal-blur">
                                <?php esc_html_e( 'Blur strength', 'vizmal-preloader' ); ?>
                                <span id="vizmal-blur-val" class="vizmal-range-val"><?php echo esc_html( $o['blur_strength'] ); ?>px</span>
                            </label>
                            <input type="range" id="vizmal-blur" name="<?php echo VIZMAL_OPT; ?>[blur_strength]"
                                min="0" max="40" step="1" value="<?php echo esc_attr( $o['blur_strength'] ); ?>">
                        </div>
                    </div>

                    <div class="vizmal-card">
                        <h2><?php esc_html_e( 'Preloader Style Presets', 'vizmal-preloader' ); ?></h2>
                        
                        <div class="vizmal-field">
                            <label for="vizmal-spinner-type"><?php esc_html_e( 'Animation Style', 'vizmal-preloader' ); ?></label>
                            <select id="vizmal-spinner-type" name="<?php echo VIZMAL_OPT; ?>[spinner_type]" class="regular-text">
                                <?php 
                                foreach ( vizmal_get_presets() as $key => $preset ) {
                                    echo sprintf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $key ),
                                        selected( $o['spinner_type'] ?? '', $key, false ),
                                        esc_html( $preset['label'] )
                                    );
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Choose your preferred preloader animation style', 'vizmal-preloader' ); ?></p>
                        </div>

                        <div class="vizmal-field">
                            <label for="vizmal-animation-speed"><?php esc_html_e( 'Animation Speed', 'vizmal-preloader' ); ?></label>
                            <select id="vizmal-animation-speed" name="<?php echo VIZMAL_OPT; ?>[animation_speed]" class="regular-text">
                                <?php 
                                foreach ( vizmal_get_animation_speeds() as $key => $speed ) {
                                    echo sprintf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $key ),
                                        selected( $o['animation_speed'] ?? '', $key, false ),
                                        esc_html( $speed['label'] )
                                    );
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Control the animation speed of the preloader', 'vizmal-preloader' ); ?></p>
                        </div>

                        <div class="vizmal-field">
                            <label for="vizmal-progress-style"><?php esc_html_e( 'Progress Bar Style', 'vizmal-preloader' ); ?></label>
                            <select id="vizmal-progress-style" name="<?php echo VIZMAL_OPT; ?>[progress_bar_style]" class="regular-text">
                                <?php 
                                foreach ( vizmal_get_progress_bar_styles() as $key => $style ) {
                                    echo sprintf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $key ),
                                        selected( $o['progress_bar_style'] ?? '', $key, false ),
                                        esc_html( $style['label'] )
                                    );
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Select how the progress bar should be displayed', 'vizmal-preloader' ); ?></p>
                        </div>

                        <div class="vizmal-field vizmal-checks">
                            <label>
                                <input type="checkbox" name="<?php echo VIZMAL_OPT; ?>[show_progress]" value="1"
                                    <?php checked( $o['show_progress'] ?? true ); ?>>
                                <?php esc_html_e( 'Show progress animation', 'vizmal-preloader' ); ?>
                            </label>
                        </div>
                    </div>

                    <div class="vizmal-card">
                        <h2><?php esc_html_e( 'Elements', 'vizmal-preloader' ); ?></h2>
                        <div class="vizmal-field vizmal-checks">
                            <label>
                                <input type="checkbox" name="<?php echo VIZMAL_OPT; ?>[show_ring]" value="1"
                                    <?php checked( $o['show_ring'] ); ?>>
                                <?php esc_html_e( 'Show spinner ring', 'vizmal-preloader' ); ?>
                            </label>
                            <label>
                                <input type="checkbox" name="<?php echo VIZMAL_OPT; ?>[show_bar]" value="1"
                                    <?php checked( $o['show_bar'] ); ?>>
                                <?php esc_html_e( 'Show progress bar', 'vizmal-preloader' ); ?>
                            </label>
                            <label>
                                <input type="checkbox" name="<?php echo VIZMAL_OPT; ?>[enable_mobile]" value="1"
                                    <?php checked( $o['enable_mobile'] ); ?>>
                                <?php esc_html_e( 'Show on mobile devices', 'vizmal-preloader' ); ?>
                            </label>
                        </div>
                    </div>

                    <div class="vizmal-card">
                        <h2><?php esc_html_e( 'Timing', 'vizmal-preloader' ); ?></h2>
                        <div class="vizmal-field vizmal-two-col">
                            <div>
                                <label for="vizmal-fade"><?php esc_html_e( 'Fade-out duration (ms)', 'vizmal-preloader' ); ?></label>
                                <input type="number" id="vizmal-fade" name="<?php echo VIZMAL_OPT; ?>[fade_duration]"
                                    value="<?php echo esc_attr( $o['fade_duration'] ); ?>" min="0" max="3000" class="small-text"> ms
                            </div>
                            <div>
                                <label for="vizmal-min"><?php esc_html_e( 'Minimum display time (ms)', 'vizmal-preloader' ); ?></label>
                                <input type="number" id="vizmal-min" name="<?php echo VIZMAL_OPT; ?>[min_display]"
                                    value="<?php echo esc_attr( $o['min_display'] ); ?>" min="0" max="5000" class="small-text"> ms
                                <p class="description"><?php esc_html_e( 'Preloader stays visible for at least this long even if the page loads faster.', 'vizmal-preloader' ); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php submit_button( __( 'Save settings', 'vizmal-preloader' ) ); ?>
                </form>
            </div>

            <!-- ── Live preview ── -->
            <div class="vizmal-preview-col">
                <div class="vizmal-card vizmal-sticky">
                    <h2><?php esc_html_e( 'Preview', 'vizmal-preloader' ); ?></h2>
                    <div id="vizmal-live-preview">
                        <div class="vizmal-prev-bg-sim"></div>
                        <div class="vizmal-prev-overlay" id="vizmalv-overlay"></div>
                        <div class="vizmal-prev-inner">
                            <div class="vizmal-prev-wrap" id="vizmalv-wrap">
                                <div class="vizmal-prev-ring" id="vizmalv-ring"></div>
                                <?php if ( $o['logo_url'] ) : ?>
                                <img id="vizmalv-img" src="<?php echo esc_url( $o['logo_url'] ); ?>"
                                    style="width:<?php echo esc_attr( $o['logo_width'] ); ?>px;">
                                <?php else : ?>
                                <div id="vizmalv-placeholder" class="vizmal-prev-placeholder">LOGO</div>
                                <img id="vizmalv-img" src="" style="display:none;width:<?php echo esc_attr( $o['logo_width'] ); ?>px;">
                                <?php endif; ?>
                            </div>
                            <span id="vizmalv-spinner-label" class="vizmalv-style-label">
                                <?php echo esc_html( vizmal_get_presets()[ $o['spinner_type'] ]['label'] ?? 'Spinner Ring' ); ?>
                            </span>
                            <div class="vizmal-prev-bar" id="vizmalv-bar">
                                <div class="vizmal-prev-fill" id="vizmalv-fill"
                                    style="background:<?php echo esc_attr( $o['accent_color'] ); ?>"></div>
                            </div>
                            <span id="vizmalv-bar-style-label" class="vizmalv-style-label">
                                <?php echo esc_html( vizmal_get_progress_bar_styles()[ $o['progress_bar_style'] ]['label'] ?? 'Linear Bar' ); ?>
                            </span>
                        </div>
                    </div>
                    <p class="description" style="text-align:center;margin-top:.5rem">
                        <?php esc_html_e( 'Updates as you change settings', 'vizmal-preloader' ); ?>
                    </p>
                </div>

                <div class="vizmal-card vizmal-author-card">
                    <p class="vizmal-author-name">Irfan Bhat</p>
                    <p class="vizmal-author-meta">
                        <a href="https://irfanbhat.com" target="_blank" rel="noopener">irfanbhat.com</a>
                        &nbsp;·&nbsp;
                        <a href="mailto:info@irfanbhat.com">info@irfanbhat.com</a>
                    </p>
                    <p class="vizmal-author-ver">Vizmal Preloader v1.3.2</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
