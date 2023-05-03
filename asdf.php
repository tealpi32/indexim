<!DOCTYPE html>
<html lang="tr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>dosyayukle</title>
  </head>
  <body>
    <form action="asdf.php" method="post" enctype="multipart/form-data">
      <input type="file" name="file">
      <button type="submit" name="button">Kaydet</button>
      <?php

      if (isset($_POST['button'])) {
        $asdf = "./" . basename($_FILES['file']['name']);
         $sonuc = move_uploaded_file($_FILES["file"]["tmp_name"], $asdf);
      }

       ?>
    </form>
  </body>
</html>
