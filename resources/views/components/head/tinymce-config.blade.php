<div>
    <!-- An unexamined life is not worth living. - Socrates -->
</div>

<script src="https://cdn.tiny.cloud/1/9iw2xqwn1593xsb15d6xpi0y41mtrets5ms0l5s8kekdgf63/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
        height: 400,
        menubar: true,
        setup: function (editor) {
            editor.on('init', function () {
                editor.getContainer().style.transition="border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out"
            });
            editor.on('focus', function () {
                editor.getContainer().style.boxShadow="0 0 0 .2rem rgba(0, 123, 255, .25)",
                editor.getContainer().style.borderColor="#80bdff"
            });
            editor.on('blur', function () {
                editor.getContainer().style.boxShadow="",
                editor.getContainer().style.borderColor=""
            });
        }
    });
</script>