<?php
/**
 * Aura Preloader Presets Configuration
 * 
 * Define preset styles for preloader animations, SVG types, and progress bars
 */

if ( ! function_exists( 'aura_get_presets' ) ) {
    
    /**
     * Get all available preloader presets
     */
    function aura_get_presets() {
        return [
            'spinner' => [
                'label'      => __( 'Spinner Ring', 'aura-preloader' ),
                'svg'        => 'spinner.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( 'Classic rotating ring with smooth animation', 'aura-preloader' ),
            ],
            'dots' => [
                'label'      => __( 'Bouncing Dots', 'aura-preloader' ),
                'svg'        => 'dots.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '3 dots bouncing up and down', 'aura-preloader' ),
            ],
            'pulse' => [
                'label'      => __( 'Pulse', 'aura-preloader' ),
                'svg'        => 'pulse.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( 'Center dot with expanding pulse rings', 'aura-preloader' ),
            ],
            'bars' => [
                'label'      => __( 'Bars', 'aura-preloader' ),
                'svg'        => 'bars.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '5 bars with wave animation', 'aura-preloader' ),
            ],
            'spinner-dots' => [
                'label'      => __( 'Spinner Dots', 'aura-preloader' ),
                'svg'        => 'spinner-dots.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '8 dots rotating in a circle', 'aura-preloader' ),
            ],
        ];
    }
    
    /**
     * Get animation speed presets
     */
    function aura_get_animation_speeds() {
        return [
            'slow' => [
                'label'       => __( 'Slow', 'aura-preloader' ),
                'multiplier'  => 1.8,
                'description' => __( 'Relaxed animation pace', 'aura-preloader' ),
            ],
            'normal' => [
                'label'       => __( 'Normal', 'aura-preloader' ),
                'multiplier'  => 1,
                'description' => __( 'Standard animation speed', 'aura-preloader' ),
            ],
            'fast' => [
                'label'       => __( 'Fast', 'aura-preloader' ),
                'multiplier'  => 0.67,
                'description' => __( 'Quick animation pace', 'aura-preloader' ),
            ],
        ];
    }
    
    /**
     * Get progress bar style presets
     */
    function aura_get_progress_bar_styles() {
        return [
            'linear' => [
                'label'       => __( 'Linear Bar', 'aura-preloader' ),
                'description' => __( 'Horizontal progress bar at bottom', 'aura-preloader' ),
                'css_class'   => 'aura-progress-linear',
            ],
            'circular' => [
                'label'       => __( 'Circular Ring', 'aura-preloader' ),
                'description' => __( 'Circular progress ring around spinner', 'aura-preloader' ),
                'css_class'   => 'aura-progress-circular',
            ],
        ];
    }
    
    /**
     * Get default preset configuration
     */
    function aura_get_default_presets_config() {
        return [
            'spinner_type'      => 'spinner',
            'animation_speed'   => 'normal',
            'progress_bar_style' => 'linear',
            'show_progress'     => true,
        ];
    }
}

