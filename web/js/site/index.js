tinymce.init({
    selector: '#message-text',
    plugins: 'link codesample',
    toolbar: 'bold italic strikethrough | link | codesample',
    menubar: false,

    // link plugin
    target_list: false,
    link_title: false,
    anchor_bottom: false,
    anchor_top: false,

    branding: false,
    elementpath: false,
    setup: function (editor) {
        editor.on('keyup', function () {
            $(editor.targetElm).val(editor.getContent());
        });

        // TinyMCE doesn't propagate the blur event
        // to the original textarea, which is needed
        // to invoke validation of the field
        editor.on('blur', function () {
            $(editor.targetElm).blur();
        });
    }
});