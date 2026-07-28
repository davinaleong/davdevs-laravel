<?php

namespace Database\Seeders;

use App\Models\Quip;
use Illuminate\Database\Seeder;

class QuipSeeder extends Seeder
{
    public function run(): void
    {
        $quips = [
            // FOOD FOR THOUGHT
            ['variant' => 'statement', 'question' => null, 'punchline' => "When you're on a keto diet, you gotta lock down your intake."],
            ['variant' => 'qa', 'question' => 'What does Santa like to have for breakfast?', 'punchline' => 'Oh, oh, oats.'],
            ['variant' => 'qa', 'question' => 'What hawker dish would you consume for youthfulness?', 'punchline' => 'Young tau foo.'],
            ['variant' => 'qa', 'question' => 'What hawker dish changes its meaning with one letter?', 'punchline' => 'Yong tau fool.'],
            ['variant' => 'qa', 'question' => 'What food can be found in a toolbox?', 'punchline' => 'A ham-mer.'],
            ['variant' => 'qa', 'question' => 'What is a veggie comedian called?', 'punchline' => 'A corn-median.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'I was having corn when I corn-sidered the corn I ate: a delicious, sweet pearl corn.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'I mang-went to eat the mangoes, and they became mang-nos.'],
            ['variant' => 'qa', 'question' => 'What is a free coffee called?', 'punchline' => 'A cof-free.'],
            ['variant' => 'qa', 'question' => 'What fruit is used to make soap?', 'punchline' => 'Soursoap.'],
            ['variant' => 'qa', 'question' => 'What fruit is associated with gravity?', 'punchline' => 'An apple.'],
            ['variant' => 'qa', 'question' => 'What fruit comes in twos?', 'punchline' => 'Pears.'],
            ['variant' => 'qa', 'question' => 'What berry is always up to date with the latest news?', 'punchline' => 'Blackcurrant.'],
            ['variant' => 'qa', 'question' => 'What type of room is full of candy?', 'punchline' => 'Suites.'],
            ['variant' => 'qa', 'question' => 'What kind of seed can open doors magically?', 'punchline' => 'A sesame seed.'],
            ['variant' => 'qa', 'question' => 'What kind of food barks?', 'punchline' => 'A hot dog.'],
            ['variant' => 'qa', 'question' => 'What pastry has art in it?', 'punchline' => 'Tart.'],
            ['variant' => 'qa', 'question' => 'Why do bodybuilders need to eat a lot of shellfish?', 'punchline' => 'To build mussels.'],
            ['variant' => 'qa', 'question' => 'What room is always squished?', 'punchline' => 'Mushroom.'],
            ['variant' => 'qa', 'question' => 'What dish pairs well with kayaking?', 'punchline' => 'A slice of crispy kaya toast with kopi.'],

            // EVERYDAY WORDPLAY
            ['variant' => 'statement', 'question' => null, 'punchline' => "At the checkpoint, we held a port, which we passed to the person at the port for them to check our passport. Now it's passed and in the past."],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'The case of finding a new case for my phone, in case it drops, was closed in Nov 2022.'],
            ['variant' => 'qa', 'question' => 'What vehicle carries business?', 'punchline' => 'A bus.'],
            ['variant' => 'qa', 'question' => 'What did the bread say to the compass when it was travelling?', 'punchline' => "I'm heading yeast."],
            ['variant' => 'qa', 'question' => 'What did the eagle say after a hard workout?', 'punchline' => "I'm so soar."],
            ['variant' => 'qa', 'question' => 'What is a scaly ceramic?', 'punchline' => 'A reptile.'],
            ['variant' => 'qa', 'question' => "What's the royalty of the tissues?", 'punchline' => 'A napking.'],
            ['variant' => 'qa', 'question' => 'What candy or dessert is never on time?', 'punchline' => 'Chocolate.'],
            ['variant' => 'qa', 'question' => 'What type of rest will make you restless?', 'punchline' => 'Arrest.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => "I'm sure you'd like to miss a mistake, but would you miss a good steak?"],
            ['variant' => 'qa', 'question' => 'What do trees and work have in common?', 'punchline' => 'They both have leaves.'],
            ['variant' => 'qa', 'question' => 'How do you make yourself know a piece of info that was new to you?', 'punchline' => 'Put a "k" in front of it, and now you\'ll know—and later knew—what was initially new to you.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'We watched a move-ie yesterday. When it ended, it became a stoppie.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'Before 31 Oct, it was Halloween. After 31 Oct, it was Byeween.'],
            ['variant' => 'qa', 'question' => 'What type of board game do drillers like to play?', 'punchline' => 'A boring game.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'When a hi-tea ends, it becomes bye-tea.'],
            ['variant' => 'qa', 'question' => 'What exercise involves cards?', 'punchline' => 'Cardio.'],
            ['variant' => 'qa', 'question' => 'What kind of air can you walk on and is good for exercise?', 'punchline' => 'Stairs.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'A black duck is simply a dark duck.'],
            ['variant' => 'qa', 'question' => 'Why do we always need to buy a new dress when we go to a location?', 'punchline' => 'Because you have to ad-dress.'],

            // FRIENDS, GATHERINGS, AND LITTLE MOMENTS
            ['variant' => 'statement', 'question' => null, 'punchline' => "A friend texted me that she attended her friend's wedding. But it happened on a Sat, so was it a Wedding or a Sat-ding?"],
            ['variant' => 'statement', 'question' => null, 'punchline' => "If she's not seated at the moment, then she should sit-ding while waiting for the couple to arrive."],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'Later that evening, she had to stand to give her soon-to-be-married friend a stand-ding ovation.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'A group of friends gathered for a party. One friend came late to the party. Another friend came after that. Is this person then late or line? (late = l8; line = l9)'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'A group of people gathered around the table for dinner. The spread consisted of fried chicken wings, roast duck legs, pork loins, lamb racks, beef steaks, and fish skewers. You could say that they had a meeting of meat-things.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'I was on my way to church when my friend texted the seat numbers in the group chat: "CUXX-CUYY." Then another friend texted, "C u!" So, I replied, "We are cued to c u at CU row."'],
            ['variant' => 'statement', 'question' => null, 'punchline' => 'I met up for a meal with my best friend, and we were going to see each other again the next day for another function. She said to me, "See you tomorrow." I replied, "See you tomorrow—row, row your boat gently down the stream."'],

            // LOCAL FLAVOUR
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore is before "D"?', 'punchline' => 'Cold Storage.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore can be found in the ocean?', 'punchline' => 'NTU-Sea.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore can be found in the fridge?', 'punchline' => 'Cold Storage.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore is very "siong"?', 'punchline' => 'Sheng Siong.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore is the opposite of hot?', 'punchline' => 'Cold Storage.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore is another word for carnival?', 'punchline' => 'Fair Price.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore has a local university in it?', 'punchline' => 'NTU-C.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore is anti everyone looking at it?', 'punchline' => 'NTUC.'],
            ['variant' => 'qa', 'question' => 'Which supermarket in Singapore is the largest supermarket?', 'punchline' => 'Giant.'],
            ['variant' => 'statement', 'question' => null, 'punchline' => "Earl Grey: plain tea. Earl White: plain tea with milk. Earl Black: gao plain tea."],
            ['variant' => 'qa', 'question' => 'What public transport is always on fire?', 'punchline' => 'A bus with many people alighting.'],

            // ANIMALS AND NATURE
            ['variant' => 'qa', 'question' => 'What do you call a pig doing karate?', 'punchline' => 'Pork chop.'],
            ['variant' => 'qa', 'question' => 'Where do crows like to gather after a long day?', 'punchline' => 'At a crow-bar.'],
            ['variant' => 'qa', 'question' => 'Why was the yak paddling across the water?', 'punchline' => 'Because it was kayaking.'],
            ['variant' => 'qa', 'question' => 'What do you call a reptilian AI?', 'punchline' => 'An algogator.'],
            ['variant' => 'qa', 'question' => 'What do trees and dogs have in common?', 'punchline' => 'They both have bark.'],

            // MINI SONG PARODY
            ['variant' => 'statement', 'question' => null, 'punchline' => "Under the tree,\nUnder the tree,\nWhere Isaac was hit by an apple,\nAnd he discovered gravity.\n\nUnder the tree,\nUnder the tree,\nWe are searching,\nHoping to find free durians.\n\n(Inspired by \"Under the Sea\" from The Little Mermaid)"],
        ];

        foreach ($quips as $quip) {
            Quip::create(array_merge($quip, ['active' => true]));
        }
    }
}
