<?php
/**
 * Vizmal Preloader Presets Configuration
 * 
 * Define preset styles for preloader animations, SVG types, and progress bars
 */

if ( ! function_exists( 'vizmal_get_presets' ) ) {
    
    /**
     * Get all available preloader presets
     */
    function vizmal_get_presets() {
        return [
            'spinner' => [
                'label'      => __( 'Spinner Ring', 'vizmal-preloader' ),
                'svg'        => 'spinner.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( 'Classic rotating ring with smooth animation', 'vizmal-preloader' ),
            ],
            'dots' => [
                'label'      => __( 'Bouncing Dots', 'vizmal-preloader' ),
                'svg'        => 'dots.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '3 dots bouncing up and down', 'vizmal-preloader' ),
            ],
            'pulse' => [
                'label'      => __( 'Pulse', 'vizmal-preloader' ),
                'svg'        => 'pulse.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( 'Center dot with expanding pulse rings', 'vizmal-preloader' ),
            ],
            'bars' => [
                'label'      => __( 'Bars', 'vizmal-preloader' ),
                'svg'        => 'bars.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '5 bars with wave animation', 'vizmal-preloader' ),
            ],
            'spinner-dots' => [
                'label'      => __( 'Spinner Dots', 'vizmal-preloader' ),
                'svg'        => 'spinner-dots.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '8 dots rotating in a circle', 'vizmal-preloader' ),
            ],
        ];
    }
    
    /**
     * Get animation speed presets
     */
    function vizmal_get_animation_speeds() {
        return [
            'slow' => [
                'label'       => __( 'Slow', 'vizmal-preloader' ),
                'multiplier'  => 1.8,
                'description' => __( 'Relaxed animation pace', 'vizmal-preloader' ),
            ],
            'normal' => [
                'label'       => __( 'Normal', 'vizmal-preloader' ),
                'multiplier'  => 1,
                'description' => __( 'Standard animation speed', 'vizmal-preloader' ),
            ],
            'fast' => [
                'label'       => __( 'Fast', 'vizmal-preloader' ),
                'multiplier'  => 0.67,
                'description' => __( 'Quick animation pace', 'vizmal-preloader' ),
            ],
        ];
    }
    
    /**
     * Get progress bar style presets
     */
    function vizmal_get_progress_bar_styles() {
        return [
            'linear' => [
                'label'       => __( 'Linear Bar', 'vizmal-preloader' ),
                'description' => __( 'Horizontal progress bar at bottom', 'vizmal-preloader' ),
                'css_class'   => 'vizmal-progress-linear',
            ],
            'circular' => [
                'label'       => __( 'Circular Ring', 'vizmal-preloader' ),
                'description' => __( 'Circular progress ring around spinner', 'vizmal-preloader' ),
                'css_class'   => 'vizmal-progress-circular',
            ],
        ];
    }
    
    /**
     * Get default preset configuration
     */
    function vizmal_get_default_presets_config() {
        return [
            'spinner_type'      => 'spinner',
            'animation_speed'   => 'normal',
            'progress_bar_style' => 'linear',
            'show_progress'     => true,
        ];
    }
}
