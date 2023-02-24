<?php

session_start();

$user = $_SESSION['user'] ?? null;
$is_login = $user !== null;
$is_admin = $user === 'admin';

$conn = new PDO('mysql:host=localhost;charset=utf8;dbname=db11','root','');

function db_all($table,$param=[],$option=[]){
    global $conn;
    $sql = ' SELECT * FROM ' . $table . ' WHERE 1 = 1 ';

    if(is_array($param)){
        foreach($param as $key => $value)
        $sql .= ' AND ' . $key . ' = "' . $value . '" ';
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

    $state = $conn->query($sql);
    return $state->FetchAll(PDO::FETCH_ASSOC);
}

function db_get($table,$param=[]){
    global $conn;
    $sql = ' SELECT * FROM ' . $table . ' WHERE 1 = 1 ';

    if(is_array($param)){
        foreach($param as $key => $value)
        $sql .= ' AND ' . $key . ' = "' . $value . '" ';
    }else{
        $sql .= ' AND id = "' . $param . '" ';
    }

    $sql .= ' LIMIT 1 ';
    $state = $conn->query($sql);
    return $state->Fetch(PDO::FETCH_ASSOC);
}

function db_count($table,$param=[]){
    global $conn;
    $sql = ' SELECT count(*) FROM ' . $table . ' WHERE 1 = 1 ';

    if(is_array($param)){
        foreach($param as $key => $value)
        $sql .= ' AND ' . $key . ' = "' . $value . '" ';
    }

    $sql .= ' LIMIT 1 ';
    $state = $conn->query($sql);
    return $state->fetchColumn();
}

function db_delete($table,$param=[]){
    global $conn;
    $sql = ' DELETE FROM ' . $table . ' WHERE 1 = 1 ';

    if(is_array($param)){
        foreach($param as $key => $value)
        $sql .= ' AND ' . $key . ' = "' . $value . '" ';
    }else{
        $sql .= ' AND id = "' . $param . '" ';
    }

    return $conn->exec($sql);
}

function db_save($table,$param=[]){
    global $conn;
    $id = $param['id'] ?? null;
    $sql = '';

    if($id){
        $tmp=[];
        foreach($param as $key => $value){
            $tmp[]= $key . ' = "' . $value . '" '; //沒有and，請記住
        }
        $sql .= ' UPDATE ' . $table;
        $sql .= ' SET ' . join(',',$tmp);
        $sql .= ' WHERE id =  "' . $id . '" ';
    }else{
        $sql .= ' INSERT INTO ' . $table;
        $sql .= ' (' .join(',',array_keys($param)).')';//裡面帶$param
        $sql .= ' VALUES("' . join('","',$param) . '")'; //請熟記
    }

    return $conn->exec($sql);
}

function db_query($sql,$param=[]){
    global $conn;
    $state = $conn->prepare($sql); //請熟記

    if(is_array($param)){
        $state->execute($param);//請熟記
    }

    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function back(){
    header('location: '.$_SERVER['HTTP_REFERER']);
}

function to($url){
    header('location: '.$url);
}

function getMsg(){
    $msg=$_SESSION['m'] ?? '';
    unset($_SESSION['m']);
    return $msg;
}

function setMsg($msg){
    $msg=$_SESSION['m'];
}

$today = date('Y-m-d');

if(!isset($_SESSION['total_'.$today])){ //請熟記是用點去接
    $total=db_get('total',['date' => $today]); //請熟記
        
        if($total){
            $total['total'] +=1;//請熟記
            db_save('total',$total);//請熟記
        }else{
            db_save('total',['date'=>$today,'total'=>1]);//請熟記
        }
        $_SESSION['total_'.$today]=true;//請熟，是用點連接
    }


$today_total=db_get('total',['date'=>$today])['total'];//請熟記
$total=db_query(' SELECT SUM(total) total WHERE total ')[0]['total'];//請熟記
