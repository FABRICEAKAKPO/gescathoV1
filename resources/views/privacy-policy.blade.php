@extends('layout')

@section('title', 'Politique de Confidentialité')

@section('content')
<div class="flex-1 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Politique de Confidentialité</h1>

        <p class="text-gray-600 mb-6">
            <strong>Dernière mise à jour :</strong> {{ now()->format('d F Y') }}
        </p>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Introduction</h2>
            <p class="text-gray-700 leading-relaxed">
                Nous sommes engagés à protéger votre vie privée. Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos données personnelles.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Données que nous collectons</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Nous pouvons collecter les informations suivantes :
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                <li>Nom et adresse e-mail</li>
                <li>Informations de profil utilisateur</li>
                <li>Données de demandes et transactions</li>
                <li>Données de navigation et d'utilisation du site</li>
                <li>Adresse IP et informations de navigateur</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">3. Utilisation de vos données</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Nous utilisons vos données pour :
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                <li>Fournir et améliorer nos services</li>
                <li>Gérer votre compte utilisateur</li>
                <li>Traiter les demandes et transactions</li>
                <li>Envoyer des notifications importantes</li>
                <li>Analyser les statistiques d'utilisation</li>
                <li>Respecter nos obligations légales</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">4. Partage de données</h2>
            <p class="text-gray-700 leading-relaxed">
                Nous ne partageons pas vos données personnelles avec des tiers, sauf si :
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                <li>Vous avez donné votre consentement explicite</li>
                <li>Nous sommes obligés par la loi</li>
                <li>Cela est nécessaire pour fournir le service</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Sécurité des données</h2>
            <p class="text-gray-700 leading-relaxed">
                Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données personnelles contre l'accès, la modification et la divulgation non autorisés. Cependant, aucune méthode de transmission sur Internet n'est entièrement sécurisée.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Vos droits</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Vous avez le droit de :
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                <li>Accéder à vos données personnelles</li>
                <li>Corriger vos données inexactes</li>
                <li>Supprimer vos données (droit à l'oubli)</li>
                <li>Retirer votre consentement</li>
                <li>Obtenir une copie de vos données</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Cookies</h2>
            <p class="text-gray-700 leading-relaxed">
                Notre site utilise des cookies pour améliorer votre expérience utilisateur. Les cookies sont de petits fichiers stockés sur votre appareil. Vous pouvez contrôler les paramètres des cookies via votre navigateur.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Durée de conservation</h2>
            <p class="text-gray-700 leading-relaxed">
                Nous conservons vos données personnelles aussi longtemps que nécessaire pour fournir nos services ou respecter nos obligations légales. Vous pouvez demander la suppression de vos données à tout moment.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Modifications de cette politique</h2>
            <p class="text-gray-700 leading-relaxed">
                Nous pouvons mettre à jour cette politique de confidentialité de temps en temps. Les modifications seront publiées sur cette page avec la date de dernière mise à jour.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Nous contacter</h2>
            <p class="text-gray-700 leading-relaxed">
                Si vous avez des questions concernant cette politique de confidentialité ou nos pratiques en matière de protection des données, veuillez nous contacter :
            </p>
            <div class="mt-4 p-4 bg-gray-100 rounded">
                <p class="text-gray-700"><strong>Email :</strong> fabriceakakpo786@gmail.com</p>
                <a href="https://wa.me/22892877153" class="text-red-600 hover:text-red-800 font-medium">
                <p class="text-gray-700"><strong>Téléphone :</strong> +228 92 87 71 53</p>
                </a>
            </div>
        </section>

        <div class="mt-12 pt-8 border-t border-gray-300">
            <a href="{{ route('dashboard') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Retour
            </a>
        </div>
    </div>
</div>
@endsection
