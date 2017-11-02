<?php

return [
    'notificationTemplates' => require 'notifications.php',
    'adminEmail' => 'admin@example.com',
    'mail.supportEmail' => 'info@postim.by',
    'site.hostName' => 'https://postim.local.by',

    'user.loginDuration' => 3600*24*30,
    'user.passwordResetTokenExpire' => 3600,
    'user.photoName' => 'photo.jpg',
    'user.lastVisitUpdatePeriod' => 1800,
    'user.socialAuthGeneratePasswordLength' => 15,
    'user.incrementExperience' => 10,
    'user.experienceForFirstLevel' => 100,

    'security.encryptionKey' => 'mc2447382@',
    'post.perPage' => 16,
    'mainPage.postCount' => 3,
    'mainPage.newsCount' => 3,
];
