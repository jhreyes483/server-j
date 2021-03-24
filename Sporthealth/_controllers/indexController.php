<?php

class indexController extends Controller
{
   //
   public function __construct(){
      parent::__construct();
      $this->objM = $this->loadModel('userModel', 'userModel');
      $this->db = new Conexion;
   }
   //
   public function index(){
      @session_start();
      session_destroy();
      $this->_view->setCss(array('font-Montserrat', 'google', 'bootstrap.min', 'jav', 'animate', 'fontawesome-all'));
      //   $this->_view->setJs(array('jquery-1.9.0', 'bootstrap.min', 'popper.min', 'fontawasome-ico', 'cUsuariosJquery', 'tablesorter-master/jquery.tablesorter'));
      $this->_view->setJs(['cUsuariosJquery']);
      $this->_view->setCss(['style']);
      $this->_view->renderizar('index');
   }

   public function formulario(){
      if (isset($_POST['a']) && !empty($_POST['a'])) {
         switch ($_POST['a']) {
            case 'solicitud':
               $dato[0] = 'quotes'; // tabla del inse
               $dato[1] = ['default', date('Y-m-d H:I') . '00', null, null, 'S']; // datos a inertar
               $sql  = $this->objM->m_insert($dato);
               $b    = $this->db->query($sql);
               if ($b) {
                  $dato[0] = 'id';
                  $dato[1] = 'quotes';
                  // una vez inserta valida el id a insertar 
                  $id_insert =  $this->objM->m_ultimo_id($dato);
                  // captura documento de usuario 
                  $dato = [];
                  $dato[0] = ' WHERE id_us = ' . $this->getSql('id_us');
                  $dato[1] = ' LIMIT 1';
                  @@$sql = $this->objM->m_consulta(4, $dato);
                  $u = $this->db->query($sql);

                  $doc = $this->m_trae_array($u, 2);

                  $dato[0] = 'quote_users';
                  $dato[1] = [$doc[1], $this->getSql('id_us'), $id_insert];
                  $sql = $this->objM->m_insert($dato);
                  $insert =  $this->db->query($sql);
                  if ($insert) {
                     echo '<script>alert("Solisto cita con numero ' . $id_insert . '");</script>';
                  } else {
                     echo '<script>alert("No inserto cita");</script>';
                  }
               } else {
               }
               break;
            case 'registro':
               $dato[0] = ' WHERE id_us = ' . $this->getSql('id_us');
               $sql = $this->objM->m_consulta(4, $dato); // Consulta si el usuario existe
               $u = $this->db->query($sql);
               if ($u->num_rows == 0) {
                  // inserta usuario
                  $p = [
                     $this->getSql('id_us'),
                     $this->getSql('fk_doc'),
                     $this->getSql('firt_name'),
                     $this->getSql('second_name'),
                     $this->getSql('last_name'),
                     $this->getSql('email'),
                     null,
                     null,
                     $this->getSql('date_of_birth'),
                     $this->getSql('gender'),
                     'C'
                  ];
                  $dato[0] = ' users ';
                  $dato[1] = $p;
                  $sql = $this->objM->m_insert($dato);
                  $b = $r = $this->db->query($sql);
                  if ($b) {
                     echo '<script>alert("Se registro correctamente");</script>';
                  } else {
                     echo '<script>alert("error al registrar");</script>';
                  }
               } else {
                  $this->_view->user = $this->m_trae_array($u, 2);
                  echo '<script>alert("usted ya exite  en el sistema")</script>';
                  // usuario ya existe extrae los tados los envia a la vista y vista los llena en el formulario
                  // alert usted ya exite  en el sistema 
               }
               break;
            default:
               # code...
               break;
         }
      }


      $sql = $this->objM->m_consulta(2, '');
      $r = $this->db->query($sql);
      $this->_view->doc = $this->m_trae_array($r, 1);
      $this->_view->setCss(array('font-Montserrat', 'google', 'bootstrap.min', 'jav', 'animate', 'fontawesome-all'));
      //   $this->_view->setJs(array('jquery-1.9.0', 'bootstrap.min', 'popper.min', 'fontawasome-ico', 'cUsuariosJquery', 'tablesorter-master/jquery.tablesorter'));
      $this->_view->setJs(['cUsuariosJquery']);
      $this->_view->setCss(['style']);
      $this->_view->renderizar('formulario');
   }


   public function login(){
      if (isset($_POST) && !empty($_POST)) {
         $dato[0] = ' WHERE id_us =' . $this->getSql('id_us');
         $dato[1] = ' AND password = "' . $this->getSql('password') . '"';
         $sql =  $this->objM->m_consulta(4, $dato);
         $r = $this->db->query($sql);
         if ($r->num_rows != 0) {

            @session_start();
            $r = $this->m_trae_array($r, 2);
            $_SESSION['usuario'] = [];
            $_SESSION['usuario']['id_us']           = $r[0];
            $_SESSION['usuario']['fk_doc']          = $r[1];
            $_SESSION['usuario']['firt_name']       = $r[2];
            $_SESSION['usuario']['second_name']     = $r[3];
            $_SESSION['usuario']['last_name']       = $r[4];
            $_SESSION['usuario']['email']           = $r[5];
            $_SESSION['usuario']['img']             = $r[7];
            $_SESSION['usuario']['date_of_birth']   = $r[8];
            $_SESSION['usuario']['gender']          = $r[9];
            $_SESSION['usuario']['fk_rol']          = $r[10];
            $this->verificarAcceso();
         } else {
            die('paila');
         }
         $_POST = null;
      }
      $this->_view->setCss(['login']);
      $this->_view->renderizar('login', 1);
   }


   public function cerrar(){
      session_destroy();
      $this->redireccionar('index');
   }
}
