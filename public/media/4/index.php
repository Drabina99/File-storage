<?php
echo "Hello world!";
echo("This works too.");
echo "Many", "Items";
$a = 10;
$b = 20;

function foo() {

    global $a, $b;

    return $a + $b;
}

$c=foo();
echo 'wynik = '.$c.' tyle';

print "Hello World!";
echo '<br/>';
print("This works too.");
echo '<br/>';
$count = 10;
$greeting = "Hello";
$interpolated = "$greeting $count times!";
echo '<br/>';
echo 'zmienna '.$interpolated.' dziala<br/>';
$name = "count";
echo '<br/>'.$name;
$result = $$name;
echo '<br/>'.$result.' '.$$name;
$greetingReference = &$greeting;
echo '<br/>'.$gretingReference;
$greetingReference = "Welcome";
echo '<br/>'.$gretingReference;
$greeting;
echo '<br/>'.$greting;


$i = 10;
$i = $i + 10;
echo "Result: <strong>$i</strong>";
