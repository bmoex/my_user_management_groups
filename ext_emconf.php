<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'My Backend User Management: Extend groups selection',
    'description' => 'Extend module with user override of groups selection',
    'category' => 'module',
    'version' => '1.0.0',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'constraints' => [
        'depends' => [
            'my_user_management' => '3.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Serfhos\\MyUserManagementGroups\\' => 'Classes'
        ]
    ],
];
