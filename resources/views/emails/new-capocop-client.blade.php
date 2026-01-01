<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez Capocop</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #002E5B 0%, #00B600 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 20px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .intro-text {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .highlight-box {
            background: linear-gradient(135deg, rgba(0, 46, 91, 0.1) 0%, rgba(0, 182, 0, 0.1) 100%);
            border-left: 4px solid #00B600;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .highlight-box-title {
            font-size: 18px;
            font-weight: 700;
            color: #00B600;
            margin-bottom: 15px;
        }
        .steps-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .steps-list li {
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: flex-start;
        }
        .steps-list li:last-child {
            border-bottom: none;
        }
        .step-number {
            background: linear-gradient(135deg, #002E5B 0%, #00B600 100%);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
            margin-right: 15px;
        }
        .step-content {
            flex: 1;
        }
        .step-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
            font-size: 16px;
        }
        .step-description {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }
        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .info-box-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info-box-text {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #002E5B 0%, #00B600 100%);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 4px 6px rgba(0, 46, 91, 0.3);
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #001C37 0%, #009200 100%);
        }
        .contact-section {
            background-color: #f9fafb;
            padding: 30px;
            margin-top: 40px;
            border-radius: 8px;
            text-align: center;
        }
        .contact-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .contact-info {
            color: #6b7280;
            font-size: 14px;
            margin: 8px 0;
        }
        .contact-info a {
            color: #002E5B;
            text-decoration: none;
            font-weight: 600;
        }
        .contact-info a:hover {
            color: #00B600;
            text-decoration: underline;
        }
        .footer {
            background-color: #1f2937;
            color: #9ca3af;
            padding: 30px;
            text-align: center;
            font-size: 12px;
        }
        .footer-text {
            margin: 5px 0;
        }
        .footer-link {
            color: #00B600;
            text-decoration: none;
        }
        .footer-link:hover {
            color: #002E5B;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            .email-header {
                padding: 30px 20px;
            }
            .greeting {
                font-size: 20px;
            }
            .cta-button {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <img src="{{ $logoUrl }}" alt="Capocop" class="logo">
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Bienvenue {{ $user->name ?? 'Cher client' }} ! 🎉
            </div>

            <div class="intro-text">
                Nous sommes ravis de vous accueillir dans la communauté <strong>Capocop</strong> ! Votre commande <strong>#{{ substr($order->id, 0, 8) }}</strong> a été enregistrée avec succès.
            </div>

            <div class="highlight-box">
                <div class="highlight-box-title">
                    ✨ Votre compte Capocop est en cours de création
                </div>
                <p style="color: #6b7280; font-size: 14px; margin: 0; line-height: 1.6;">
                    Un compte Capocop Pay a été automatiquement créé avec votre adresse email <strong>{{ $user->email }}</strong>. Pour finaliser votre compte et profiter pleinement de nos services, suivez les étapes ci-dessous.
                </p>
            </div>

            <div style="margin: 40px 0;">
                <h2 style="font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 25px;">
                    📋 Étapes pour finaliser votre compte Capocop
                </h2>

                <ul class="steps-list">
                    <li>
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-title">Vérifiez votre email</div>
                            <div class="step-description">
                                Vous recevrez sous peu un email de confirmation avec vos identifiants de connexion (identifiant Capocop et mot de passe temporaire). Vérifiez également vos spams si nécessaire.
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="step-number">2</div>
                            <div class="step-content">
                            <div class="step-title">Connectez-vous à votre compte</div>
                            <div class="step-description">
                                Rendez-vous sur <a href="{{ url('/') }}" style="color: #002E5B; text-decoration: none; font-weight: 600;">capocop.com</a> et connectez-vous avec les identifiants reçus. Vous pourrez ensuite accéder à votre espace client et gérer vos commandes.
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-title">Complétez votre profil</div>
                            <div class="step-description">
                                Dans votre espace client, complétez vos informations personnelles (adresse, téléphone, etc.) pour faciliter vos prochaines commandes et bénéficier d'une expérience optimale.
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <div class="step-title">Activez Capocop Pay</div>
                            <div class="step-description">
                                Une fois connecté, activez votre portefeuille Capocop Pay pour effectuer des paiements rapides et sécurisés lors de vos prochaines commandes. Vous pourrez également recharger votre compte et suivre vos transactions.
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <div class="step-title">Suivez votre commande</div>
                            <div class="step-description">
                                Consultez le statut de votre commande en temps réel depuis votre espace client. Vous recevrez des notifications à chaque étape de traitement et de livraison.
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="info-box">
                <div class="info-box-title">
                    💡 Avantages de votre compte Capocop
                </div>
                <div class="info-box-text">
                    <ul style="margin: 10px 0; padding-left: 20px; color: #6b7280;">
                        <li style="margin: 8px 0;">Paiements rapides et sécurisés avec Capocop Pay</li>
                        <li style="margin: 8px 0;">Paiement échelonné jusqu'à 12 mois</li>
                        <li style="margin: 8px 0;">Suivi en temps réel de vos commandes</li>
                        <li style="margin: 8px 0;">Historique complet de vos achats</li>
                        <li style="margin: 8px 0;">Offres exclusives et codes promo</li>
                        <li style="margin: 8px 0;">Service client dédié et réactif</li>
                    </ul>
                </div>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ url('/myAccount') }}" class="cta-button">
                    Accéder à mon espace client
                </a>
            </div>

            <div class="divider"></div>

            <div class="info-box">
                <div class="info-box-title">
                    📦 Détails de votre commande
                </div>
                <div class="info-box-text">
                    <p style="margin: 5px 0;"><strong>Numéro de commande :</strong> #{{ substr($order->id, 0, 8) }}</p>
                    <p style="margin: 5px 0;"><strong>Montant total :</strong> {{ number_format($order->total_amount, 0, ',', ' ') }} XAF</p>
                    <p style="margin: 5px 0;"><strong>Méthode de paiement :</strong> Capocop Pay</p>
                    <p style="margin: 5px 0;"><strong>Statut :</strong> En attente de traitement</p>
                </div>
            </div>

            <div class="contact-section">
                <div class="contact-title">
                    Besoin d'aide ? Nous sommes là pour vous !
                </div>
                <div class="contact-info">
                    📧 Email : <a href="mailto:infos@capocop.com">infos@capocop.com</a>
                </div>
                <div class="contact-info">
                    📞 Téléphone : <a href="tel:+237695664661">+237 695 66 46 61</a>
                </div>
                <div class="contact-info">
                    📍 Adresse : Penja, Cameroun
                </div>
                <div class="contact-info" style="margin-top: 15px;">
                    Notre équipe est disponible du lundi au samedi de 8h à 18h pour répondre à toutes vos questions.
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                <strong style="color: #ffffff;">Capocop</strong> - Votre partenaire de confiance pour l'énergie domestique et les fournitures au Cameroun
            </p>
            <p class="footer-text">
                © {{ date('Y') }} Sonitelecom. Tous droits réservés.
            </p>
            <p class="footer-text" style="margin-top: 15px;">
                <a href="{{ url('/') }}" class="footer-link">Visiter notre site</a> | 
                <a href="mailto:contact@capocop.com" class="footer-link">Nous contacter</a>
            </p>
        </div>
    </div>
</body>
</html>

