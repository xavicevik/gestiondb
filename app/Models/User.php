<?php

namespace App\Models;

use App\Http\Controllers\VentaController;
use App\Mail\Notificaciones;
use App\Notifications\EmailcodeNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Auth\CanResetPassword;


class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nombre', 'email', 'username','password', 'apellido', 'idtipos_documento', 'idrol', 'estado',
        'documento', 'direccion', 'indicativo', 'idpais', 'iddepartamento', 'idciudad', 'observaciones',
        'telefono', 'movil', 'isnatural', 'camaracomercio', 'rut', 'url', 'idempresa', 'changedpassword'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'full_name'
    ];


    public function scopeUserApp($query) {
        return $query->where('idrol', '<>', '2')->where('idrol', '<>', '6');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'idempresa');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idrol');
    }

    public function tipodocumento()
    {
        return $this->belongsTo(TiposDocumento::class, 'idtipos_documento');
    }

    public function pais(){
        return $this->belongsTo(Pais::class, 'idpais');
    }

    public function departamento(){
        return $this->belongsTo(Departamento::class, 'iddepartamento');
    }

    public function ciudad(){
        return $this->belongsTo(Ciudad::class, 'idciudad');
    }

    public function AllPermissions(): Attribute {
        $permissions = [];
        foreach ($this->getAllPermissions() as $permission) {
                $permissions[] = $permission->name;
        }
        return Attribute::make (
            get: fn () => $permissions,
        );
    }

    public function getFullNameAttribute()
    {
        return ucwords("{$this->nombre} {$this->apellido}");
    }

    public function generateCode()
    {
        $code = rand(1000, 9999);

        Usercode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'code' => $code ]
        );

        $receiverNumber = auth()->user()->movil;
        $message = "Codigo de acceso: ". $code;

        try {
            $to = '573155665528'; //$receiverNumber
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic QWRhbW1Tb2x1Y2lvbmVzX0JfMVdFOjZpW3pMRVEkTWI=',
            ])->post("https://api-sms.masivapp.com/send-message", [
                "to" => $to,
                "text" => $message,
                "isPremium" => false,
                "isFlash" => false,
                "isLongmessage" => false,
                "isRandomRoute" => false
            ]);

        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }

    public function sendemail()
    {
        $code = rand(1000, 9999);

        Usercode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'code' => $code ]
        );

        $receiverNumber = auth()->user()->movil;
        $message = "Codigo de acceso: ". $code;

        try {

            auth()->user()->notify(new EmailcodeNotification($code));

        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }
}
