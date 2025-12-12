# 🚀 Déploiement Rapide sur Render - 5 Minutes

Guide ultra-rapide pour déployer Capocop sur Render.

## ⚡ Étapes Rapides

### 1. Push vers Git (1 min)

```bash
git add .
git commit -m "Configuration Render"
git push origin main
```

### 2. Créer le Blueprint sur Render (2 min)

1. Allez sur [dashboard.render.com](https://dashboard.render.com)
2. Cliquez **"New +"** → **"Blueprint"**
3. Connectez votre dépôt Git (GitHub/GitLab/Bitbucket)
4. Render détecte `render.yaml` automatiquement
5. Cliquez **"Apply"**

### 3. Configurer les variables essentielles (2 min)

Une fois déployé, ajoutez dans **Environment** :

```env
APP_URL=https://votre-app.onrender.com
SANCTUM_STATEFUL_DOMAINS=votre-app.onrender.com

# Email (optionnel pour commencer)
MAIL_MAILER=log
```

### 4. C'est tout ! ✅

Votre application est en ligne avec :
- ✅ Application Laravel
- ✅ MySQL 8.4
- ✅ Redis
- ✅ Worker pour les queues
- ✅ Cron job pour les tâches planifiées
- ✅ HTTPS automatique

## 📍 Accéder à votre application

URL : `https://capocop-app.onrender.com` (ou le nom que vous avez choisi)

## 🔧 Commandes utiles

```bash
# Installer Render CLI
npm install -g @render/cli

# Se connecter
render login

# Voir les logs
render logs -s capocop-app --tail

# Exécuter une commande
render exec -s capocop-app -- php artisan migrate

# Ouvrir un shell
render shell -s capocop-app
```

## 🐛 Problèmes ?

### L'app ne démarre pas
→ Vérifiez les logs : Dashboard → capocop-app → Logs

### Erreur "No APP_KEY"
→ Ajoutez dans Environment : `APP_KEY=base64:VotreClé`
→ Générez-la : `php artisan key:generate --show`

### Migrations ne fonctionnent pas
→ Dashboard → capocop-app → Shell :
```bash
php artisan migrate --force
```

## 📚 Documentation complète

Pour plus de détails, consultez [RENDER_DEPLOYMENT.md](./RENDER_DEPLOYMENT.md)

## 💰 Coûts

**Plan gratuit (pour tester)** :
- Web : Gratuit (s'arrête après 15 min d'inactivité)
- Worker : $7/mois
- MySQL : Gratuit (1 GB)
- Redis : Gratuit (25 MB)

**Total** : ~$7/mois

**Plan production** : ~$85/mois (services toujours actifs, plus de ressources)

## ⬆️ Mise à jour

Simple push Git = déploiement automatique :

```bash
git add .
git commit -m "Mise à jour"
git push origin main
# 🎉 Render redéploie automatiquement !
```

## 🎉 Terminé !

Votre application est en production sur Render ! 🚀

