<?php
session_start();

$user = $_SESSION['user'] ?? null;
$is_login = $user !== null;
$is_admin = $user === 'admin';

$conn =new PDO('mysql:host=localhost;charset=utf8;dbname=db11','root','');

function db_all($table,$param=[],$option=[]){
    global $conn;
    $sql = 'SELECT * FROM ' . $table . ' WHERE 1 = 1 ';//注意from後面空格
   
    if(is_array($param)){
        foreach($param as $key => $value){
            $sql .= ' AND ' . $key . ' = "' . $value . '" ';
        }
    }

    if(isset($option['order'])){
        $sql .= ' ORDER BY ' . $option['order'];
    }

    if(isset($option['limit'])){
        $sql .= ' LIMIT ' . $option['limit'];
    }

    if(isset($option['offset'])){
        $sql .= ' OFFSET ' . $option['offset'];
    }

    $state = $conn -> query($sql);
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function db_get($table,$param=[]){
    global $conn;
    $sql = 'SELECT * FROM ' . $table . ' WHERE 1 = 1 ';
    if(is_array($param)){
        foreach($param as $key => $value){
            $sql .= ' AND ' . $key . ' = "' . $value . '" ';
        }
    }else{
        $sql .= ' AND id = "' . $param . '" ';
    }
    $sql .= ' LIMIT 1 ';
    $state = $conn -> query($sql);
    return $state->fetch(PDO::FETCH_ASSOC);
}

function db_count($table,$param=[]){
    global $conn;
    $sql = 'SELECT count(*) FROM ' . $table . ' WHERE 1 = 1 ';
    if(is_array($param)){
        foreach($param as $key => $value){
            $sql .= ' AND ' . $key . ' = "' . $value . '" ';
        }
    }
    $sql .= ' LIMIT 1 ';
    $state = $conn -> query($sql);
    return $state->fetchColumn();
}

function db_delete($table,$param=[]){
    global $conn;
    $sql = ' DELETE FROM ' . $table . ' WHERE 1 = 1 ';
    if(is_array($param)){
        foreach($param as $key => $value){
            $sql .= ' AND ' . $key . ' = "' . $value . '" ';
        }
    }else{
        $sql .= ' AND id = "' . $param . '" '; //刪除功能，記得把id加回來
    }
    return $conn -> exec($sql);//記得return(exec執行)
}

function db_save($table,$param=[]){
    global $conn;
    $id=$param['id']??null; //這句熟記
    $sql='';
    
    if($id){
        $tmp=[];
        foreach($param as $key => $value){
            $tmp[]= $key . ' = "' . $value . '" '; //沒有AND
        }

        $sql .= ' UPDATE ' .$table; //記得全部是.=
        $sql .= ' SET ' .join(',',$tmp);
        $sql .= ' WHERE id = "' . $id . '" ' ; //記得""包'.$id.'
        
    }else{
        $sql .= ' INSERT INTO ' .$table;
        $sql .= ' (' . join(',',array_keys($param)) . ')';//這句熟記
        $sql .= ' VALUES("'. join ('","',$param).'")';//這句是在取值，記得VALUES，用","拆
    }

    return $conn -> exec($sql);//記得return
}
function db_query($sql,$param=[]){ //這個function工具是用日後直接寫sql語句用
    global $conn;
    $state=$conn->prepare($sql);//記得$state

    if(is_array($param)){
        $state->execute($param);//這句熟記
    }

    //$state = $conn -> query($sql); 這句不用，因為if已經做了
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function back(){
    header('location: ' . $_SERVER['HTTP_REFERER']);
}

function to($url){
    header('location: ' . $url);
}

function getMsg(){
    $msg=$_SESSION['m'] ?? '';
    unset($_SESSION['m']);//這句記得裡面寫法
    return $msg;
}

function setMsg($msg){
    $msg=$_SESSION['m'];
}

$today= date('Y-m-d'); //date()時間格式

//下面請熟記

if (!isset($_SESSION['total_'.$today])){
    $total=db_get('total',['date'=>$today]);
    if($total){
        $total['total'] +=1;
        db_save('total',$total);
    }else{
        db_save('total',['date'=>$today,'total'=>1]);
    }
    $_SESSION['total_'.$today]=true;
}

$today_total=db_get('total',['date'=>$today])['total'];
$total=db_query(' SELECT SUM(total) total FROM total ')[0]['total'];


