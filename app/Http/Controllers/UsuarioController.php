<?php

namespace App\Http\Controllers;

use App\Mail\SendStoreUsuario;
use App\Mensaje;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $usuario = Usuario::all();
        return response()->json([
            'res' => true,
            'usuario' => $usuario
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * Función que actualiza el campo send_email a 1
     * cuando no hay errores en el envío del correo
     * se utiliza la clase DB de laravel para hacer la consulta
     * como otra alternativa para interactuar con la base de datos y que porque se 
     * @param int $id id del usuario
     */
    private function updateSendEmail($id)
    {
        try {
            DB::table('usuarios')
                ->where('id', $id)
                ->update(['send_email' => 1]);
        } catch (\Throwable $th) {
            //  alamacenar en log de errores
        }
    }

    /**
     * Función que selecciona todos los mensajes de la base de datos
     */
    public function selectAllMessages()
    {
        $query = "SELECT u.id AS id_usuario, u.name, u.email, u.phone, u.created_at, u.send_email, m.message, m.created_at as created_at_message FROM usuarios u
        INNER JOIN mensajes m ON m.id_usuario= u.id";
        try {
            $select = DB::select($query);
            return response()->json($select, 200);

        } catch (\Throwable $th) {
            //  alamacenar en log de errores
            return response()->json([
                'res' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'res' => false,
                'error' => $validator->errors()
            ], 500);
        }

        // validar si el usuario ya existe por el correo
        $usuario = Usuario::where('email', $request->input('email'))->first();
        if (!is_object($usuario)) {
            $usuario = new Usuario();
            $usuario->name = $request->input('name');
            $usuario->email = $request->input('email');
            $usuario->phone = $request->input('phone');
            $usuario->save();
        }
        $mensaje = new Mensaje();
        $mensaje->id_usuario = $usuario->id;
        $mensaje->message = $request->input('message');
        $mensaje->save();


        $detail = array("nombre" => $usuario->name);
        try {
            Mail::to($usuario->email)->send(new SendStoreUsuario($detail));
            $this->updateSendEmail($usuario->id);
        } catch (\Throwable $th) {
            //  alamacenar en log de errores
        }




        return response()->json([
            'res' => true,
            'message' => 'Usuario creado correctamente'
        ], 201);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //obtener el usuario por su id
        $usuario = Usuario::find($id);
        if (is_object($usuario)) {
            $data = [
                'res' => true,
                'usuario' => $usuario
            ];
        } else {
            $data = [
                'res' => false,
                'message' => 'No se encontro el usuario'
            ];
        }
        return response()->json($data, 200);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //validar los datos

        $validator = validator($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'res' => false,
                'error' => $validator->errors()
            ], 400);
        }

        //actualizar usuario por su id
        $usuario = Usuario::find($id);
        if (is_object($usuario)) {
            $usuario->name = $request->input('name');
            $usuario->email = $request->input('email');
            $usuario->phone = $request->input('phone');
            $usuario->save();
            $data = [
                'res' => true,
                'message' => 'Usuario actualizado correctamente'
            ];
        } else {
            $data = [
                'res' => false,
                'message' => 'No se encontro el usuario'
            ];
        }
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //eliminar un usuario por su id
        $usuario = Usuario::find($id);
        if (is_object($usuario)) {
            $usuario->delete();
            $data = [
                'res' => true,
                'message' => 'Usuario eliminado correctamente'
            ];
        } else {
            $data = [
                'res' => false,
                'message' => 'No se encontro el usuario'
            ];
        }
        return response()->json($data, 200);
    }
}