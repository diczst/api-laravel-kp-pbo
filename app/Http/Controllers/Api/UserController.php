<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
    // responses
    public function success($data, $message = 'Success'){
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function error($message = 'Terjadi kesalahan'){
        return response()->json([
            'code' => 400,
            'message' => $message
        ], 400); 
    }

    public function index(){
        $users = User::orderBy('exp', 'asc')->get();
        return $this->success($users);
    }

    public function store(Request $request){
        $validation = Validator::make($request->all(), [
            'nama_lengkap' => 'required',
            'nisn' => 'required|unique:users',
            'no_hp' => 'required|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validation->errors()->first()
            ], 400); 
        }

        // jika user ada upload gambar
        if($request->file('image')){
            $image = $request->image->getClientOriginalName();  
            $image = str_replace(' ', '', $image);
            $image = date('Hs').rand(1,999) . "_" . $image;
            $request->image->storeAs('public/user', $image);
            $fileName = $image;
        } else {
            $fileName = null;
        }

        $user =  User::create(array_merge($request->all(), [
            'password' => bcrypt($request->password),
            'image' => $fileName
            ])
         );

        if($user){
            return $this->success($user, "Selamat datang $user->name");
        } else {
            return $this->error();
        }
    }

    public function login(Request $request){
        $validation = Validator::make($request->all(), [
            'nisn' => 'required',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->error($validation->errors()->first());
        }

        $user = User::where('nisn', $request->nisn)->first();
        if($user){
            if(password_verify($request->password, $user->password)){
                return $this->success($user);
            } else {
                return $this->error('Password salah');
            }
        }
        return $this->error("NIK atau password salah");
    }

    public function show($id){
        $user = User::where('id', $id)->first();
        return $this->success($user);
    }

    public function update(Request $request, $id){
        $user = User::where('id', $id)->first();
        if($user){
            $user->update($request->all());
            return $this->success($user);
        }
        return $this->error("User tidak ditemukan");
    }

    public function uploadImage(Request $request, $id){
        $user = User::where('id', $id)->first();
        if($user){
            $fileName = "";
            if($request->image){
                // dapatkan nama file
                $image = $request->image->getClientOriginalName();  

                // format nama dengan menghilangkan spasi
                $image = str_replace(' ', '', $image);

                // membuat agar nama file tidak ada yang sama saat terupload
                $image = date('Hs').rand(1,999) . "_" . $image;

                $fileName = $image;
                $request->image->storeAs('public/user', $image);
            } else {
                return $this->error('Image wajib diberikan');
            }

            $user->update([
                'image' => $fileName
            ]);
            return $this->success($user);
        }
        return $this->error("User tidak ditemukan");
    }

}
