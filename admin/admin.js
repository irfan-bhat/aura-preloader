/* Aura Preloader v1.2 — admin JS — Irfan Bhat */
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
});

