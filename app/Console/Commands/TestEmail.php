<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste la configuration email en envoyant un email de test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Quelle adresse email utiliser pour le test ?');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse email invalide !');
            return 1;
        }

        $this->info('📧 Configuration email actuelle :');
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['MAIL_MAILER', Config::get('mail.default')],
                ['MAIL_HOST', Config::get('mail.mailers.smtp.host')],
                ['MAIL_PORT', Config::get('mail.mailers.smtp.port')],
                ['MAIL_USERNAME', Config::get('mail.mailers.smtp.username') ? '***' : 'Non défini'],
                ['MAIL_FROM_ADDRESS', Config::get('mail.from.address')],
                ['MAIL_FROM_NAME', Config::get('mail.from.name')],
            ]
        );

        if (Config::get('mail.default') === 'log') {
            $this->warn('⚠️  ATTENTION : MAIL_MAILER est sur "log". Les emails seront enregistrés dans les logs, pas envoyés !');
        }

        $this->info("\n📤 Envoi de l'email de test à : {$email}");

        try {
            Mail::raw('Ceci est un email de test depuis Capocop. Si vous recevez ce message, votre configuration email fonctionne correctement !', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test de configuration email - Capocop');
            });

            $this->info('✅ Email envoyé avec succès !');
            
            if (Config::get('mail.default') === 'log') {
                $this->info('📝 Vérifiez le fichier storage/logs/laravel.log pour voir l\'email.');
            } else {
                $this->info('📬 Vérifiez votre boîte de réception (et les spams).');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi de l\'email :');
            $this->error($e->getMessage());
            
            $this->newLine();
            $this->warn('💡 Suggestions :');
            $this->line('1. Vérifiez vos identifiants SMTP dans le fichier .env');
            $this->line('2. Vérifiez que le port SMTP n\'est pas bloqué');
            $this->line('3. Pour le développement, utilisez Mailtrap ou MAIL_MAILER=log');
            
            return 1;
        }
    }
}

