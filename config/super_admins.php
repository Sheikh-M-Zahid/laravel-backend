<?php

// The 3 "founding" Super Admin emails.
//
// Anyone who logs in (or completes registration) with one of these emails
// is automatically made a Super Admin + Admin -- see the bootstrap check in
// AuthController::login() and AuthController::verifyEmail().
//
// ONLY these 3 people can nominate/approve a NEW Super Admin (all 3 must
// approve before a nomination takes effect -- see SuperAdminController).
// Anyone else (including a Super Admin promoted later) can only *suggest* a
// nominee to these 3; they can't approve one themselves.
//
// >>> To change these 3 people in the future, edit the array below. <<<
return [
    'emails' => [
        'hasan15marjan@gmail.com',
        'zsheikhmohammad@gmail.com',
        'marzia14hasan@gmail.com',
    ],
];
