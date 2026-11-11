<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicNavigationTest extends TestCase
{
    public function test_company_profile_details_and_instagram_link_are_configured(): void
    {
        $profile = config('profile');

        $this->assertSame('Jl. Tawes No. 38, Iringmulyo, Metro Timur, Kota Metro, Lampung', $profile['address']);
        $this->assertSame('@innes_arsen_wisata', $profile['instagram_handle']);
        $this->assertSame('https://www.instagram.com/innes_arsen_wisata/', $profile['instagram_url']);
        $this->assertCount(4, $profile['missions']);
        $this->assertCount(6, $profile['services']);
    }

    public function test_default_login_url_opens_the_login_form(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk ke akun Anda.')
            ->assertSee(route('login.store'), false);
    }

    public function test_editorial_navigation_and_article_pages_are_available(): void
    {
        $this->get(route('editorial.explore'))->assertOk()->assertSee('Jelajah alam dan petualangan Indonesia');
        $this->get(route('editorial.culture'))->assertOk()->assertSee('Cerita budaya dan sejarah Nusantara');
        $this->get(route('editorial.culinary'))->assertOk()->assertSee('Menjelajah rasa khas daerah');
        $this->get(route('editorial.culinary'))
            ->assertSee('photo-1519708227418-c8fd9a32b7a2')
            ->assertSee('Hidangan laut untuk kuliner pesisir');
        $this->get(route('editorial.advertorial'))->assertOk()->assertSee('Rekomendasi perjalanan bersama Innes Arsen Wisata');
        $this->get(route('editorial.news'))->assertOk()->assertSee('Kabar dan informasi perjalanan');
        $this->get(route('editorial.show', 'menikmati-fajar-di-dieng'))
            ->assertOk()
            ->assertSee('Menikmati fajar dan udara sejuk di dataran tinggi Dieng');
    }
}
