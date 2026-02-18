<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\TranslationKey;
use App\Models\Translation;

class TranslationSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Languages
        $languages = [
            'en' => ['name' => 'English', 'is_active' => true],
            'fr' => ['name' => 'Français', 'is_active' => true],
        ];

        $langModels = [];
        foreach ($languages as $code => $data) {
            $langModels[$code] = Language::updateOrCreate(
                ['code' => $code],
                ['name' => $data['name'], 'is_active' => $data['is_active']]
            );
        }

        // 2) Keys + translations (group => [ key => [en=>..., fr=>...] ])
        $data = [
            'auth' => [
                // Login
                'auth.login.title' => [
                    'en' => 'Login',
                    'fr' => 'Connexion',
                ],
                'auth.login.email' => [
                    'en' => 'Email address',
                    'fr' => 'Adresse e-mail',
                ],
                'auth.login.password' => [
                    'en' => 'Password',
                    'fr' => 'Mot de passe',
                ],
                'auth.login.remember' => [
                    'en' => 'Remember me',
                    'fr' => 'Se souvenir de moi',
                ],
                'auth.login.forgot' => [
                    'en' => 'Forgot your password?',
                    'fr' => 'Mot de passe oublié ?',
                ],
                'auth.login.submit' => [
                    'en' => 'Sign in',
                    'fr' => 'Se connecter',
                ],
                'auth.login.no_account' => [
                    'en' => "Don't have an account?",
                    'fr' => "Vous n'avez pas de compte ?",
                ],
                'auth.login.register_link' => [
                    'en' => 'Create account',
                    'fr' => 'Créer un compte',
                ],

                // Register
                'auth.register.title' => [
                    'en' => 'Create account',
                    'fr' => 'Créer un compte',
                ],
                'auth.register.name' => [
                    'en' => 'Full name',
                    'fr' => 'Nom complet',
                ],
                'auth.register.email' => [
                    'en' => 'Email address',
                    'fr' => 'Adresse e-mail',
                ],
                'auth.register.password' => [
                    'en' => 'Password',
                    'fr' => 'Mot de passe',
                ],
                'auth.register.password_confirm' => [
                    'en' => 'Confirm password',
                    'fr' => 'Confirmer le mot de passe',
                ],
                'auth.register.submit' => [
                    'en' => 'Register',
                    'fr' => "S'inscrire",
                ],
                'auth.register.have_account' => [
                    'en' => 'Already have an account?',
                    'fr' => 'Vous avez déjà un compte ?',
                ],
                'auth.register.login_link' => [
                    'en' => 'Login',
                    'fr' => 'Connexion',
                ],

                // Forgot password
                'auth.forgot.title' => [
                    'en' => 'Forgot Password',
                    'fr' => 'Mot de passe oublié',
                ],
                'auth.forgot.description' => [
                    'en' => 'Enter your email and we will send you a reset link.',
                    'fr' => 'Entrez votre e-mail et nous vous enverrons un lien de réinitialisation.',
                ],
                'auth.forgot.email' => [
                    'en' => 'Email address',
                    'fr' => 'Adresse e-mail',
                ],
                'auth.forgot.submit' => [
                    'en' => 'Send reset link',
                    'fr' => 'Envoyer le lien',
                ],
                'auth.forgot.back_to_login' => [
                    'en' => 'Back to login',
                    'fr' => 'Retour à la connexion',
                ],

                // Reset password
                'auth.reset.title' => [
                    'en' => 'Reset Password',
                    'fr' => 'Réinitialiser le mot de passe',
                ],
                'auth.reset.password' => [
                    'en' => 'New password',
                    'fr' => 'Nouveau mot de passe',
                ],
                'auth.reset.password_confirm' => [
                    'en' => 'Confirm new password',
                    'fr' => 'Confirmer le nouveau mot de passe',
                ],
                'auth.reset.submit' => [
                    'en' => 'Reset password',
                    'fr' => 'Réinitialiser',
                ],
            ],

            'profile' => [
                'profile.title' => [
                    'en' => 'Profile',
                    'fr' => 'Profil',
                ],
                'profile.edit.title' => [
                    'en' => 'Edit profile',
                    'fr' => 'Modifier le profil',
                ],
                'profile.name' => [
                    'en' => 'Name',
                    'fr' => 'Nom',
                ],
                'profile.email' => [
                    'en' => 'Email',
                    'fr' => 'E-mail',
                ],
                'profile.phone' => [
                    'en' => 'Phone',
                    'fr' => 'Téléphone',
                ],
                'profile.save' => [
                    'en' => 'Save changes',
                    'fr' => 'Enregistrer',
                ],
                'profile.cancel' => [
                    'en' => 'Cancel',
                    'fr' => 'Annuler',
                ],
            ],

            'common' => [
                'common.back' => [
                    'en' => 'Back',
                    'fr' => 'Retour',
                ],
                'common.loading' => [
                    'en' => 'Loading...',
                    'fr' => 'Chargement...',
                ],
                'common.actions' => [
                    'en' => 'Actions',
                    'fr' => 'Actions',
                ],
            ],
        ];

        // 3) Insert keys + translations
        foreach ($data as $group => $items) {
            foreach ($items as $keyString => $valuesByLang) {

                $key = TranslationKey::updateOrCreate(
                    ['key' => $keyString],
                    ['group' => $group]
                );

                foreach ($langModels as $code => $lang) {
                    $value = $valuesByLang[$code] ?? '';

                    // Safe create/update (handles re-seeding)
                    Translation::updateOrCreate(
                        [
                            'translation_key_id' => $key->id,
                            'language_id' => $lang->id,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                }
            }
        }
    }
}
