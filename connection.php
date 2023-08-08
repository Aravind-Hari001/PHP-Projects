<?php
    $con=new mysqli('localhost','root','','apollo');
    class DB{
       
        public function dml($sql){
            global $con;
            $res=mysqli_query($con,$sql);
            if ($res) {
                return true;
            }
            return false;
        }
        function dql($sql) {
            global $con;
            $res=mysqli_query($con,$sql);
            if ($res) {
                return $res;
            }
            return false;
        }
    }

?>