<?php

$I = new AcceptanceTester($scenario ?? null);
$I->wantTo('login with existing user');

$I->amOnPage('/dashboard');

$I->seeCurrentUrlEquals('/login');

$I->fillField('email', 'john.doe@gmail.com');
$I->fillField('password', 'secret');

$I->click('Login');

$I->seeCurrentUrlEquals('/dashboard');

$I->see('John Doe');
$I->see("You're logged in!");
