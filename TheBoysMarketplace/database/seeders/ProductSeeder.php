<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            // ELECTRONICS (category_id = 1)
            [
                'name'        => 'Gaming Headset',
                'price'       => 350000,
                'image'       => 'images/headset.jpg',
                'description' => 'Model: TB-HS01. High quality gaming headset with 7.1 surround sound, detachable mic, and soft ear cushions. Made in: Indonesia. Color: Black / Red.',
                'quantity'    => 10,
                'category_id' => 1,
            ],
            [
                'name'        => 'Mechanical Keyboard',
                'price'       => 550000,
                'image'       => 'images/keyboard.jpg',
                'description' => 'Model: TB-MK87. TKL RGB mechanical keyboard with hot-swappable blue switches and braided USB-C cable. Made in: China. Color: Black.',
                'quantity'    => 7,
                'category_id' => 1,
            ],
            [
                'name'        => '24\" 144Hz Gaming Monitor',
                'price'       => 2100000,
                'image'       => 'images/monitor.jpg',
                'description' => 'Model: TB-GM24. 24-inch 144Hz IPS gaming monitor with 1ms response time and FreeSync support. Made in: Vietnam. Color: Black.',
                'quantity'    => 5,
                'category_id' => 1,
            ],
            [
                'name'        => 'Wireless Gaming Mouse',
                'price'       => 250000,
                'image'       => 'images/mouse.jpg',
                'description' => 'Model: TB-WM01. Lightweight wireless gaming mouse with adjustable DPI (800–16000) and USB receiver. Made in: China. Color: Black.',
                'quantity'    => 15,
                'category_id' => 1,
            ],

            // TOYS (category_id = 2)
            [
                'name'        => 'Action Figure – Hero (Blue Suit)',
                'price'       => 150000,
                'image'       => 'images/action_figure_blue.jpg',
                'description' => 'Model: TB-AF01. Collectible 15cm articulated hero action figure with display stand. Made in: China. Color: Blue suit.',
                'quantity'    => 12,
                'category_id' => 2,
            ],
            [
                'name'        => 'Action Figure – Hero (Red Suit)',
                'price'       => 150000,
                'image'       => 'images/action_figure_red.jpg',
                'description' => 'Model: TB-AF01-R. Same hero model with alternate colorway. Made in: China. Color: Red suit.',
                'quantity'    => 8,
                'category_id' => 2,
            ],

            // BOOKS (category_id = 3)
            [
                'name'        => 'Novel – The Night Watch',
                'price'       => 120000,
                'image'       => 'images/book_night_watch.jpg',
                'description' => 'Model: TB-BK01. Paperback novel, 420 pages. Genre: Fantasy / Thriller. Printed in: Indonesia. Cover color: Dark blue.',
                'quantity'    => 20,
                'category_id' => 3,
            ],
            [
                'name'        => 'Comic – The Boys Vol. 1',
                'price'       => 90000,
                'image'       => 'images/comic_the_boys_v1.jpg',
                'description' => 'Model: TB-CM01. Full-color comic, first volume of The Boys series. Printed in: Malaysia. Cover color: Black / Red.',
                'quantity'    => 18,
                'category_id' => 3,
            ],

            // CLOTHES (category_id = 4)
            [
                'name'        => 'Graphic T-Shirt – The Boys (Black)',
                'price'       => 175000,
                'image'       => 'images/tshirt_black.jpg',
                'description' => 'Model: TB-TS01. Unisex cotton graphic T-shirt with The Boys logo. Made in: Indonesia. Color: Black. Sizes: S–XL.',
                'quantity'    => 25,
                'category_id' => 4,
            ],
            [
                'name'        => 'Graphic T-Shirt – The Boys (White)',
                'price'       => 175000,
                'image'       => 'images/tshirt_white.jpg',
                'description' => 'Model: TB-TS01-W. Same model as TB-TS01 with inverted colorway. Made in: Indonesia. Color: White. Sizes: S–XL.',
                'quantity'    => 20,
                'category_id' => 4,
            ],
            [
                'name'        => 'Hoodie – The Boys Logo',
                'price'       => 320000,
                'image'       => 'images/hoodie_logo.jpg',
                'description' => 'Model: TB-HD01. Fleece hoodie with front pocket and embroidered The Boys logo. Made in: Bangladesh. Color: Charcoal Grey.',
                'quantity'    => 10,
                'category_id' => 4,
            ],

            // KITCHENWARE (category_id = 5)
            [
                'name'        => 'Chef Knife 8\"',
                'price'       => 210000,
                'image'       => 'images/chef_knife.jpg',
                'description' => 'Model: TB-KN08. 8-inch stainless steel chef knife with full-tang handle. Made in: Japan. Color: Silver blade / Black handle.',
                'quantity'    => 14,
                'category_id' => 5,
            ],
            [
                'name'        => 'Non-Stick Frying Pan 28cm',
                'price'       => 260000,
                'image'       => 'images/frying_pan.jpg',
                'description' => 'Model: TB-PN28. 28cm non-stick frying pan suitable for gas and induction stoves. Made in: Thailand. Color: Dark grey body / Black handle.',
                'quantity'    => 9,
                'category_id' => 5,
            ],

            // FOOD (category_id = 6)
            [
                'name'        => 'Spicy Instant Ramen 5-Pack',
                'price'       => 55000,
                'image'       => 'images/instant_ramen.jpg',
                'description' => 'Model: TB-RM05. Pack of 5 spicy instant ramen servings. Made in: Korea. Flavor: Extra Spicy. Color: Red / Black packaging.',
                'quantity'    => 40,
                'category_id' => 6,
            ],
            [
                'name'        => 'Premium Coffee Beans 500g',
                'price'       => 130000,
                'image'       => 'images/coffee_beans.jpg',
                'description' => 'Model: TB-CF500. 100% Arabica whole beans, medium roast. Origin: Indonesia (Sumatra). Packaging color: Brown / Gold.',
                'quantity'    => 22,
                'category_id' => 6,
            ],
        ]);
    }
}