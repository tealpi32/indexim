<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Veritabanı ve Tablo Gösterme</title>
<style>
    body {
        
        display: flex;
        
    }
    .left-column {
        width: 25%; /* Sol sütun genişliği */
        padding: 20px;
        border: 1px solid #ccc;
    }
    .right-column {
        
        padding: 20px;
        border: 1px solid #ccc;
    }
    .container {
        margin-bottom: 20px;
    }
    h2 {
        margin-bottom: 10px;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    ul li {
        margin-bottom: 5px;
    }
    ul li a {
        text-decoration: none;
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    table, th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>
<div class="left-column">
    <?php
    // MySQL bağlantı bilgileri
    $servername = "localhost";
    $username = "root";
    $password = "f7e49e1af7e49e1a"; // MySQL root kullanıcısının şifresi
    $conn = new mysqli($servername, $username, $password);

    // Bağlantı kontrolü
    if ($conn->connect_error) {
        die("Veritabanı bağlantı hatası: " . $conn->connect_error);
    }

    // Tüm veritabanlarını listele
    $sql = "SHOW DATABASES";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Tüm Veritabanları</h2>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li style='font-family: Arial, sans-serif; font-weight: bold;'><a href='sondb.php?dbc=".$row['Database']."'>" . $row['Database'] . "</a></b></li>";
            if (isset($_GET['dbc'])){
                if($_GET['dbc']==$row['Database'])
                {
                    if(isset($_GET['dbc'])){
                        $servername = "localhost";
                        $username = "root";
                        $password = "f7e49e1af7e49e1a";
                        $dbname = $_GET['dbc']; 
                
                        try {
                            // PDO bağlantısı oluşturma
                            $conna = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                            $conna->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                            // Veritabanındaki tüm tabloları listele
                            $sql = "SHOW TABLES";
                            $stmt = $conna->prepare($sql);
                            $stmt->execute();
                
                            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                            if (count($tables) > 0) {
                                
                                echo "<ul>";
                                foreach ($tables as $table) {
                                    echo "<li><strong><a href='sondb.php?dbc=".$dbname."&table=".$table."'>&middot;    $table</a></strong></li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>&middot;     Bu veritabanında hiç tablo bulunamadı.</p>";
                            }
                
                        } catch(PDOException $e) {
                            die("Veritabanı hatası: " . $e->getMessage());
                        }
                
                        // Bağlantıyı kapat
                        $conna = null;
                    }
                }
            }
        }
        echo "</ul>";
    } else {
        echo "<p>Hiç veritabanı bulunamadı.</p>";
    }

    // Bağlantıyı kapat
    $conn->close();
    
    
echo '</div>

<div class="right-column">';
    if(isset($_GET['dbc']) and isset($_GET['table'])){
        
        $dbname = $_GET['dbc'];
        $tableName = $_GET['table'];
$gcc=[];
        try {
            echo "<td><form action='sondb.php' method='GET'>";
echo "<input type='hidden' name='ekledb' value='$dbname'>";
echo "<input type='hidden' name='ekletable' value='$tableName'>";
echo "<button type='submit' name='ekle' class='btn btn-primary'>Veri Ekle</button>";
echo "</form>";
            // PDO bağlantısı oluşturma
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Tablodaki sütun isimlerini al
            $sql_columns = "SHOW COLUMNS FROM $tableName";
            $stmt_columns = $conn->prepare($sql_columns);
            $stmt_columns->execute();

            $columns = $stmt_columns->fetchAll(PDO::FETCH_COLUMN);

            // Tablodaki tüm verileri al
            $sql_data = "SELECT * FROM $tableName";
            $stmt_data = $conn->prepare($sql_data);
            $stmt_data->execute();

            $result = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0) {
                echo "<h2>$tableName Tablosu Verileri</h2>";
                
                echo "<table border='1'>";
                // Sütun başlıklarını yazdır
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<th>$column</th>";
                }
                echo "<th>İşlemler</th>";
                echo "</tr>";
                // Veri satırlarını yazdır
                foreach ($result as $row) {
                    echo "<tr>";
                    foreach ($columns as $column) {
                        $gcc[]=$row[$column];
                        echo "<td>" . $row[$column] . "</td>";
                        
                    }
                    $birlesik = implode(",,", $gcc);
                    $dbc = $_GET['dbc'];
                    $table = $_GET['table'];
                    // Düzenleme düğmesi
echo "<td><form action='sondb.php' method='GET'>";
echo "<input type='hidden' name='duzendb' value='$dbc'>";
echo "<input type='hidden' name='duzentable' value='$table'>";
echo "<input type='hidden' name='verii' value='$birlesik'>";
echo "<button type='submit' name='duzenle' class='btn btn-primary'>Düzenle</button>";
echo "</form>";

// Silme düğmesi
echo "<form action='sondb.php' method='GET'>";
echo "<input type='hidden' name='sildb' value='$dbc'>";
echo "<input type='hidden' name='siltable' value='$table'>";
echo "<input type='hidden' name='verii' value='$birlesik'>";
echo "<button type='submit' name='sil' class='btn btn-danger'>Sil</button>";
echo "</form></td>";
                    echo "</tr>";
                    unset($gcc);
                }
                echo "</table>";
            } else {
                echo "<p>Bu tabloda hiç veri bulunamadı.</p>";
            }

        } catch(PDOException $e) {
            die("Veritabanı hatası: " . $e->getMessage());
        }

        // Bağlantıyı kapat
        $conn = null;
    
    
}

if (isset($_GET['ekletable'])){


    
    $dbnaame=$_GET['ekledb'];
    $tableName=$_GET['ekletable'];
    $gcc=[];
    $gcc1=[];
    
    
        // PDO bağlantısı oluşturma
        $conn = new PDO("mysql:host=$servername;dbname=$dbnaame", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tablodaki sütun isimlerini al
        $sql_columns = "SHOW COLUMNS FROM $tableName";
        $stmt_columns = $conn->prepare($sql_columns);
        $stmt_columns->execute();

        $columns = $stmt_columns->fetchAll(PDO::FETCH_COLUMN);

        // Tablodaki tüm verileri al
        $sql_data = "SELECT * FROM $tableName";
        $stmt_data = $conn->prepare($sql_data);
        $stmt_data->execute();

        $result = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo "<h2>$tableName Tablosu Verileri</h2>";
            
            // Sütun başlıklarını yazdır
            echo "<tr>";
            foreach ($columns as $column) {
                $gcc1[]=$column;
                
            }
                    $sayi=count($gcc1);
                    echo '<form action="" method="POST">';
                    for ($i=0; $i < $sayi; $i++) { 
                        echo '<input type="text" name="'.$i.'" placeholder="'.$gcc1[$i].'"><br>';
                    }
                    echo '<input type="hidden" name="dbnamee" value="'.$dbnaame.'">';
                    echo '<input type="hidden" name="tablename" value="'.$tableName.'">';
                    echo '<input type="hidden" name="sayi" value="'.$sayi.'">';
                    echo '<button type="submit" name="eklekayit">Kaydet</button></form>';
                    
                
                
            
            
            
            
        } else {
            echo "<p>Bu tabloda hiç veri bulunamadı.</p>";
        }

    
    // Bağlantıyı kapat
    $conn = null;

    



}
if (isset($_POST['eklekayit'])){
   
    $duzenVerileri = $_POST;
    $dbname = $_POST['dbnamee'];
    $tableName = $_POST['tablename'];   
    
    try {
        // Veritabanı bağlantısı
        $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Tablodaki sütun isimlerini al
        
        $sql_columns = "SHOW COLUMNS FROM $tableName";
        $stmt_columns = $db->prepare($sql_columns);
        $stmt_columns->execute();
    
        $columns = $stmt_columns->fetchAll(PDO::FETCH_COLUMN);
    
        // Sütun isimlerini bir diziye aktar
        $gcc1 = $columns;
    
        // INSERT sorgusunu hazırla
        $sql = "INSERT INTO $tableName (";
        $sql_columns = implode(', ', $gcc1); // Sütunları virgülle ayırarak SQL için hazırla
        $sql .= $sql_columns . ") VALUES (";
    
        // Parametreleri sorguya ekleyerek hazırla
        $params = [];
        foreach ($duzenVerileri as $key => $value) {
            // Anahtarın rakamla başlayıp başlamadığını kontrol et
            if (ctype_digit(substr($key, 0, 1))) {
                $param_name = ":param_$key";
                $params[$param_name] = $value;
                $sql .= $param_name . ", ";
            }
        }
        
        $sql = rtrim($sql, ", ") . ")"; // Son virgülü kaldır ve parantezi kapat
    
        // Sorguyu hazırla ve parametreleri bağla
        $stmt = $db->prepare($sql);
        foreach ($params as $param_name => $param_value) {
            $stmt->bindValue($param_name, $param_value);
        }
    
        // Sorguyu çalıştır
        $stmt->execute();
    
        echo '<script>window.location="?dbc=' . $dbnaame . '&table='.$tableName.'"; alert("Veri Eklendi");</script>';
    } catch (PDOException $e) {
        echo "Veri ekleme hatası: " . $e->getMessage();
    }
    
    
    
}
if (isset($_GET['siltable'])){


    $dbnaame=$_GET['sildb'];
    $tableName=$_GET['siltable'];
    $verii=$_GET['verii'];
    $gcc=[];
    $gcc1=[];
    
    try {
        // PDO bağlantısı oluşturma
        $conn = new PDO("mysql:host=$servername;dbname=$dbnaame", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tablodaki sütun isimlerini al
        $sql_columns = "SHOW COLUMNS FROM $tableName";
        $stmt_columns = $conn->prepare($sql_columns);
        $stmt_columns->execute();

        $columns = $stmt_columns->fetchAll(PDO::FETCH_COLUMN);

        // Tablodaki tüm verileri al
        $sql_data = "SELECT * FROM $tableName";
        $stmt_data = $conn->prepare($sql_data);
        $stmt_data->execute();

        $result = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo "<h2>$tableName Tablosu Verileri</h2>";
            echo "<table border='1'>";
            // Sütun başlıklarını yazdır
            echo "<tr>";
            foreach ($columns as $column) {
                $gcc1[]=$column;
                
            }
            
            echo "</tr>";
            // Veri satırlarını yazdır
            foreach ($result as $row) {
                echo "<tr>";
                foreach ($columns as $column) {
                    $gcc[]=$row[$column];
                             
                }
                $birlesik = implode(",,", $gcc);
                if ($birlesik==$verii){
                    $sayi=count($gcc)-1;
                    $kosul1=$gcc1[$sayi];
                    $kosul2=$gcc[$sayi];
    $db = new PDO("mysql:host=$servername;dbname=$dbnaame;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $where1 = $kosul1;
            $where2 = $kosul2;

            // UPDATE sorgusunu parametreli olarak hazırla
            $sql = "DELETE from $tableName WHERE $where1 = :where2";
            $stmt = $db->prepare($sql);

            // Parametreleri bind et
            
            $stmt->bindParam(':where2', $where2);

            // Sorguyu çalıştır
            $stmt->execute();

            
        
                    break;
                }
                unset($gcc);
            }
            echo "</table>";
        } else {
            echo "<p>Bu tabloda hiç veri bulunamadı.</p>";
        }

    } catch(PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }
    // Bağlantıyı kapat
    $conn = null;

    echo '<script>window.location="?dbc=' . $dbnaame . '&table='.$tableName.'"; alert("Veri Silindi");</script>';

}




if (isset($_GET['duzentable'])){
    $dbnaame=$_GET['duzendb'];
    $tableName=$_GET['duzentable'];
    $verii=$_GET['verii'];
    $gcc=[];
    try {
        // PDO bağlantısı oluşturma
        $conn = new PDO("mysql:host=$servername;dbname=$dbnaame", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tablodaki sütun isimlerini al
        $sql_columns = "SHOW COLUMNS FROM $tableName";
        $stmt_columns = $conn->prepare($sql_columns);
        $stmt_columns->execute();

        $columns = $stmt_columns->fetchAll(PDO::FETCH_COLUMN);

        // Tablodaki tüm verileri al
        $sql_data = "SELECT * FROM $tableName";
        $stmt_data = $conn->prepare($sql_data);
        $stmt_data->execute();

        $result = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo "<h2>$tableName Tablosu Verileri</h2>";
            echo "<table border='1'>";
            // Sütun başlıklarını yazdır
            echo "<tr>";
            foreach ($columns as $column) {
                
            }
            
            echo "</tr>";
            // Veri satırlarını yazdır
            foreach ($result as $row) {
                echo "<tr>";
                foreach ($columns as $column) {
                    $gcc[]=$row[$column];
                              
                }
                $birlesik = implode(",,", $gcc);
                if ($birlesik==$verii){
                    $sayi=count($gcc);
                    echo '<form action="" method="POST">';
                    for ($i=0; $i < $sayi; $i++) { 
                        echo '<input type="text" name="'.$i.'" value="'.$gcc[$i].'"><br>';
                    }
                    echo '<input type="hidden" name="dbnamee" value="'.$dbnaame.'">';
                    echo '<input type="hidden" name="tablename" value="'.$tableName.'">';
                    echo '<input type="hidden" name="verii" value="'.$verii.'">';
                    echo '<input type="hidden" name="veriisayi" value="'.$sayi.'">';
                    echo '<button type="submit" name="degistirkayit">Değiştir</button></form>';
                    break;
                }
                unset($gcc);
            }
            echo "</table>";
        } else {
            echo "<p>Bu tabloda hiç veri bulunamadı.</p>";
        }

    } catch(PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }
    // Bağlantıyı kapat
    $conn = null;
}
if(isset($_POST['degistirkayit']))
{
    $dbnaame=$_POST['dbnamee'];
    $tableName=$_POST['tablename'];
    $verii=$_POST['verii'];
    $sayii=$_POST['veriisayi']-1;
    $duzenVerileri = $_POST;
    $gcc=[];
    $gcc1=[];
    $say=rand(0,$sayii);
    try {
        // PDO bağlantısı oluşturma
        $conn = new PDO("mysql:host=$servername;dbname=$dbnaame", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tablodaki sütun isimlerini al
        $sql_columns = "SHOW COLUMNS FROM $tableName";
        $stmt_columns = $conn->prepare($sql_columns);
        $stmt_columns->execute();

        $columns = $stmt_columns->fetchAll(PDO::FETCH_COLUMN);

        // Tablodaki tüm verileri al
        $sql_data = "SELECT * FROM $tableName";
        $stmt_data = $conn->prepare($sql_data);
        $stmt_data->execute();

        $result = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo "<h2>$tableName Tablosu Verileri</h2>";
            echo "<table border='1'>";
            // Sütun başlıklarını yazdır
            echo "<tr>";
            foreach ($columns as $column) {
                $gcc1[]=$column;
                
            }
            
            echo "</tr>";
            // Veri satırlarını yazdır
            foreach ($result as $row) {
                echo "<tr>";
                foreach ($columns as $column) {
                    $gcc[]=$row[$column];
                             
                }
                $birlesik = implode(",,", $gcc);
                if ($birlesik==$verii){
                    $sayi=count($gcc);
                    $kosul1=$gcc1[$say];
                    $kosul2=$gcc[$say];
                    $db = new PDO("mysql:host=$servername;dbname=$dbnaame;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    for ($i = 0; $i < count($gcc); $i++) {
        if ($gcc[$i] != $duzenVerileri[$i]) {
            $set1 = $gcc1[$i];
            $set2 = $duzenVerileri[$i];
            $where1 = $kosul1;
            $where2 = $kosul2;

            // UPDATE sorgusunu parametreli olarak hazırla
            $sql = "UPDATE $tableName SET $set1 = :set2 WHERE $where1 = :where2";
            $stmt = $db->prepare($sql);

            // Parametreleri bind et
            $stmt->bindParam(':set2', $set2);
            $stmt->bindParam(':where2', $where2);

            // Sorguyu çalıştır
            $stmt->execute();

            
        }}
                    break;
                }
                unset($gcc);
            }
            echo "</table>";
        } else {
            echo "<p>Bu tabloda hiç veri bulunamadı.</p>";
        }

    } catch(PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }
    // Bağlantıyı kapat
    $conn = null;

    echo '<script>window.location="?dbc=' . $dbnaame . '&table='.$tableName.'"; alert("Veri Güncellendi");</script>';

}


?>
</div>
</body>
</html>
