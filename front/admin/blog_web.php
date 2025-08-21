<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Zoi Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="administrador.css">

    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: '.editor-descripcion',
            height: 250,
            menubar: false,
            plugins: 'link lists preview',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | preview',
            setup: function (editor) {
                editor.on('keyup change', function () {
                    const content = editor.getContent();
                    const preview = document.getElementById('preview_descripcion_' + editor.id.split('_')[1]);
                    if (preview) preview.innerHTML = content;
                });
            }
        });

    </script>
    <script>
        tinymce.init({
            selector: '.editor-descripcion, .editor-contenido',
            height: 250,
            menubar: false,
            plugins: 'link lists preview',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | preview',
            setup: function (editor) {
                editor.on('keyup change', function () {
                    const tipo = editor.id.includes('descripcion') ? 'descripcion' : 'contenido';
                    const id = editor.id.split('_')[1];
                    const preview = document.getElementById('preview_' + tipo + '_' + id);
                    if (preview) preview.innerHTML = editor.getContent();
                });
            }
        });
    </script>


</head>
<body>

<?php include '../general/headerC.php'; ?>
<?php include 'bodyBW.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>

