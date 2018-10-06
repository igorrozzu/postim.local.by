<?php


$params = [
    'notificationTemplates' => require 'notifications.php',
    'premiumAccount' => require 'premiumAccount.php',

    'adminEmail' => 'admin@example.com',
    'mail.supportEmail' => 'info@postim.by',
    'mail.feedbackEmail' => 'feedback@postim.by',
    'mail.askEmail' => 'ask@postim.by',
    'site.hostName' => 'https://postim.local.by',

    'auth.google.returnUrl' => 'https://postim.local.by/social-auth/auth?authclient=google',

    'user.loginDuration' => 3600*24*30,
    'user.passwordResetTokenExpire' => 3600,
    'user.photoName' => 'photo.jpg',
    'user.lastVisitUpdatePeriod' => 1800,
    'user.socialAuthGeneratePasswordLength' => 15,
    'user.incrementExperience' => 10,
    'user.experienceForFirstLevel' => 100,

    'security.encryptionKey' => 'mc2447382@',

    'post.perPage' => 16,
    'post.recommendedPostsMaxCount' => 2,

    'mainPage.postCount' => 4,
    'mainPage.newsCount' => 4,
];

if(YII_ENV == 'prod'){
    $params['site.hostName'] = 'https://postim.by';
    $params['auth.google.returnUrl'] = 'https://postim.by/social-auth/auth?authclient=google';
}

if(YII_ENV == 'testing'){
    $params['site.hostName'] = 'https://postim.testing.by';
    $params['auth.google.returnUrl'] = 'https://postim.testing.by/social-auth/auth?authclient=google';
}

return $params;
