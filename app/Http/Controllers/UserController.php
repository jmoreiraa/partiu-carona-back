<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->middleware('auth:sanctum')->only('index', 'visible', 'update');
    }

    public function index(Request $request) {
        $user = $request->user();
        $motoristas = $this->user->where([
            ['origem', '=', $user->origem],
            ['destino', '=', $user->destino],
            ['visible', '=', True],
            ['motorista', '=', True]
        ])->paginate(10);
        return $motoristas;
    }

    public function visible(Request $request) {
        $user = $request->user();
        $user['visible'] = !$user->visible;
        $user->save();
        $user = $request->user();
        $token = $user->createToken($request->header('User-Agent'));
        $user['token'] = $token->plainTextToken;
        return $user;
    }

    public function update(Request $request) {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'max:32|min:2|string',
            'whatsapp' => '',
            'npessoas' => '',
            'placa' => '',
            'carro' => '',
            'origem' => '',
            'destino' => '',
            'data' => '',
            'horario' => '',
        ]);
        $user->update($validated);
        $user = $request->user();
        $token = $user->createToken($request->header('User-Agent'));
        $user['token'] = $token->plainTextToken;
        return $user;
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $user = $this->user->where('email', $validated['email'])->first();
        if (!$user) {
            return abort(404);
        }
        if (Hash::check($validated['password'], $user->password)) {
            $token = $user->createToken($request->header('User-Agent'));
            $user['token'] = $token->plainTextToken;
            return $user;
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }

    public function motorista(Request $request){
        $validated = $request->validate([
            'name' => 'required|max:32|min:2|string',
            'email' => 'required|email|string|unique:App\Models\User,email',
            'whatsapp' => 'required',
            'password' => 'required|string|max:32|min:8|regex:/^\S*$/u',
            'npessoas' => 'required',
            'placa' => 'required',
            'carro' => 'required',
            'origem' => 'required',
            'destino' => 'required',
            'data' => 'required',
            'horario' => 'required',
        ]);

        // VERIFICANDO SE USUÁRIO TEM +18
        $VerificaIdade = explode('-', $validated['data']);
        $Ano = $VerificaIdade[0];
        $Mes = $VerificaIdade[1];
        $Dia = $VerificaIdade[2];

        // UNIX timestamp
        $Nascimento = mktime(0, 0, 0, $Dia, $Mes, $Ano);

        // fetch the current date (minus 18 years)
        $Verifica['Dia'] = date('d');
        $Verifica['Mes'] = date('m');
        $Verifica['Ano'] = date('Y') - 18;

        // Timestamp
        $Hoje = mktime(0, 0, 0, $Verifica['Dia'], $Verifica['Mes'], $Verifica['Ano']);

        if ($Nascimento > $Hoje) abort(403);

        $validated['password'] = Hash::make($validated['password'], [
            'rounds' => 12,
        ]);

        $validated['motorista'] = True;


        return $this->user->create($validated);
    }


    public function usuario(Request $request){
        $validated = $request->validate([
            'name' => 'required|max:32|min:2|string',
            'email' => 'required|email|string|unique:App\Models\User,email',
            'whatsapp' => 'required',
            'password' => 'required|string|max:32|min:8|regex:/^\S*$/u',
            'origem' => 'required',
            'destino' => 'required',
            'data' => 'required',
            'horario' => 'required',
        ]);

        // VERIFICANDO SE USUÁRIO TEM +18
        $VerificaIdade = explode('-', $validated['data']);
        $Ano = $VerificaIdade[0];
        $Mes = $VerificaIdade[1];
        $Dia = $VerificaIdade[2];

        // UNIX timestamp
        $Nascimento = mktime(0, 0, 0, $Dia, $Mes, $Ano);

        // fetch the current date (minus 18 years)
        $Verifica['Dia'] = date('d');
        $Verifica['Mes'] = date('m');
        $Verifica['Ano'] = date('Y') - 18;

        // Timestamp
        $Hoje = mktime(0, 0, 0, $Verifica['Dia'], $Verifica['Mes'], $Verifica['Ano']);

        if ($Nascimento > $Hoje) abort(403);

        $validated['password'] = Hash::make($validated['password'], [
            'rounds' => 12,
        ]);

        $validated['motorista'] = False;

        return $this->user->create($validated);
    }
}
