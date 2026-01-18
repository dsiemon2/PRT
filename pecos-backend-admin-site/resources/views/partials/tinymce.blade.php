{{--
    TinyMCE Rich Text Editor Partial

    Usage: @include('partials.tinymce', ['selector' => '#myTextarea', 'height' => 300])

    Parameters:
    - selector: CSS selector for textarea(s) to convert (default: '.tinymce-editor')
    - height: Editor height in pixels (default: 300)
    - menubar: Show menubar (default: false)
    - plugins: Array of plugins (default: standard set)
    - toolbar: Toolbar configuration (default: standard toolbar)
    - mode: 'full', 'simple', or 'email' (default: 'full')
--}}

@php
    $selector = $selector ?? '.tinymce-editor';
    $height = $height ?? 300;
    $menubar = $menubar ?? false;
    $mode = $mode ?? 'full';

    // Define toolbar/plugin presets based on mode
    $presets = [
        'full' => [
            'plugins' => 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            'toolbar' => 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        ],
        'simple' => [
            'plugins' => 'link lists',
            'toolbar' => 'undo redo | bold italic underline | link | numlist bullist | removeformat',
        ],
        'email' => [
            'plugins' => 'anchor autolink charmap link lists table visualblocks',
            'toolbar' => 'undo redo | blocks | bold italic underline | forecolor backcolor | link | align | numlist bullist | table | removeformat | code',
        ],
    ];

    $plugins = $plugins ?? $presets[$mode]['plugins'];
    $toolbar = $toolbar ?? $presets[$mode]['toolbar'];
@endphp

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/zeznyjaqe9c56yilns9k0mck1wivl5fh6cnb14qyhrhm37zi/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '{{ $selector }}',
        height: {{ $height }},
        menubar: {{ $menubar ? 'true' : 'false' }},
        plugins: '{{ $plugins }}',
        toolbar: '{{ $toolbar }}',

        // Content styling
        content_style: `
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                font-size: 14px;
                line-height: 1.6;
                color: #333;
            }
            p { margin: 0 0 10px 0; }
            ul, ol { margin: 0 0 10px 20px; }
            table { border-collapse: collapse; width: 100%; }
            table td, table th { border: 1px solid #ddd; padding: 8px; }
            a { color: #8B4513; }
        `,

        // Image upload handling (base64 for now, can be upgraded to server upload)
        automatic_uploads: true,
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                // Convert to base64 for simple embedding
                var reader = new FileReader();
                reader.onload = function() {
                    resolve(reader.result);
                };
                reader.onerror = function() {
                    reject('Failed to read file');
                };
                reader.readAsDataURL(blobInfo.blob());
            });
        },

        // Paste handling
        paste_data_images: true,

        // Responsive behavior
        resize: true,
        autoresize_bottom_margin: 20,

        // Setup callback
        setup: function(editor) {
            // Sync content back to textarea on change
            editor.on('change', function() {
                editor.save();
            });

            // Trigger change event on textarea when content changes
            editor.on('input', function() {
                var textarea = document.querySelector(editor.targetElm.getAttribute('id') ? '#' + editor.targetElm.getAttribute('id') : '{{ $selector }}');
                if (textarea) {
                    var event = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(event);
                }
            });
        },

        // Branding
        branding: false,
        promotion: false,

        // Status bar
        statusbar: true,
        elementpath: false,

        // Link settings
        link_default_target: '_blank',
        link_assume_external_targets: true,

        // Table settings
        table_responsive_width: true,
        table_default_styles: {
            width: '100%'
        }
    });
});

// Helper function to get TinyMCE content
function getTinyMCEContent(selector) {
    var editor = tinymce.get(selector.replace('#', ''));
    return editor ? editor.getContent() : '';
}

// Helper function to set TinyMCE content
function setTinyMCEContent(selector, content) {
    var editor = tinymce.get(selector.replace('#', ''));
    if (editor) {
        editor.setContent(content || '');
    }
}

// Helper function to destroy TinyMCE instance (useful for modals)
function destroyTinyMCE(selector) {
    var editor = tinymce.get(selector.replace('#', ''));
    if (editor) {
        editor.destroy();
    }
}

// Helper to reinitialize (useful after modal opens)
function reinitTinyMCE(selector, options) {
    destroyTinyMCE(selector);
    setTimeout(function() {
        tinymce.init(Object.assign({
            selector: selector,
            height: 300,
            menubar: false,
            plugins: '{{ $plugins }}',
            toolbar: '{{ $toolbar }}',
            branding: false,
            promotion: false
        }, options || {}));
    }, 100);
}
</script>

<style>
/* TinyMCE container styling */
.tox-tinymce {
    border-radius: 0.375rem !important;
    border-color: #dee2e6 !important;
}

.tox-tinymce:focus-within {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Modal TinyMCE fixes */
.modal .tox-tinymce {
    z-index: 1060;
}

.modal .tox-dialog-wrap {
    z-index: 1070;
}

/* Hide textarea when TinyMCE is active */
.tinymce-editor {
    visibility: hidden;
    height: 0;
    margin: 0;
    padding: 0;
}
</style>
