<?php

$today=date('Y-m-d');

if (!isset($_SESSION['total_'. $today])){
    $total=db_get('total',['date'=>$today]);
    if($today){
        $total +=1;
        db_save('total',$total);
    }else{
        db_save('total',['date'=>$today,'total'=>1]);
    }
    $_SESSION['total_'.$today]=true;
}

$today_total=db_get('total',['date'=>$today])['total'];
$total=db_query(' SELECT SUM(total) total FROM total')[0]['total'];