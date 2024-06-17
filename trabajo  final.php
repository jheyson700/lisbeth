

<?php
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "file_storage";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Directorio donde se guardarán los archivos
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo ya existe
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Permitir ciertos formatos de archivo
    $allowed_types = ['jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx'];
    if (!in_array($fileType, $allowed_types)) {
        echo "Sorry, only JPG, JPEG, PNG, GIF, PDF, DOC & DOCX files are allowed.";
        $uploadOk = 0;
    }

    // Verificar si $uploadOk está configurado a 0 por un error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // Preparar la declaración SQL para insertar información del archivo en la base de datos
            $stmt = $conn->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)");
            $stmt->bind_param("ss", $_FILES["file"]["name"], $target_file);

            if ($stmt->execute()) {
                echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded and saved.";
            } else {
                echo "Sorry, there was an error saving your file information in the database.";
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
    Select file to  dowload:
    <input type="file" name="file" id="file">
    <input type="submit" value="Upload File" name="submit">
</form>

</body>
</html>
