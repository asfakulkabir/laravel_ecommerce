<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetaPixelTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_layout_initializes_pixel_and_does_not_pass_test_code_as_user_data(): void
    {
        Setting::setValue('meta_pixel_id', '123456789');
        Setting::setValue('meta_test_event_code', 'TEST123');

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('fbq(\'init\', "123456789");', false)
            ->assertSee("fbq('track', 'PageView');", false)
            ->assertDontSee('TEST123', false);
    }
}
