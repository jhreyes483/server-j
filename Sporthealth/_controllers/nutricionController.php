<?php

// use Mpdf\Tag\Section;

class nutricionController extends Controller
{
   private $db;
   private $param;
   //
   public function __construct(){

      $this->objM = $this->loadModel('userModel', 'userModel');
      $this->db = new Conexion;
      parent::__construct();
      $this->_view->setCss(array('font-Montserrat', 'google', 'bootstrap.min', 'jav', 'animate', 'fontawesome-all'));
      //   $this->_view->setJs(array('jquery-1.9.0', 'bootstrap.min', 'popper.min', 'fontawasome-ico', 'cUsuariosJquery', 'tablesorter-master/jquery.tablesorter'))
      $this->param = $this->getParam();
   }
   //
   public function index(){
      $this->_view->setCss(['style']);
      $this->_view->renderizar('index');
   }


   public function perfil(){
      if(isset($_POST) && !empty($_POST) ){
         $sql =  'UPDATE users SET firt_name ="' . $this->getSql('firt_name').'", last_name ="' . $this->getSql('last_name') . 
         '", email = "'.$this->getSql('email').'", date_of_birth = "'.$this->getSql('rol').'", password = "'.$this->getSql('password').'"    WHERE id_us = ' .$_SESSION['usuario']['id_us'];
         $b = $this->db->query($sql);
         if($b){
            echo '<script>aler("Actualizo usuario"):</script>';
         }else{
            echo '<script>aler("Error al actualizar");</script>';
         }
      }

      $dato[0] = ' WHERE U.id_us = '.$_SESSION['usuario']['id_us'];
      $sql = $this->objM->m_consulta(6, $dato);
      $r = $this->db->query($sql);
      $this->_view->user = $this->m_trae_array($r,2);
      $this->_view->renderizar('perfil');
   }

   public function paciente(){
      $relTabla = [ 0=>23, 1=>26, 2=>13, 3=>27, 4=>28, 5=>29, 6=>11, 7=>25, 8=>31 ];
      $relMedida= [ 0=>1, 1=>3, 2=>2, 3=>2, 4=>2, 5=>2, 6=>2,7=>1, 8=>8];

      $dato[0] = ' WHERE id_us = '.$this->getSql('id_us');
      $dato[1] = ' LIMIT 1';
      $sql = $this->objM->m_consulta(4, $dato);
      $u = $this->db->query($sql);
     
      $doc = $this->m_trae_array($u,2);
   if(isset($_POST['a'])){
      foreach ($_POST['a'] as $i => $d) {
         $dato[0] = 'measures';
         $dato[1] = ['default',date('Y-m-d'),''.$d.'', $relMedida[$i], $relTabla[$i] , $this->getSql('id_us'),$doc[1] ];
         $sql =  $this->objM->m_insert($dato);
         $this->db->query($sql);
      }
   }
      $dato[0] = '';
      $this->_view->renderizar('datosPaciente');
   }


   public function asignar(){ 
      if (isset($_POST['rol'])) {
         $dato1[0] = ' WHERE Q.status = "' . $this->getSql('rol') . '"';
      }
      if (isset($_POST['informe'])) {
         $dato1[0] = ' WHERE U.id_us = ' . $this->getSql('id_us');
      }


      if (isset($_POST['update'])) {
         // 0 tabla 0  campos 1  actualizacion 2  condicion 3 
         $sql =  
         'UPDATE quotes SET date_quote ="' . $this->getSql('fecha_asig') . ' ' . $this->getSql('hora') . '",  status ="' . $this->getSql('status') . '" WHERE id = ' . $this->getSql('id_cita');
         $b = $this->db->query($sql);
         if ($b) {
            $dato2[0] = ' WHERE id_us = ' . $this->getSql('doctor');
            $dato2[1] = ' LIMIT 1';
            $sql = $this->objM->m_consulta(4, $dato2);
            $d = $this->db->query($sql);
            $d = $this->m_trae_array($d, 2);
            $id_doctor = $d[1];

            $d1[0] =  'quote_users';
            $d1[1] = [$id_doctor, $this->getSql('doctor'), $this->getSql('id_cita')];
            $sql = $this->objM->m_insert($d1);
            $b =  $this->db->query($sql);
            if ($b) {
               echo '<script>aler("Asigno cita");</script>';
            } else {
               echo '<script>aler("Error al asignar cita");</script>';
            }
         } else {
         }
      }


      // Trae las citas de los usuarios
      $sql = $this->objM->m_consulta(5, $dato1);
      $r   = $this->db->query($sql);
      $r   = $this->m_trae_array($r);
      $r   = $this->verificaResul($r);
      $this->_view->status =  $this->statusCita;


      // Select de usuario 
      $dato[0] = ' WHERE R.id_ac_rol = "D"';
      $sql = $this->objM->m_consulta(0, $dato);
      $r   = $this->db->query($sql);
      $this->_view->doctor   = $this->m_trae_array($r, 1);
      $this->_view->renderizar('asignarCita');
   }


   public function reportes(){
      $this->_view->setCss(['datatables/datatables.min','datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min']);
      $this->_view->setJs([  'jquery/jquery-3.3.1.min','loaderChart','popper/popper.min','bootstrap.min','datatables/datatables.min','datatables/Buttons-1.5.6/js/dataTables.buttons.min','datatables/JSZip-2.5.0/jszip.min','datatables/pdfmake-0.1.36/pdfmake.min','datatables/pdfmake-0.1.36/vfs_fonts','datatables/Buttons-1.5.6/js/buttons.html5.min','mainDatable', 'all'] );


      $dato[0] = '';
      $dato[1] = '';
      $dato[2] = ' ORDER BY date_create';
      $sql =  $this->objM->m_consulta(1, $dato);
      $r = $this->db->query($sql);
      $r = $this->m_trae_array($r);
      $this->verificaResul($r);
      $this->_view->renderizar('reportes');
   }

   public function informe(){
      $dato[0] = ' WHERE U.id_us = ' . $_POST['id_us'];
      $dato[1] = ' AND MT.id_m = 23';
      $dato[2] = ' ORDER BY date_create';
      $sql =  $this->objM->m_consulta(1, $dato);
      $r = $this->db->query($sql);
      $r = $this->m_trae_array($r);
      $meses = [
         1 => 'Enero',
         2 => 'Febrero',
         3 => 'Marzo',
         4 => 'Abril',
         5 => 'Mayo',
         6 => 'Junio',
         7 => 'Julio',
         8 => 'Agosto',
         9 => 'Septiembre',
         10 => 'Octubre',
         11 => 'Noviembre',
         12 => 'Diciembre'
      ];

      foreach ($r as $k => $v) {
         $mes =  date('m', strtotime($v[5]));
         $ar[$meses[abs($mes)]][] = $v;
      }

      foreach ($ar as $grupo => $d) {
         foreach ($d as $reg => $v) {
            $t[$grupo] = (array_sum(array_column($d, 6)) / count($d));
         }
      }
      $this->_view->peso = $t;
      $this->_view->renderizar('informe');
   }
}

// 
