<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminSettingsController extends Controller
{
    private const MAIL_KEYS = [
        'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
        'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name',
    ];

    private const MAIL_CONFIG_MAP = [
        'mail_mailer'       => 'mail.default',
        'mail_host'         => 'mail.mailers.smtp.host',
        'mail_port'         => 'mail.mailers.smtp.port',
        'mail_username'     => 'mail.mailers.smtp.username',
        'mail_password'     => 'mail.mailers.smtp.password',
        'mail_encryption'   => 'mail.mailers.smtp.encryption',
        'mail_from_address' => 'mail.from.address',
        'mail_from_name'    => 'mail.from.name',
    ];

    public function mailForm()
    {
        try {
            $settings = Setting::whereIn('key', self::MAIL_KEYS)->get()->pluck('value', 'key');
        } catch (\Throwable) {
            $settings = collect();
        }
        return view('admin.settings.mail', compact('settings'));
    }

    public function updateMail(Request $request)
    {
        $data = $request->validate([
            'mail_mailer'       => 'required|in:smtp,mailgun,ses,sendmail,log',
            'mail_host'         => 'nullable|string|max:200',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:200',
            'mail_password'     => 'nullable|string|max:200',
            'mail_encryption'   => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email|max:200',
            'mail_from_name'    => 'required|string|max:100',
        ]);

        try {
            foreach ($data as $key => $value) {
                if ($key === 'mail_password' && ($value === null || $value === '')) {
                    continue;
                }
                Setting::set($key, $value);
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo guardar: ' . $e->getMessage() . ' — Ejecuta php artisan migrate primero.');
        }

        AuditService::log('actualizó', 'configuración', 'Actualizó configuración de correo SMTP');
        return back()->with('success', 'Configuración de correo guardada. Los cambios aplican en la próxima solicitud.');
    }

    public function testMail()
    {
        $email = auth()->user()->email;
        try {
            Mail::raw(
                "Correo de prueba enviado desde Pump Tracker — GRS S.A.S.\n\nSi recibiste este mensaje, la configuración SMTP está funcionando correctamente.",
                fn($msg) => $msg->to($email)->subject('✅ Prueba SMTP — Pump Tracker')
            );
            AuditService::log('envió', 'configuración', "Prueba de correo SMTP a {$email}");
            return back()->with('success', "Correo de prueba enviado a {$email}. Revisa tu bandeja de entrada.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }
}
