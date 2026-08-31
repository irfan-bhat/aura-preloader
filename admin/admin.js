/* Vizmal Preloader v1.3.0 — admin JS — Irfan Bhat */
jQuery(function ($) {

    /* ── Media uploader ── */
    var frame;

    $('#vizmal-upload-btn').on('click', function () {
        if (frame) { frame.open(); return; }

        frame = wp.media({
            title:    'Choose Logo',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });

        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            $('#vizmal-logo-url').val(att.url).trigger('input');
            $('#vizmal-img-preview').attr('src', att.url);
            $('#vizmal-preview-wrap').show();
            $('#vizmal-remove-btn').show();
            syncPreviewLogo(att.url);
        });

        frame.open();
    });

    $('#vizmal-remove-btn').on('click', function () {
        $('#vizmal-logo-url').val('').trigger('input');
        $('#vizmal-img-preview').attr('src', '');
        $('#vizmal-preview-wrap').hide();
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
        var hex     = $('#vizmal-overlay-color').val();
        var opacity = parseInt($('#vizmal-overlay-opacity').val(), 10) / 100;
        var blur    = parseInt($('#vizmal-blur').val(), 10);
        var rgb     = hexToRgb(hex);
        var rgba    = 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + opacity.toFixed(2) + ')';
        var bFilter = 'blur(' + blur + 'px) saturate(1.4)';
        $('#vizmalv-overlay').css({
            'background':            rgba,
            'backdrop-filter':       bFilter,
            '-webkit-backdrop-filter': bFilter,
        });
    }

    /* ── Overlay color ── */
    $('#vizmal-overlay-color').on('input', function () {
        var hex = $(this).val();
        $(this).siblings('.vizmal-hex-text').val(hex);
        rebuildOverlay();
    });

    /* ── Accent color ── */
    $('#vizmal-accent').on('input', function () {
        var hex = $(this).val();
        $(this).siblings('.vizmal-hex-text').val(hex);
        $('#vizmalv-ring').css('border-top-color', hex);
        $('#vizmalv-fill').css('background', hex);
    });

    /* ── Hex text inputs ── */
    $('.vizmal-hex-text').on('input', function () {
        var val = $(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            var target = $(this).data('for');
            $('#' + target).val(val).trigger('input');
        }
    });

    /* ── Blur strength slider ── */
    $('#vizmal-blur').on('input', function () {
        $('#vizmal-blur-val').text($(this).val() + 'px');
        rebuildOverlay();
    });

    /* ── Overlay opacity slider ── */
    $('#vizmal-overlay-opacity').on('input', function () {
        $('#vizmal-opacity-val').text($(this).val() + '%');
        rebuildOverlay();
    });

    /* ── Logo width ── */
    $('#vizmal-logo-width').on('input', function () {
        var w = parseInt($(this).val(), 10) || 64;
        $('#vizmalv-img').css('width', w + 'px');
        $('#vizmalv-wrap').css({ width: (w + 28) + 'px', height: (w + 28) + 'px' });
    });

    /* ── Toggle ring / bar ── */
    $('input[name$="[show_ring]"]').on('change', function () {
        $('#vizmalv-ring').toggle(this.checked);
    });
    $('input[name$="[show_bar]"]').on('change', function () {
        $('#vizmalv-bar').toggle(this.checked);
    });

    /* ── Logo URL typed manually ── */
    $('#vizmal-logo-url').on('input', function () {
        syncPreviewLogo($(this).val());
    });

    function syncPreviewLogo(url) {
        if (url) {
            $('#vizmalv-img').attr('src', url).show();
            $('#vizmalv-placeholder').hide();
        } else {
            $('#vizmalv-img').hide();
            $('#vizmalv-placeholder').show();
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
        var ring = $('#vizmalv-ring');
        // Remove all previous type classes
        ring.removeClass(function (i, cls) {
            return (cls.match(/(^|\s)vizmalv-type-\S+/g) || []).join(' ');
        });
        ring.addClass('vizmalv-type-' + type);
        // Update the preview label if one exists
        $('#vizmalv-spinner-label').text(spinnerLabels[type] || type);
    }

    $('#vizmal-spinner-type').on('change', function () {
        applySpinnerType($(this).val());
    });

    /* ── Progress Bar Style ── */
    function applyProgressStyle(style) {
        var bar  = $('#vizmalv-bar');
        var fill = $('#vizmalv-fill');
        if (style === 'circular') {
            bar.addClass('vizmalv-circular').removeClass('vizmalv-linear');
            fill.addClass('vizmalv-circular-fill').removeClass('vizmalv-linear-fill');
        } else {
            bar.addClass('vizmalv-linear').removeClass('vizmalv-circular');
            fill.addClass('vizmalv-linear-fill').removeClass('vizmalv-circular-fill');
        }
        // Update label if present
        $('#vizmalv-bar-style-label').text(style === 'circular' ? 'Circular Ring' : 'Linear Bar');
    }

    $('#vizmal-progress-style').on('change', function () {
        applyProgressStyle($(this).val());
    });

    /* ── Initialise preview from saved values on page load ── */
    (function init() {
        applySpinnerType($('#vizmal-spinner-type').val() || 'spinner');
        applyProgressStyle($('#vizmal-progress-style').val() || 'linear');
        // Sync accent to ring/fill initial colour
        var accent = $('#vizmal-accent').val();
        if (accent) {
            $('#vizmalv-ring').css('border-top-color', accent);
            $('#vizmalv-fill').css('background', accent);
        }
        rebuildOverlay();
    })();
});
