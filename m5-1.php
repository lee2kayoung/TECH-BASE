<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <h2>～掲示板テーマ～</h2>
    <?php
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS mission5" //テーブルを作成する
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "passc TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    $n = "";
    $c = "";
    $edit = "";
    if(isset($_POST["passe"])){  //パスワードがない投稿は編集できない
        if((!empty($_POST["passe"])||$_POST["passe"]=="0") && !empty($_POST["num"])){ //編集
            $num = $_POST["num"];
            $passe = $_POST["passe"];
            $edit = $num;
            $id = $num;
            $sql = 'SELECT * FROM mission5 WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();
            $results = $stmt->fetchAll();                             
            foreach($results as $row){
                if($row['passc'] == $passe){
                    $n = $row['name']; //名前がフォームに表示
                    $c = $row['comment']; //コメント
                }
            }
        }
    }
    ?>
    <form action="" method="post">
        <input type="text" name="name" value="<?php echo $n; ?>" placeholder="名前">
        <input type="text" name="comment" value="<?php echo $c; ?>" placeholder="コメント">
        <input type="hidden" name="edinum" value="<?php echo $edit; ?>">
        <input type="password" name="passc" placeholder="パスワード">
        <input type="submit" name="submit" value="送信">
    </form>
    <form action="" method="post">
        <input type="text" name="number" placeholder="削除対象番号">
        <input type="password" name="passd" placeholder="パスワード">
        <input type="submit" name="delete" value="削除">
    </form>
    <form action="" method="post">
        <input type="text" name="num" placeholder="編集対象番号">
        <input type="password" name="passe" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
    </form>
    <?php
        if(!empty($_POST["name"]) && !empty($_POST["comment"])){ //投稿
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $passc = $_POST["passc"];
            //$count = count(file($filename))+1; 変える
            $date = date("Y/m/d H:i:s");
            if(!empty($_POST["edinum"])){ //編集投稿
                $edinum = $_POST["edinum"];
                $id = $edinum;
                $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,passc=:passc WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':passc', $passc, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }else{      //普通の投稿機能　データ入力
                $sql = "INSERT INTO mission5 (name, comment, date, passc) VALUES (:name, :comment, :date, :passc)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':passc', $passc, PDO::PARAM_STR);
                $stmt->execute();        
            }
        }else if((!empty($_POST["number"])) && (!empty($_POST["passd"]) || $_POST["passd"] == "0")){ //削除
            $number = $_POST["number"];
            $passd = $_POST["passd"];
            $id = $number;
            $sql = 'SELECT * FROM mission5 where id=:id'; //パスワードを抽出
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
            foreach($results as $row){
                $pass = $row['passc'];
            }
            if($pass == $passd){
                $sql = 'delete from mission5 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        $sql = 'SELECT * FROM mission5'; //データを表示
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].'. ';
            echo $row['name'].' ';
            echo $row['date'].'<br>';
            echo $row['comment'].'<br>';
        echo "<hr>";
        }    
    ?>
</body>
</html>