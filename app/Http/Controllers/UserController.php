<?php

namespace App\Http\Controllers;

use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\User;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Mail;
use Str;

class UserController extends Controller
{
    public function registerUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name'     => 'required',
                'email'    => 'required',
                'phone'    => 'required',
                'password' => 'nullable',
            ]);

            $userExist = User::select('id')->where('email', $request->email)->first();

            if ($userExist) {
                Log::info('Tentativa de cadastro de usuário já existente:' . json_encode($request->all()));
                return response()->json(['message' => 'Usuário já registrado.'], 400);
            }

            $validatePhone = User::select('id')->where('phone', $request->phone)->first();

            if ($userExist) {
                Log::info('Tentativa de cadastro de usuário com telefone já existente:' . json_encode($request->all()));
                return response()->json(['message' => 'O número de telefone fornecido já está sendo usado por outro usuário. Por favor, tente novamente com outro número de telefone.'], 400);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'phone'    => $request->phone,
            ]);

            DB::commit();
            Log::info('Usuário registrado com sucesso: ' . json_encode($user->toArray()));

            return response()->json(['message' => 'Usuário registrado com sucesso.'], 201);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::info('Erro ao registrar usuário: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Erro ao registrar usuário, verifique os dados e tente novamente.'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro interno: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro, verifique os dados e tente novamente.'], 400);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = auth()->user();

            foreach ($request->all() as $key => $value) {
                if (!empty($value) && !in_array($key, ['name', 'email', 'password', 'phone'])) {
                    $user->$key = $value;
                }
            }

            $user->save();

            return response()->json(['message' => 'Usuário alterado com sucesso.'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao alterar os dados do usuário: ' . json_encode($e->getMessage()));

            return response()->json(['message' => 'Erro ao atualizar dados.'], 400);
        }
    }

    public function getAccessCode($request)
    {
        try {
            $request->validate([
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::info('Usuário não encontrado para o e-mail informado: ' . $request->email);
                return response()->json(['message' => 'Usuário não encontrado para o e-mail informado.'], 400);
            }

            $temporaryAccessCode = AccessCode::where('user_id', $user->id)
                ->where('expiration_time', '>', now())
                ->first();

            if ($temporaryAccessCode && !$temporaryAccessCode->isExpired()) {
                Log::info('Tentativa de envio de código de acesso para usuário com código não expirado: ' . $request->email);
                return response()->json(['message' => 'Um código de acesso foi enviado recentemente, aguarde alguns instantes antes de solicitar um novo código.'], 400);
            }

            $accessCode     = Str::random(6);
            $expirationTime = 2;
            $expiration     = now()->addMinutes($expirationTime);

            if ($temporaryAccessCode) {
                $temporaryAccessCode->update([
                    'access_code' => $accessCode,
                    'expires_at'  => $expiration,
                ]);
            } else {
                $temporaryAccessCode = AccessCode::create([
                    'user_id'     => $user->id,
                    'access_code' => $accessCode,
                    'expires_at'  => $expiration,
                ]);
            }

            Mail::to($user->email)->send(new AccessCodeMail($temporaryAccessCode));

            Log::info('Código de acesso enviado com sucesso para o usuário ' . $user->email);

            return response()->json(['message' => 'Código de acesso enviado com sucesso.'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar código de acesso: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao enviar código de acesso.'], 400);
        }
    }

    public function updatePasswordWithAccessCode(Request $request)
    {
        try {
            $request->validate([
                'access_code'  => 'required',
                'new_password' => 'required|min:8',
            ]);

            $accessCode = AccessCode::where('access_code', $request->access_code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$accessCode || $accessCode->isExpired()) {
                Log::info('Tentativa de atualização de senha com um código inválido ou expirado. UserId: ' . $accessCode->user_id);
                return response()->json(['message' => 'Código de acesso inválido ou expirado.'], 400);
            }

            $user = User::find($accessCode->user_id);

            if (!$user) {
                Log::warning('Usuário não encontrado durante a atualização de senha. UserId: ' . $accessCode->user_id);
                return response()->json(['message' => 'Usuário não encontrado.'], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            $accessCode->delete();

            Log::info('Senha do usuário atualizada com sucesso. UserId: ' . $accessCode->user_id);

            return response()->json(['message' => 'Senha atualizada com sucesso.'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar senha: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao atualizar senha.'], 400);
        }
    }

    public function updatePassword()
    {
        try {
            $dataPassword = request(['credentials', 'new_password']);

            if (!auth()->validate($dataPassword['credentials'])) {
                return response()->json(['error' => 'Senha atual incorreta'], 400);
            }

            $authUser = auth()->user();

            $user = User::find($authUser->id);

            $user->password = Hash::make($dataPassword['new_password']);

            $user->save();

            Log::info('Senha do usuário atualizada com sucesso. UserId: ' . $user->id);

            return response()->json(['message' => 'Senha alterada com sucesso.'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar senha: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao atualizar senha.'], 400);
        }
    }
}
