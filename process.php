<?php
    require_once 'lib/dbinfo.php';
    $oldumask = umask(0);
    switch($_GET['mode']){

        case 'login':
            $stmt = $dbh->prepare("SELECT * from USERINFO WHERE id = :id and pw = :pw");
            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':pw',$pw);
            $id = $_POST['id'];
            $pw = $_POST['pw'];
            $stmt->execute();
            $check = $stmt->fetch();
            if(empty($check)){
                echo "<script type=\"text/javascript\">alert('아이디 혹은 비밀번호를 확인해주세요!');</script>";
                echo("<script>location.replace('login.html');</script>");
                }
            else{
                $_SESSION['id']=$check['id'];
                $_SESSION['pw']=$check['pw'];
                $_SESSION['grade']=$check['grade'];
                header("Location: upload.php"); 
            }
            break;


        case 'create':
            $idcheck = $dbh->prepare("SELECT * FROM USERINFO WHERE id=:id");
            $idcheck->bindParam(':id', $id);
        
            $id = $_POST['id'];
            $idcheck->execute();
        
            $idif = $idcheck->fetch();
            if(!empty($idif)){
                echo "<script type=\"text/javascript\">alert('존재하는 아이디입니다!');</script>";
                echo("<script>location.replace('makeaccount.php');</script>");
            }
            else{
                $stmt = $dbh->prepare("INSERT INTO USERINFO (id, pw, tel, name) VALUES (:id, :pw, :tel, :name)");
                $stmt->bindParam(':id',$id);
                $stmt->bindParam(':pw',$pw);
                $stmt->bindParam(':tel',$tel);
                $stmt->bindParam(':name',$name);
                $id = $_POST['id'];
                $pw = $_POST['pw'];
                $tel = (string)$_POST['tel1']."-".(string)$_POST['tel2']."-".(string)$_POST['tel3'];
                $name = $_POST['name'];
                $stmt->execute();
                mkdir("/home/samba/userfile/".$id , 0777, true);
                mkdir("/home/samba/userfile/".$id."trash" , 0777, true);
                mkdir("/home/samba/userfile/thumbnail/".$id , 0777, true);
                
                header("Location: login.html");
            }
            break;

            
        case 'change':
            $stmt = $dbh->prepare("UPDATE USERINFO SET id=:id, pw =:pw, tel=:tel, name=:name, grade=:grade WHERE id=:cid");
            $stmt->bindParam(':cid',$cid);
            $stmt->bindParam(':id',$oid);
            $stmt->bindParam(':pw',$cpw);
            $stmt->bindParam(':tel',$ctel);
            $stmt->bindParam(':name',$cname);
            $stmt->bindParam(':grade',$cgrade);

            $cid = $_POST['cid'];
            $oid = $_POST['id'];
            $cpw = $_POST['pw'];
            $ctel = $_POST['tel'];
            $cname = $_POST['name'];
            $cgrade = $_POST['grade'];

            $stmt->execute();
            header("Location: tool.php");
            break;


        case 'delete':
            $stmt = $dbh->prepare("DELETE FROM USERINFO WHERE id=:cid");
            $stmt->bindParam(':cid',$cid);
            $cid = $_GET['cid'];
            $stmt->execute();
            header("Location: tool.php");
            break;

        case 'mkdir':
            $dirname = $_POST['dirname'];
            $id = $_SESSION['id'];
            $link = $id."/".$_SESSION['link'];
            $tmp_name=uniqid();

            if($link === ''){
                mkdir("/home/samba/userfile/$id/$tmp_name", 0777, true);
            }
            else{
                mkdir("/home/samba/userfile/$link/$tmp_name", 0777, true);
            }
            $thumbdir ='img/directory.png';

            $stmt = $dbh->prepare("INSERT INTO DATAINFO VALUES (:tmp_name,:name,'dir',0,:path,:id,:thumbdir)");
            $stmt->bindParam(':tmp_name',$tmp_name);
            $stmt->bindParam(':name',$dirname);
            $stmt->bindParam(':path',$link);
            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':thumbdir',$thumbdir);
            $stmt->execute();
            
            echo  "<script type='text/javascript'>";
            echo "opener.parent.location.reload();";
            echo "window.close();";
            echo "</script>";
            break;

            
        }
        umask($oldumask);
?>