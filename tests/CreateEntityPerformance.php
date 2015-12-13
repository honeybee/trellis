#!/usr/bin/env php
<?php

use Trellis\Tests\Runtime\Fixtures\ArticleType;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// same order as in ArticleType definition!
$data = [
    'uuid' => '7e185d43-f870-46e7-9cea-59800555e970',
    'headline' => 'this is a short headline',
    'content' => "Like you, I used to think the world was this great place where everybody lived by the same standards I did, then some kid with a nail showed me I was living in his world, a world where chaos rules not order, a world where righteousness is not rewarded. That's Cesar's world, and if you're not willing to play by his rules, then you're gonna have to pay the price.

    Well, the way they make shows is, they make one show. That show's called a pilot. Then they show that show to the people who make shows, and on the strength of that one show they decide if they're going to make more shows. Some pilots get picked and become television programs. Some don't, become nothing. She starred in one of the ones that became nothing.
",
    'click_count' => 123,
    'float' => 123.456,
    'coords' => [ 'lon' => 123.456, 'lat' => 52.34 ],
    'author' => 'Samuel L Ipsum',
    'email' => 'samuel.l.ipsum@example.com',
    'website' => 'http://slipsum.com/lite/',
    'birthday' => '2014-12-31T12:34:56.123456+01:00',
    'images' => [ 1, 2, 3, 4, 5, 6 ],
    'keywords' => [ 'some', 'keywords' ],
    'enabled' => true,
    'content_objects' => [
        [
            '@type' => 'paragraph',
            'title' => 'hello world!',
            'text' => " You see? It's curious. Ted did figure it out - time travel. And when we get back, we gonna tell everyone. How it's possible, how it's done, what the dangers are. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an 'unknown entry event'? Why don't they know? If they don't know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive logic.
",
            'coords' => [ 'lon' => 12.34, 'lat' => 56.78 ]
        ],
        [
            '@type' => 'paragraph',
            'title' => 'hello world again!',
            'text' => " Now that we know who you are, I know who I am. I'm not a mistake! It all makes sense! In a comic, you know how you can tell who the arch-villain's going to be? He's the exact opposite of the hero. And most times they're friends, like you and me! I should've known way back when... You know why, David? Because of the kids. They called me Mr Glass. ",
            'coords' => [ 'lon' => 23.45, 'lat' => 56.78 ]
        ]
    ],
    'categories'=> [
        [
            '@type' => 'referenced_category',
            'identifier' => '1023abf5-f870-46e7-9cea-5980055a523b',
            'referenced_identifier' => 'some-category'
        ]
    ],
    'meta' => [
        'key' => 'value'
    ],
    'workflow_state' => []
];


$times = [];

$num = 10;
$repeats = 1000;

$article_type = new ArticleType();
for ($t = 0; $t < $num; $t++) {
    $start = microtime(true);
    for ($i = 0; $i < $repeats; $i++) {
        $article = $article_type->createEntity($data);
    }
    $end = microtime(true);
    $times[$t] = round(($end - $start) * 1000, 3);
    echo $times[$t] . 'ms ';
}

$sum = 0;
for ($i = 0; $i < $num; $i++) {
    $sum += $times[$i];
}

echo PHP_EOL . 'Average time to create ' . $repeats . ' articles: ' . round($sum / $num, 3) . PHP_EOL;

// default: avg=4692ms for 1000 articles, 4668ms, 4709ms
// default w/ bool cast in toBoolean of Rule: avg=4503ms for 1000 articles, 4491ms
// default w/o toBoolean: avg=4355ms for 1000 articles, 4348ms
// default w/o toBoolean and w/ simple trim(): avg=4323ms for 1000 articles, 4356ms
//
// ~ 7% improvement for 1000 articles when not using toBoolean for getOption()
//
// default w/o toBoolean in TextRule: avg=4401ms for 1000 articles, 4395
// default w/o toBoolean and w/o strip_ctrl_chars in TextRule: avg=4034ms for 1000 articles, 4061
// default w/ trim instead of regexp in TextRule: avg=4457ms for 1000 articles, 4430, 4476
// default w/ trim and w/o toBoolean in TextRule: avg=4229ms for 1000 articles
// default w/ bool cast: avg=4247ms for 1000 articles
// bool cast instead of filter_var and is_string validation: avg=3240ms for 1000 articles, 3174
//
