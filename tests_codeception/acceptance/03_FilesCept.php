<?php
$I = new AcceptanceTester($scenario ?? null);

$I->wantTo('have disk page');

$I->amOnPage('/');
$I->click('Register');

$I->seeCurrentUrlEquals('/register');

$I->fillField('name', 'Jan');
$I->fillField('email', 'johndoe@gmail.com');
$I->fillField('password', 'secret333');
$I->fillField('password_confirmation', 'secret333');

$I->click('Register');


$I->seeCurrentUrlEquals('/dashboard');

$I->see('Dashboard');
$I->see('Your files');
$I->see('Upload');
$I->see('Shared');

$I->click('Your files');
$I->see('Your files', 'h2');
$I->see('No files yet!');

$I->click('Upload new file!');

$I->seeCurrentUrlEquals('/disk/upload');
$I->see('Upload', 'h2');
$I->see('Upload your file:');

$I->click('Go!');
$I->seeCurrentUrlEquals('/disk/upload');

$file1 = 'hymn_polski.mp3';
$I->attachFile('input[type="file"]', $file1);
$I->click("Go!");

$I->seeInDatabase('media', [
    'file_name' => $file1
]);

$I->seeCurrentUrlEquals("/disk");

$I->dontSee('No files yet!');
$I->see('Name');
$I->see('Size');

$file1_size = $I->grabFromDatabase('media', 'size', [
    'file_name' => $file1
]);

$I->see($file1);
$I->see($file1_size);

$I->click('Upload new file!');

$I->seeCurrentUrlEquals('/disk/upload');
$file2 = 'screen1.jpg';
$I->attachFile('input[type="file"]', $file2);
$I->click("Go!");

$I->seeInDatabase('media', [
    'file_name' => $file1
]);

$I->seeInDatabase('media', [
    'file_name' => $file2
]);

$I->click("Your files");
$I->seeCurrentUrlEquals("/disk");

$I->dontSee('No files yet!');

$file2_size = $I->grabFromDatabase('media', 'size', [
    'file_name' => $file2
]);

$I->see($file1);
$I->see($file1_size);
$I->see($file2);
$I->see($file2_size);

$I->click('Shared');
$I->seeCurrentUrlEquals('/disk/shared');
$I->see('No files yet!');

$I->amOnPage('/logout');
$I->amOnPage('/register');
$I->seeCurrentUrlEquals('/register');

$I->wantTo('login other account');

$I->fillField('name', 'Foo');
$I->fillField('email', 'foo@gmail.com');
$I->fillField('password', 'secret123');
$I->fillField('password_confirmation', 'secret123');

$I->click('Register');

$I->seeCurrentUrlEquals('/dashboard');

$I->click('Your files');

$I->see('No files yet!');

$file3 = 'readme.md';

$I->click('Upload');
$I->attachFile('input[type="file"]', $file3);
$I->click("Go!");

$I->seeInDatabase('media', [
    'file_name' => $file3
]);

$I->click('Your files');

$file3_size = $I->grabFromDatabase('media', 'size', [
    'file_name' => $file3
]);

$I->see($file3);
$I->see($file3_size);

$I->see('Download');

$I->click('Delete');
$I->see('No files yet!');
$I->dontSee($file3);
$I->dontSee($file3_size);
$I->dontSeeInDatabase('media', [
    'file_name' => $file3
]);

$I->click('Upload');
$I->attachFile('input[type="file"]', $file3);
$I->click("Go!");

$I->click("Share");

$id3 = $I->grabFromDatabase('media', 'id', [
    'file_name' => $file3
]);

$I->seeCurrentUrlEquals('/disk/'.$id3.'/share');
$I->see('Share', 'h2');
$I->see('User email:');

$I->fillField('email', 'zlymail@gmail.com');
$I->click('Share');
$I->seeCurrentUrlEquals('/disk/'.$id3.'/share');
$I->see('Share', 'h2');
$I->see('User email:');

$I->fillField('email', 'johndoe@gmail.com');
$I->click('Share');

$I->seeInDatabase('users', [
    'email' => 'johndoe@gmail.com'
]);

$I->amOnPage('/logout');
$I->amOnPage('/register');
$I->seeCurrentUrlEquals('/register');

$I->wantTo('check shared file');

$I->amOnPage('/disk');

$I->seeCurrentUrlEquals('/login');

$I->fillField('email', 'johndoe@gmail.com');
$I->fillField('password', 'secret333');

$I->click('Login');

$email = 'foo@gmail.com';

$I->seeCurrentUrlEquals('/disk');

$I->click('Shared');
$I->seeCurrentUrlEquals('/disk/shared');

$I->click('Notifications');
$I->seeCurrentUrlEquals('/disk/notifications');
$I->dontSee('No files yet!');
$I->see('User with email: ' . $email . ' shared you a file!');
$I->see('File name: '.$file3);

$I->click('Shared');
$I->seeCurrentUrlEquals('/disk/shared');
$I->dontSee('No files yet!');
$I->see($file3);
$I->see($file3_size);

$I->click('Delete');
$I->see('No files yet!');

$I->seeInDatabase('media', [
    'file_name' => $file3
]);

$I->click('Your files');
$I->dontSee('No files yet!');
$I->see($file1);
$I->see($file1_size);
$I->see($file2);
$I->see($file2_size);

$I->click('Share download link');

$id1 = $I->grabFromDatabase('media', 'id', [
    'file_name' => $file1
]);

$I->seeCurrentUrlEquals('/disk/'.$id1.'/link');
$I->see('Download link', 'h2');
$I->see('Here is your download link!');
$I->see('Copy and share:');
