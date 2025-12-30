<?php

return [
    'actions' => [
        'create' => 'Créer',
        'edit' => 'Modifier',
        'view' => 'Voir',
        'delete' => 'Supprimer',
        'restore' => 'Restaurer',
        'force_delete' => 'Forcer la suppression',
        'replicate' => 'Dupliquer',
        'export' => 'Exporter',
        'import' => 'Importer',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'close' => 'Fermer',
        'submit' => 'Soumettre',
        'back' => 'Retour',
        'next' => 'Suivant',
        'previous' => 'Précédent',
        'search' => 'Rechercher',
        'filter' => 'Filtrer',
        'reset' => 'Réinitialiser',
        'apply' => 'Appliquer',
        'clear' => 'Effacer',
        'select_all' => 'Tout sélectionner',
        'deselect_all' => 'Tout désélectionner',
        'bulk_actions' => 'Actions groupées',
    ],

    'resources' => [
        'create' => 'Créer :resource',
        'edit' => 'Modifier :resource',
        'view' => 'Voir :resource',
        'delete' => 'Supprimer :resource',
        'restore' => 'Restaurer :resource',
        'force_delete' => 'Forcer la suppression de :resource',
        'replicate' => 'Dupliquer :resource',
        'export' => 'Exporter :resource',
        'import' => 'Importer :resource',
        'list' => 'Liste des :resource',
        'new' => 'Nouveau :resource',
        'edit_record' => 'Modifier l\'enregistrement',
        'view_record' => 'Voir l\'enregistrement',
        'delete_record' => 'Supprimer l\'enregistrement',
        'restore_record' => 'Restaurer l\'enregistrement',
        'force_delete_record' => 'Forcer la suppression de l\'enregistrement',
        'replicate_record' => 'Dupliquer l\'enregistrement',
    ],

    'pages' => [
        'create' => 'Créer',
        'edit' => 'Modifier',
        'view' => 'Voir',
        'list' => 'Liste',
        'dashboard' => 'Tableau de bord',
    ],

    'tables' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nom',
            'email' => 'Email',
            'created_at' => 'Créé le',
            'updated_at' => 'Modifié le',
            'deleted_at' => 'Supprimé le',
            'actions' => 'Actions',
        ],
        'empty' => [
            'heading' => 'Aucun enregistrement trouvé',
            'description' => 'Créez votre premier :resource pour commencer.',
        ],
        'search' => 'Rechercher...',
        'filters' => 'Filtres',
        'no_results' => 'Aucun résultat trouvé.',
        'per_page' => 'Par page',
        'selected' => 'sélectionné(s)',
    ],

    'forms' => [
        'fields' => [
            'name' => 'Nom',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'password_confirmation' => 'Confirmer le mot de passe',
            'remember_me' => 'Se souvenir de moi',
        ],
        'validation' => [
            'required' => 'Ce champ est obligatoire.',
            'email' => 'Ce champ doit être une adresse email valide.',
            'min' => 'Ce champ doit contenir au moins :min caractères.',
            'max' => 'Ce champ ne peut pas contenir plus de :max caractères.',
            'confirmed' => 'La confirmation ne correspond pas.',
            'unique' => 'Cette valeur est déjà utilisée.',
        ],
    ],

    'notifications' => [
        'created' => ':resource créé avec succès.',
        'updated' => ':resource modifié avec succès.',
        'deleted' => ':resource supprimé avec succès.',
        'restored' => ':resource restauré avec succès.',
        'force_deleted' => ':resource supprimé définitivement.',
        'replicated' => ':resource dupliqué avec succès.',
        'saved' => 'Enregistré avec succès.',
        'error' => 'Une erreur est survenue.',
    ],

    'modals' => [
        'delete' => [
            'heading' => 'Supprimer :resource',
            'description' => 'Êtes-vous sûr de vouloir supprimer cet enregistrement ? Cette action ne peut pas être annulée.',
            'cancel' => 'Annuler',
            'delete' => 'Supprimer',
        ],
        'force_delete' => [
            'heading' => 'Forcer la suppression de :resource',
            'description' => 'Êtes-vous sûr de vouloir supprimer définitivement cet enregistrement ? Cette action ne peut pas être annulée.',
            'cancel' => 'Annuler',
            'delete' => 'Supprimer définitivement',
        ],
        'restore' => [
            'heading' => 'Restaurer :resource',
            'description' => 'Êtes-vous sûr de vouloir restaurer cet enregistrement ?',
            'cancel' => 'Annuler',
            'restore' => 'Restaurer',
        ],
    ],

    'navigation' => [
        'dashboard' => 'Tableau de bord',
        'menu' => 'Menu',
        'user_menu' => 'Menu utilisateur',
        'logout' => 'Déconnexion',
        'profile' => 'Profil',
    ],
];


