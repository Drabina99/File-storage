<?php

$I = new AcceptanceTester($scenario ?? null);
$I->wantTo('see Laravel links on homepage');

$I->amOnPage('/');

$I->seeInTitle('Laravel');

$I->seeLink("Documentation", "https://laravel.com/docs");
$I->seeLink("Laracasts", "https://laracasts.com");
$I->seeLink("Forge", "https://forge.laravel.com");
