<?php
class userModel{
public function __construct()
{
   $this->db = new Conexion;


}


public function m_consulta($tipo, $param){
 
switch ($tipo) {

   case 0: // trae select de todo los usuarios
      $cond = implode(' ', $param);
      return 'SELECT U.id_us, CONCAT (U.firt_name, " ", U.last_name , " " ,U.second_name)
      FROM users U 
      JOIN roles R ON R.id_ac_rol = U.fk_rol'.$cond;
      break;
 
   case 1: // consulta medidas de usario 
      $cond = implode( ' ', $param );
      return 'SELECT
      U.fk_doc, CONCAT (U.firt_name, " ", U.last_name , " " ,U.second_name) , U.img, U.date_of_birth , U.gender,
      M.date_create, M.caracter,
      MT.name_type_n, 
      N.name_type_m
      FROM users U 
      JOIN measures M ON M.fk_us = U.id_us 
      JOIN types_n N ON  N.id_n = M.fk_type_n 
      JOIN types_m MT ON M.fk_type_m = MT.id_m'
      .$cond;

   case 2: //  documentos
      return 'SELECT * FROM `type_docs`';

   case 3: // peso de un usuario
      'SELECT * FROM `measures`'.$param;
      # code...
      break;

   case 4: // login de usario
      $cond= implode(' ', $param);
     return 'SELECT * FROM users
      '.$cond;

   case 5://consulta La solicitudes de consulta
      if(!isset($param)) $param=[];
      $cond = implode(' ',$param);
      return 'SELECT QU.fk_doc, QU.fk_us,
         Q.id, Q.date_request, Q.date_quote, Q.obs, Q.status,
         CONCAT(U.firt_name," ", U.second_name," ", U.last_name ), R.name_rol
            FROM quotes Q
            LEFT JOIN quote_users QU ON  QU.fk_quote = Q.id
            LEFT JOIN users U ON U.id_us = QU.fk_us
            LEFT JOIN roles R ON R.id_ac_rol = U.fk_rol  '.$cond; 

   case 6:
      $cond = implode( ' ', $param );
      return'SELECT DISTINCT QU.fk_doc, QU.fk_us,
      
      U.firt_name, U.second_name, U.last_name, R.name_rol, U.email, U.date_of_birth, U.password
         FROM quotes Q
         LEFT JOIN quote_users QU ON  QU.fk_quote = Q.id
         LEFT JOIN users U ON U.id_us = QU.fk_us
         LEFT JOIN roles R ON R.id_ac_rol = U.fk_rol '.$cond;
      break;
   
   case 7:
      break;
   
   default:
      # code...
      break;
}

// UPDATE quotes SET date_quote ='2021-03-18',  status ="A" ;

}

public function m_update($param ){
// return 'UPDATE quotes SET date_quote ='2021-03-18',  status ="A"'

}

public function m_delete($a){

}

public function m_insert($param){
   return 'INSERT INTO '.$param[0]. ' VALUES ("'.implode('","' ,$param[1]).'")';
}

public function m_ultimo_id($param){
   
 $obj = ($this->db->query('SELECT MAX('.$param[0].') from '.$param[1].''));
 $i = 0;
 while ($result = mysqli_fetch_row($obj)) {
   $data[0] = $result;
   $i++;
 }
 return $data[0][0];
}





}

?>