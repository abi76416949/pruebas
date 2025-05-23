<?php
class UsuarioDAO extends Model implements CRUD
{
    const roleManager = 2;
    const CUSTOMER_INACTIVE = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $insertData = array(
            ':nombre' => $data['nombreUsuario'],
            ':apellido_paterno' => $data['apellidoPaternoUsuario'],
            ':apellido_materno' => $data['apellidoMaternoUsuario'],
            ':email' => $data['correoUsuario'],
            ':password_usuario' => sha1($data['password']),
            ':imagen' => $data['imagen'],
            ':calle' => $data['calleUsuario'],
            ':estado' => $data['estadoUsuario'],
            ':municipio' => $data['municipioUsuario'],
            ':colonia' => $data['coloniaUsuario'],
            ':codigo_postal' => $data['codigoPostalUsuario'],
            ':id_rol' => $data['rolUsuario'],
            ':is_active' => self::CUSTOMER_INACTIVE,
            ':telefono' => $data['telefonoUsuario'],
            ':is_email_notified' => false
        );
        $query ="INSERT INTO usuario values (NULL, 
                :nombre,
                :apellido_paterno,
                :apellido_materno,
                :email,
                :password_usuario,
                :imagen,
                :calle,
                :estado,
                :municipio,
                :colonia,
                :codigo_postal,
                :id_rol,
                :is_active,
                :telefono,
                :is_email_notified)";
        
        if ($this->db->ejecutarAccion($query, $insertData)) {
            return $this->db->getLastInsertId();
        }
    }

    public function insertGymAndPlanSistema($data)
    {
        $query = $this->db->conectar()->prepare('INSERT INTO usuario_gimnasio
            (id_usuario, id_gimnasio, id_plan_sistema, fecha_inicio, fecha_termino, estatus)
            VALUES
            (:id_usuario, :id_gimnasio, :id_plan_sistema, NOW(), NULL, NULL)');
        $query->execute([
            ':id_usuario' => $data['id_usuario'],
            ':id_gimnasio' => $data['id_gimnasio'],
            ':id_plan_sistema' => $data['id_plan_sistema'],
        ]);
        echo 'ok';
    }

    public function update($data)
    {
        $arrayActualizar = [
            ':id_usuario' => $data['id_usuario'],
            ':nombreUsuario' => $data['nombreUsuario'],
            ':apellidoPaternoUsuario' => $data['apellidoPaternoUsuario'],
            ':apellidoMaternoUsuario' => $data['apellidoMaternoUsuario'],
            ':telefonoUsuario' => $data['telefonoUsuario'],
            ':emailUsuario' => $data['emailUsuario'],
            ':calleUsuario' => $data['calleUsuario'],
            ':estadoUsuario' => $data['estadoUsuario'],
            ':municipioUsuario' => $data['municipioUsuario'],
            ':coloniaUsuario' => $data['coloniaUsuario'],
            ':codigoPostalUsuario' => $data['codigoPostalUsuario'],
            ':id_rol' => $data['id_rol']
        ];
        $query = $this->db->conectar()->prepare('UPDATE usuario SET 
            nombre = :nombreUsuario,
            apellido_paterno = :apellidoPaternoUsuario,
            apellido_materno = :apellidoMaternoUsuario,
            telefono = :telefonoUsuario,
            email = :emailUsuario,
            calle = :calleUsuario,
            estado = :estadoUsuario,
            municipio = :municipioUsuario,
            colonia = :coloniaUsuario,
            codigo_postal = :codigoPostalUsuario,
            id_rol = :id_rol
            WHERE id_usuario = :id_usuario');

        $query->execute($arrayActualizar);
        echo 'ok';
    }

    public function delete($id)
    {
        $query = $this->db->conectar()->prepare('DELETE FROM usuario where id_usuario = :id_usuario');
        $query->execute([':id_usuario' => $id]);
        echo 'ok';
    }

    public function read()
    {
        require_once 'usuarioDTO.php';
        $query = "SELECT
        u.id_usuario AS 'ID',
        u.id_rol,
        u.nombre,
        u.apellido_paterno,
        u.apellido_materno,
        u.telefono,
        u.email,
        u.password_usuario,
        u.imagen,
        u.calle,
        u.estado,
        u.municipio,
        u.colonia,
        u.codigo_postal,
        r.nombre_rol AS 'Rol',
        CASE
            WHEN r.nombre_rol = 'Administrador' THEN 'No aplica'
            WHEN ug.id_gimnasio IS NULL THEN 'Aun no se le asigna'
            ELSE g.nombre_gimnasio
        END AS nombreGimnasio,
        CASE
            WHEN r.nombre_rol = 'Administrador' THEN 'No aplica'
            WHEN ug.id_plan_sistema IS NULL THEN 'Aun no se le asigna'
            ELSE ps.nombre_plan_sistema
        END AS nombrePlanSistema,
        CASE
        WHEN (SELECT MIN(pps.vencimiento) FROM pago_plan_sistema pps WHERE pps.id_usuario = u.id_usuario AND pps.vencimiento > CURDATE()) IS NOT NULL THEN 1 ELSE 0
        END AS is_active
    FROM usuario u
    LEFT JOIN usuario_gimnasio ug ON u.id_usuario = ug.id_usuario
    LEFT JOIN gimnasio g ON ug.id_gimnasio = g.id_gimnasio
    LEFT JOIN plan_sistema ps ON ug.id_plan_sistema = ps.id_plan_sistema
    JOIN rol r ON u.id_rol = r.id_rol";

        $objUsuario = array();
        if (is_array($this->db->consultar($query)) || is_object($this->db->consultar($query))) {
        foreach ($this->db->consultar($query) as $key => $value) {
            $usuario = new UsuarioDTO();
            $usuario->id_usuario = $value['ID'];
            $usuario->id_rol = $value['id_rol'];
            $usuario->nombreUsuario = $value['nombre'];
            $usuario->apellidoPaternoUsuario = $value['apellido_paterno'];
            $usuario->apellidoMaternoUsuario = $value['apellido_materno'];
            $usuario->telefonoUsuario = $value['telefono'];
            $usuario->emailUsuario = $value['email'];
            $usuario->passwordUsuario = $value['password_usuario'];
            $usuario->imagen = $value['imagen'];
            $usuario->calleUsuario = $value['calle'];
            $usuario->estadoUsuario = $value['estado'];
            $usuario->municipioUsuario = $value['municipio'];
            $usuario->coloniaUsuario = $value['colonia'];
            $usuario->codigoPostalUsuario = $value['codigo_postal'];
            $usuario->nombreRol = $value['Rol'];
            $usuario-> nombre_gimnasio= $value['nombreGimnasio'];
            $usuario->nombre_plan_sistema = $value['nombrePlanSistema'];
            $usuario->is_active = $value['is_active'];
            array_push($objUsuario, $usuario);
        }
        }

        $objUsuario = array_values($objUsuario);
        return $objUsuario;
    }

public function login($data)
{
    require_once 'usuarioDTO.php';

    // Encriptar password
    $password = sha1($data['passwordUsuario']);
    ob_clean(); // 🔧 Limpia cualquier salida anterior (espacios, errores, HTML)
    header('Content-Type: application/json'); 
    // Preparar consulta
    $query = $this->db->prepare("
        SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.password_usuario, u.imagen, u.calle, u.estado, u.municipio, u.colonia, u.codigo_postal, u.id_rol, ug.id_gimnasio,
        (CASE WHEN u.id_rol = 2 AND EXISTS (
            SELECT 1 FROM pago_plan_sistema pps 
            WHERE pps.id_usuario = u.id_usuario AND pps.vencimiento > CURDATE()
        ) THEN 1 ELSE 0 END) as is_active
        FROM usuario AS u
        LEFT JOIN usuario_gimnasio AS ug ON u.id_usuario = ug.id_usuario
        WHERE u.email = :emailUsuario AND u.password_usuario = :passwordUsuario
    ");

    // Enlazar valores
    $query->bindParam(":emailUsuario", $data['emailUsuario']);
    $query->bindParam(":passwordUsuario", $password);
    $query->execute();

    // Obtener resultado
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    // Si encontró usuario
    if (count($result) === 1) {
        $row = $result[0];

        // Si es usuario rol 2 (cliente) pero sin pago activo
        if ($row['is_active'] === 0 && $row['id_rol'] === 2) {
            return array("warning" => true);
        }

        error_log("Login exitoso, datos del usuario: " . print_r($row, true));

        // ✅ Verifica si la sesión ya está activa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Guardar en sesión
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['id_gimnasio'] = $row['id_gimnasio'];
        $_SESSION['nombreUsuario'] = $row['nombre'];
        $_SESSION['apellidoPaternoUsuario'] = $row['apellido_paterno'];
        $_SESSION['apellidoMaternoUsuario'] = $row['apellido_materno'];
        $_SESSION['emailUsuario'] = $row['email'];
        $_SESSION['passwordUsuario'] = $row['password_usuario'];
        $_SESSION['imagen'] = $row['imagen'];
        $_SESSION['calleUsuario'] = $row['calle'];
        $_SESSION['estadoUsuario'] = $row['estado'];
        $_SESSION['municipioUsuario'] = $row['municipio'];
        $_SESSION['coloniaUsuario'] = $row['colonia'];
        $_SESSION['codigoPostalUsuario'] = $row['codigo_postal'];
        $_SESSION['id_rol'] = $row['id_rol'];
        $_SESSION['login'] = true;
        $_SESSION['permisos'] = $this->getPermisos($row['id_rol']);

        return array("success" => true);
    }

    // Si no encontró usuario
    return array("error" => "Usuario y Contraseña incorrectos");
}

    
    public function getPermisos($idrol)
    {
        require_once 'permisoDTO.php';
        $query = "SELECT p.id_rol, p.id_modulo, m.nombre_modulo AS modulo, m.icono, p.c, p.r, p.u, p.d, s.id_submodulo, s.nombre_submodulo AS submodulo, s.icono AS subicono
        FROM permiso p
        INNER JOIN modulo m ON p.id_modulo = m.id_modulo
        LEFT JOIN submodulo s ON s.id_modulo = m.id_modulo WHERE p.id_rol = " . $idrol . " ORDER BY m.posicion";
        $sql = $this->db->consultar($query);
        $arrPermisos = array();
        for ($i = 0; $i < count($sql); $i++) {
            $moduloId = $sql[$i]['id_modulo'];
            if (!isset($arrPermisos[$moduloId])) {
                $arrPermisos[$moduloId] = array(
                    'modulo' => $sql[$i]['modulo'],
                    'c' => $sql[$i]['c'],
                    'r' => $sql[$i]['r'],
                    'u' => $sql[$i]['u'],
                    'd' => $sql[$i]['d'],
                    'icono' => $sql[$i]['icono'],
                    'submodulos' => array()
                );
            }
            if ($sql[$i]['id_submodulo'] !== null) {
                $arrPermisos[$moduloId]['submodulos'][] = array(
                    'submodulo' => $sql[$i]['submodulo'],
                    'subicono' => $sql[$i]['subicono']
                );
            }
        }
        return $arrPermisos;
    }

    public function readUserManagersGym()
    {
        require_once 'usuarioDTO.php';
        $query = "SELECT usuario.id_usuario, usuario.nombre, usuario.apellido_paterno, usuario.apellido_materno
        FROM usuario
        INNER JOIN rol ON usuario.id_rol = rol.id_rol
        WHERE rol.id_rol = " . self::roleManager;
        $objUsuario = array();
        if (is_array($this->db->consultar($query)) || is_object($this->db->consultar($query))) {
            foreach ($this->db->consultar($query) as $key => $value) {
                $usuario = new UsuarioDTO();
                $usuario->id_usuario = $value['id_usuario'];
                $usuario->nombreUsuario = $value['nombre'];
                $usuario->apellidoPaternoUsuario = $value['apellido_paterno'];
                $usuario->apellidoMaternoUsuario = $value['apellido_paterno'];
                array_push($objUsuario, $usuario);
            }
        }else{
            $objUsuario=null;
        }
        return $objUsuario;
    }

    public function updateImage($data)
    {
        $insertData = array(
            ':id_user' => $data['id_user'],
            ':imagen' => $data['imageInput'],
        );

        $queryUpdateUser = "UPDATE usuario SET 
        imagen = :imagen
        WHERE id_usuario = :id_user";

        if ($this->db->ejecutarAccion($queryUpdateUser, $insertData)) {
            echo "ok";
        }
    }

    public function getUsersWithUpcomingMembershipExpiry()
    {
        $objCliente = array();
        require_once 'clienteDTO.php';
        $query = "SELECT u.*, pps.vencimiento, ps.nombre_plan_sistema
        FROM usuario as u
        INNER JOIN pago_plan_sistema as pps ON u.id_usuario = pps.id_usuario
        INNER JOIN plan_sistema as ps ON pps.id_plan_sistema = ps.id_plan_sistema
        WHERE pps.vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
        AND u.id_usuario NOT IN (
            SELECT id_usuario
            FROM pago_plan_sistema
            WHERE vencimiento > DATE_ADD(CURDATE(), INTERVAL 5 DAY)
        )";
        if (is_array($this->db->consultar($query)) || is_object($this->db->consultar($query))) {
            foreach ($this->db->consultar($query) as $key => $value) {
                $cliente = new ClienteDTO();
                $cliente->imagen_cliente = $value['imagen'];
                $cliente->id_cliente = $value['id_usuario'];
                $cliente->nombre_cliente = $value['nombre'];
                $cliente->apellido_paterno_cliente = $value['apellido_paterno'];
                $cliente->apellido_materno_cliente = $value['apellido_materno'];
                $cliente->numero_cliente = $value['telefono'];
                $cliente->nombrePlanGym = $value['nombre_plan_sistema'];
                $cliente->fecha_vencimiento = $value['vencimiento'];
                $cliente->email_customer = $value['email'];
                $cliente->is_email_notified = $value['is_email_notified'];
                $objCliente[$cliente->id_cliente] = $cliente;
            }
        }

        $objCliente = array_values($objCliente);
        return $objCliente;
    }

    public function updatePassword($data)
    {
        $arrayUpdate = [
            ':idUser' => $data['idUser'],
            ':password' => sha1($data['newPassword']),
        ];
        $query ="UPDATE usuario SET password_usuario = :password WHERE id_usuario = :idUser";
        
        if ($this->db->ejecutarAccion($query, $arrayUpdate)) {
            echo "ok";
        }
    }
}
?>
