/* Aura Preloader v1.3.0 — admin JS — Irfan Bhat */
jQuery(function ($) {

    /* ── Media uploader ── */
    var frame;

    $('#aura-upload-btn').on('click', function () {
        if (frame) { frame.open(); return; }

        frame = wp.media({
            title:    'Choose Logo',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });

        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            $('#aura-logo-url').val(att.url).trigger('input');
            $('#aura-img-preview').attr('src', att.url);
            $('#aura-preview-wrap').show();
            $('#aura-remove-btn').show();
            syncPreviewLogo(att.url);
        });

        frame.open();
    });

    $('#aura-remove-btn').on('click', function () {
        $('#aura-logo-url').val('').trigger('input');
        $('#aura-img-preview').attr('src', '');
        $('#aura-preview-wrap').hide();
        $(this).hide();
        syncPreviewLogo('');
    });

    /* ── Helper: hex → r,g,b ── */
    function hexToRgb(hex) {
        var r = parseInt(hex.slice(1,3),16);
        var g = parseInt(hex.slice(3,5),16);
        var b = parseInt(hex.slice(5,7),16);
        return [r, g, b];
    }

    function rebuildOverlay() {
        var hex     = $('#aura-overlay-color').val();
        var opacity = parseInt($('#aura-overlay-opacity').val(), 10) / 100;
        var blur    = parseInt($('#aura-blur').val(), 10);
        var rgb     = hexToRgb(hex);
        var rgba    = 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + opacity.toFixed(2) + ')';
        var bFilter = 'blur(' + blur + 'px) saturate(1.4)';
        $('#aurav-overlay').css({
            'background':            rgba,
            'backdrop-filter':       bFilter,
            '-webkit-backdrop-filter': bFilter,
        });
    }

    /* ── Overlay color ── */
    $('#aura-overlay-color').on('input', function () {
        var hex = $(this).val();
        $(this).siblings('.aura-hex-text').val(hex);
        rebuildOverlay();
    });

    /* ── Accent color ── */
    $('#aura-accent').on('input', function () {
        var hex = $(this).val();
        $(this).siblings('.aura-hex-text').val(hex);
        $('#aurav-ring').css('border-top-color', hex);
        $('#aurav-fill').css('background', hex);
    });

    /* ── Hex text inputs ── */
    $('.aura-hex-text').on('input', function () {
        var val = $(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            var target = $(this).data('for');
            $('#' + target).val(val).trigger('input');
        }
    });

    /* ── Blur strength slider ── */
    $('#aura-blur').on('input', function () {
        $('#aura-blur-val').text($(this).val() + 'px');
        rebuildOverlay();
    });

    /* ── Overlay opacity slider ── */
    $('#aura-overlay-opacity').on('input', function () {
        $('#aura-opacity-val').text($(this).val() + '%');
        rebuildOverlay();
    });

    /* ── Logo width ── */
    $('#aura-logo-width').on('input', function () {
        var w = parseInt($(this).val(), 10) || 64;
        $('#aurav-img').css('width', w + 'px');
        $('#aurav-wrap').css({ width: (w + 28) + 'px', height: (w + 28) + 'px' });
    });

    /* ── Toggle ring / bar ── */
    $('input[name$="[show_ring]"]').on('change', function () {
        $('#aurav-ring').toggle(this.checked);
    });
    $('input[name$="[show_bar]"]').on('change', function () {
        $('#aurav-bar').toggle(this.checked);
    });

    /* ── Logo URL typed manually ── */
    $('#aura-logo-url').on('input', function () {
        syncPreviewLogo($(this).val());
    });

    function syncPreviewLogo(url) {
        if (url) {
            $('#aurav-img').attr('src', url).show();
            $('#aurav-placeholder').hide();
        } else {
            $('#aurav-img').hide();
            $('#aurav-placeholder').show();
        }
    }

    /* ── Animation Style (spinner type) ── */
    var spinnerLabels = {
        'spinner'      : 'Spinner Ring',
        'dots'         : 'Bouncing Dots',
        'pulse'        : 'Pulse',
        'bars'         : 'Bars',
        'spinner-dots' : 'Spinner Dots'
    };

    function applySpinnerType(type) {
        var ring = $('#aurav-ring');
        // Remove all previous type classes
        ring.removeClass(function (i, cls) {
            return (cls.match(/(^|\s)aurav-type-\S+/g) || []).join(' ');
        });
        ring.addClass('aurav-type-' + type);
        // Update the preview label if one exists
        $('#aurav-spinner-label').text(spinnerLabels[type] || type);
    }

    $('#aura-spinner-type').on('change', function () {
        applySpinnerType($(this).val());
    });

    /* ── Progress Bar Style ── */
    function applyProgressStyle(style) {
        var bar  = $('#aurav-bar');
        var fill = $('#aurav-fill');
        if (style === 'circular') {
            bar.addClass('aurav-circular').removeClass('aurav-linear');
            fill.addClass('aurav-circular-fill').removeClass('aurav-linear-fill');
        } else {
            bar.addClass('aurav-linear').removeClass('aurav-circular');
            fill.addClass('aurav-linear-fill').removeClass('aurav-circular-fill');
        }
        // Update label if present
        $('#aurav-bar-style-label').text(style === 'circular' ? 'Circular Ring' : 'Linear Bar');
    }

    $('#aura-progress-style').on('change', function () {
        applyProgressStyle($(this).val());
    });

    /* ── Initialise preview from saved values on page load ── */
    (function init() {
        applySpinnerType($('#aura-spinner-type').val() || 'spinner');
        applyProgressStyle($('#aura-progress-style').val() || 'linear');
        // Sync accent to ring/fill initial colour
        var accent = $('#aura-accent').val();
        if (accent) {
            $('#aurav-ring').css('border-top-color', accent);
            $('#aurav-fill').css('background', accent);
        }
        rebuildOverlay();
    })();
});

