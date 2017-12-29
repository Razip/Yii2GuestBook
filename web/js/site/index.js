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

$('#message-form').on('beforeSubmit', function () {
    var yiiForm = $(this);

    var ajax = $.ajax({
        type: yiiForm.attr('method'),

        url: yiiForm.attr('action'),

        // available only in newest browser versions
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

        // updating messages

        var URL = window.location.href;

        $('#messages').load(URL + ' #messages > *');
    });

    return false;
});