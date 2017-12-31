tinymce.init({
    selector: '#message-text',

    plugins: 'link codesample',

    toolbar: 'bold italic strikethrough | link | codesample',

    menubar: false,

    // link plugin

    target_list: false,

    link_title: false,

    // other

    anchor_bottom: false,

    anchor_top: false,

    branding: false,

    elementpath: false,

    setup: function (editor) {
        // TinyMCE copies text to the textarea only
        // when the form is submitted, which makes
        // live validation of the field impossible
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

$('#message-form').on('beforeSubmit', function () {
    var yiiForm = $(this);

    var ajax = $.ajax({
        type: yiiForm.attr('method'),

        url: yiiForm.attr('action'),

        // available only in the newest browsers
        data: new FormData(yiiForm[0]),

        processData: false,

        contentType: false
    });

    ajax.done(function () {
        $('#message-captcha-image').yiiCaptcha('refresh');

        // clearing the form
        yiiForm.trigger('reset');

        // removing focus
        yiiForm.find('input').each(function (index, element) {
            element.blur();
        });

        var URL = window.location.href;

        // updating messages
        $('#messages').load(URL + ' #messages > *');
    });

    return false;
});